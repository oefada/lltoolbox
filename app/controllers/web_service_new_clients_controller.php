<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');

class WebServiceNewClientsController extends WebServicesController
{
	var $name = 'WebServiceNewClients';
	var $uses = array('Client','ClientContact');
	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_new_clients';
	var $serviceUrlDev = 'http://toolboxdev.luxurylink.com/web_service_new_clients';
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
		
		//@mail('devmail@luxurylink.com', 'SUGAR-to-TOOLBOX: Record pushed', print_r($decoded_request, true));			

		$date_now = date('Y-m-d H:i:s', strtotime('now'));
		
		// map data from Sugar to toolbox client table structure
		$client_data_save = array();
        $client_data_save['name']				= str_replace('&#039;', "'", $decoded_request['client']['client_name']);
        $client_data_save['managerUsername'] 	= $decoded_request['client']['manager_ini'];
		$client_data_save['teamName']			= $decoded_request['client']['team_name'];
        $client_data_save['modified']			= $date_now;
        $client_data_save['seoName']			= $this->convertToSeoName($client_data_save['name']);
            
		if ($client_id && is_numeric($client_id)) {
			// ======= EXISTING CLIENT UPDATE ========
        	$client_data_save['clientId'] = $client_id;
        	$this->Client->save($client_data_save);
        	$decoded_request['client']['client_id'] = $client_id;
        			
		} else {
			// ======= NEW CLIENT INSERT =============
			$next_auto_inc_result = $this->Client->query("SHOW TABLE STATUS WHERE Name = 'client'");
			$next_client_auto_id = $next_auto_inc_result[0]['TABLES']['Auto_increment'];
			
			$client_data_save['inactive'] 				= 1; // set new clients from sugar to inactive
			$client_data_save['created'] 				= $date_now;
			$client_data_save['oldProductId']			= "0-$next_client_auto_id";
			
			$this->Client->create();
			$this->Client->save($client_data_save);
			
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
	    
	  	$reservationContacts = array('AUC', 'CCALL', 'ALL_AUC', 'ALL.AUC');
	  	$homepageContacts = array('MKT', 'ALL', 'CCALL', 'ALL_AUC', 'ALL.AUC');
	    
	    if ($client_id) {
		    $contacts = $decoded_request['contacts'];
		    foreach ($contacts as $k => $contact) {
		    	$contact_id = $contact['contact_id'];
		    	$recipient_type	 = $contact['recipient_type_c'];
		    	
		    	if (empty($recipient_type)) {
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
				
				$checkResult = $this->ClientContact->query("SELECT * FROM clientContact WHERE clientId = $client_id AND sugarContactId = '$contact_id'");
		    	
		    	if (empty($checkResult)) {
		    		if (in_array($recipient_type, $reservationContacts)) {
		    			$newClientContact['clientContactTypeId'] = 1;
		    			$this->ClientContact->create();
		    			$this->ClientContact->save($newClientContact);
		    		}
		    		if (in_array($recipient_type, $homepageContacts)) {
		    			$newClientContact['clientContactTypeId'] = 2;
		    			$this->ClientContact->create();
		    			$this->ClientContact->save($newClientContact);
		    		}
		    	} else {
					foreach ($checkResult as $bb => $tb_contact) {
						$newClientContact['clientContactId'] = $tb_contact['clientContact']['clientContactId'];
						$this->ClientContact->save($newClientContact);	
					}
				}
		    }
		}
	      
	    // this tests to see if we were getting correct response from the request
	    return $encoded_response;
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
