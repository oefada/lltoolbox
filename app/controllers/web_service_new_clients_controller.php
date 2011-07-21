<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');

// FOR DEV WEB SERVICE SETTINGS! VERY IMPORTANT FOR DEV
define('DEV_USER_TOOLBOX_HOST', 'http://' . $_SERVER['ENV_USER'] . '-toolboxdev.luxurylink.com/web_service_tickets');

class WebServiceNewClientsController extends WebServicesController
{
	var $name = 'WebServiceNewClients';
	var $uses = array('Client','ClientContact');
	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_new_clients';

	// IF DEV, please make sure you use this path, or if using your own dev, then change this var
	var $serviceUrlDev = DEV_USER_TOOLBOX_HOST;

	var $errorResponse = false;
	var $api = array(
					'save_client' => array(
						'doc' => 'Save new client or update record from Sugar',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function beforeFilter() { $this->LdapAuth->allow('*'); }

	// main function to update or insert client records
	function save_client($sm_request)
	{
	    $response_value = '';
	    $sm_sproc_response = array();

	    // JOSN decoded the request into an assoc. array
	    $decoded_request = json_decode($sm_request, true);

	    // look for a client id but no error check
	    $client_id = trim($decoded_request['client']['client_id']);

	    // respond with DB operation mode
	    if (!$client_id || empty($client_id)) {
	        $response_value = '1';  // client insert will occur
	    } elseif (is_numeric($client_id) && $client_id > 0) {
	        $response_value = '2';  // client update will occur
	    } else {
	        $response_value = '-1';
	    }

		$date_now = date('Y-m-d H:i:s', strtotime('now'));

		if ($client_id) {
			$client = $this->Client->findByClientId($client_id);
			$client_data_save = $client['Client'];
            if (!empty($client_data_save['sites']) && is_array($client_data_save['sites'])) {
                $client_data_save['sites'] = implode(',', $client_data_save['sites']);
            }
		}
		else {
			$client_data_save = array();
			$client_data_save['sites'] = 'luxurylink';
		}

		// map data from Sugar to toolbox client table structure
		//$client_data_save = array();
        $client_data_save['name']				= str_replace('&#039;', "'", $decoded_request['client']['client_name']);
		$client_data_save['name']				= $this->utf8dec($client_data_save['name']);
        $client_data_save['managerUsername'] 	= $decoded_request['client']['manager_ini'];
		$client_data_save['teamName']			= $decoded_request['client']['team_name'];
        $client_data_save['modified']			= $date_now;
        $client_data_save['seoName']			= $this->convertToSeoName($client_data_save['name']);

		// unbind assoc
		$this->Client->bindOnly(array(), false);

		if ($client_id && is_numeric($client_id)) {
			// ======= EXISTING CLIENT UPDATE ========

			// *** check first by doing a manual update ***
			$result = $this->Client->query("UPDATE client SET modified = NOW() WHERE clientId = $client_id LIMIT 1");
			if ($this->Client->getAffectedRows()) {
        		$client_data_save['clientId'] = $client_id;
				$client_cake_save = array();
				$client_cake_save['Client'] = $client_data_save;
	        	if (!$this->Client->save($client_cake_save, array('callbacks' => false))) {
					@mail('devmail@luxurylink.com', 'SUGAR BUS -- EXISTING CLIENT NOT SAVED', print_r($client_data_save, true) . print_r($this->Client->validationErrors, true) . print_r($decoded_request, true) . print_r($this->Client->validationErrors, true));
				}
	        	$decoded_request['client']['client_id'] = $client_id;
			} else {
				// the client id was invalid so send devmail, and do nothing
				@mail('devmail@luxurylink.com', 'SUGAR BUS [CLIENT] -- INVALID CLIENTID', print_r($client_data_save, true) . print_r($decoded_request, true));
				return false;
			}
		} else {
			// ======= NEW CLIENT INSERT =============
			$next_auto_inc_result = $this->Client->query("SHOW TABLE STATUS WHERE Name = 'client'");
			$next_client_auto_id = $next_auto_inc_result[0]['TABLES']['Auto_increment'];

			$client_data_save['inactive'] 				= 1; // set new clients from sugar to inactive
			$client_data_save['created'] 				= $date_now;
			$client_data_save['oldProductId']			= "0-$next_client_auto_id";

			$this->Client->create();
			if (!$this->Client->save($client_data_save, array('callbacks' => false))) {
				@mail('devmail@luxurylink.com', 'SUGAR BUS -- NEW CLIENT NOT SAVED ('.$client_id.')', print_r($client_data_save, true) . print_r($this->Client->validationErrors, true) . print_r($decoded_request, true) . print_r($this->Client->validationErrors, true));
			}

			// get new client id and send back to Sugar
			$client_id = $decoded_request['client']['client_id'] = $this->Client->getLastInsertId();

		}

	    $decoded_request['request']['response'] = $response_value;
	    $decoded_request['request']['response_time'] = time();

	    $encoded_response = json_encode($decoded_request);

		// send info back to sugar -- should only go back to Sugar webservice on new clients so we can give Sugar back new client id.
		// look in Sugar : /var/www/html/soap/SoapSugarUsers.php for the web service 'update_client'
		// look in Sugar : /var/www/html/custom/modules/Accounts/SiteManagerAgent.php for the after hook Sugar logic.
	    $this->sendToSugar($encoded_response);

	    // update client contacts
	    // ----------------------------------------------------------------------
	    // clientContactTypeId = 1 is reservation SUGAR -> (AUC, CCALL, ALL_AUC)
	    // clientContactTypeId = 2 is homepage notification SUGAR ->(MKT, CCALL, ALL, ALL_AUC)

	  	$reservationContacts = array('AUC', 'ALL_AUC', 'ALL.AUC');
	  	$homepageContacts = array('MKT', 'MKT_ALL', 'ALL');
		$reservationCopy = array('ALL');
		$deleteContacts = array('NLT');

	    if ($client_id) {
		    $contacts = $decoded_request['contacts'];
			if (!empty($contacts)) {
				$this->ClientContact->query("DELETE FROM clientContact WHERE clientId = $client_id");
			}
		    foreach ($contacts as $k => $contact) {
		    	$contact_id = $contact['contact_id'];
		    	$recipient_type	 = $contact['recipient_type_c'];

		    	if (empty($recipient_type)) {
					continue;
		    	}

				if (in_array($recipient_type, $deleteContacts)) {
					continue;
				}

				if (empty($contact['email_address'])) {
					continue;
				}

		    	$newClientContact = array();
		    	$newClientContact['clientId']				= $client_id;
		    	$newClientContact['primaryContact']			= $contact['primary_c'];
		    	$newClientContact['name']					= $contact['contact_name'];
		    	$newClientContact['emailAddress']			= $contact['email_address'];
		    	$newClientContact['phone']					= $contact['phone_work'];
		    	$newClientContact['fax']					= $contact['phone_fax'];
		    	$newClientContact['sugarContactId']			= $contact_id;

		    	if (in_array($recipient_type, $reservationContacts)) {
		    		$newClientContact['clientContactTypeId'] = 1;
		    		$this->ClientContact->create();
		    		$this->ClientContact->save($newClientContact, array('callbacks'=>false));
		    	}
		    	if (in_array($recipient_type, $homepageContacts)) {
		    		$newClientContact['clientContactTypeId'] = 2;
		    		$this->ClientContact->create();
		    		$this->ClientContact->save($newClientContact, array('callbacks'=>false));
		    	}
		    	if (in_array($recipient_type, $reservationCopy)) {
		    		$newClientContact['clientContactTypeId'] = 3;
		    		$this->ClientContact->create();
		    		$this->ClientContact->save($newClientContact, array('callbacks'=>false));
		    	}
		    }
		}

	    // this tests to see if we were getting correct response from the request
	    return $encoded_response;
	}

	function utf8dec ( $s_String ) {
		$s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
		return substr($s_String, 0, strlen($s_String)-1);
	}

	function sendToSugar($data) {
		// had to use this custom native soap class and functions because couldn't run both cakephp nusoap server and client
		// this soap call to made to sugar in order to give Sugar the new clientId from toolbox so it's recorded in Sugar
		if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
			$client = new SoapClient('http://sugardev.luxurylink.com:8888/services2/ClientReceiver2?wsdl');
		} else {
			$client = new SoapClient('http://sugarprod.luxurylink.com:8888/services2/ClientReceiver2?wsdl');
		}
		try {
			$client->soap_call($data);
		} catch (SoapFault $exception) {
			// do nothing
		}
		return true;
	}

	function convertToSeoName($str) {
	    $str = strtolower(html_entity_decode($str, ENT_QUOTES, "ISO-8859-1"));  // convert everything to lower string
	    $search_accent = explode(",","ç,æ,~\,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,ñ");
	    $replace_accent = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,n");
	    $search_accent[] = '&';
	    $replace_accent[] = ' and ';
	    $str = str_replace($search_accent, $replace_accent, $str);
	    $str = preg_replace("/<([^<>]*)>/", ' ', $str);                     // remove html tags
	    $str_array = preg_split("/[^a-zA-Z0-9]+/", $str);                   // remove non-alphanumeric
	    $count_a = count($str_array);
	    if ($count_a) {
	        if ($str_array[0] == 'the') {
	            array_shift($str_array);
	        }
	        if (isset($str_array[($count_a - 1)]) && (($str_array[($count_a - 1)] == 'the') || !$str_array[($count_a - 1)])) {
	            array_pop($str_array);
	        }
	        for ($i=0; $i<$count_a; $i++) {
	            if ($str_array[$i]=='s' && strlen($str_array[($i - 1)])>1) {
	                $str_array[($i - 1)] = $str_array[($i - 1)] . 's';
	                unset($str_array[$i]);
	            } elseif ($str_array[$i]=='' || !$str_array[$i]) {
	                unset($str_array[$i]);
	            }
	        }
	        return (substr(implode('-', $str_array), 0, 499));
	    }else {
	        return '';
	    }
	}
}
?>
