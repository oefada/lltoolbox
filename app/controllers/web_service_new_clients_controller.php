<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');

class WebServiceNewClientsController extends WebServicesController
{
	var $name = 'WebServiceNewClients';
	var $uses = array('Client','ClientContact','ConnectorLog','LoaMembershipType','Loa');
	var $serviceUrl = '/web_service_new_clients';

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

        if (isset($sm_request)){
            $this->ConnectorLog->setData($sm_request);
            try {
                $this->ConnectorLog->execute();
            } catch (Exception $e) {
                @mail('oefada@luxurylink.com','Connector Log Error',$e->getMessage());
            }
        }

	    // JSON decoded the request into an assoc. array
	    $decoded_request = json_decode($sm_request, true);

	    // look for a client id but no error check
	    $client_id = trim($decoded_request['client']['client_id']);

	    // respond with DB operation mode
	    if (!isset($client_id) || empty($client_id)) {
	        $response_value = '1';  // client insert will occur
	    } elseif (is_numeric($client_id) && $client_id > 0) {
	        $response_value = '2';  // client update will occur
	    } else {
	        $response_value = '-1';
	    }

		$date_now = date('Y-m-d H:i:s', strtotime('now'));

		if ($client_id) {
            $this->Client->recursive = -1;
			$client = $this->Client->findByClientId($client_id);
			$client_data_save = $client['Client'];
            $client_data_existing = $client;

            $sugarClientName= $this->prepClientNameForImport($decoded_request['client']['client_name']);

            //moved clientNameChange check to beforeSave.

            if (!empty($client_data_save['sites']) && is_array($client_data_save['sites'])) {
                $client_data_save['sites'] = implode(',', $client_data_save['sites']);
            }

		} else {
			$client_data_save = array();
			$client_data_save['sites'] = 'luxurylink';
		}

		// map data from Sugar to toolbox client table structure
		//$client_data_save = array();
        $client_data_save['name']               = $this->prepClientNameForImport($decoded_request['client']['client_name']);
        $client_data_save['nameNormalized']     = $this->Client->normalize($client_data_save['name'], 1);
        $client_data_save['managerUsername'] 	= $decoded_request['client']['manager_ini'];
		$client_data_save['teamName']			= $decoded_request['client']['team_name'];
        $client_data_save['segment']			= $decoded_request['client']['segment'];

        if(empty($client_data_save['address1'])){
            $client_data_save['address1']			= $decoded_request['client']['address1'];
        }
        if(empty($client_data_save['address1'])){
            $client_data_save['address1']			= $decoded_request['client']['address1'];
        }
        if(empty($client_data_save['postalCode'])){
            $client_data_save['postalCode']			= $decoded_request['client']['postalCode'];
        }

        $client_data_save['modified']			= $date_now;
        $client_data_save['seoName']			= $this->Client->convertToSeoName($client_data_save['name']);

		// unbind assoc
		$this->Client->bindOnly(array(), false);

		if ($client_id && is_numeric($client_id)) {
			// ======= EXISTING CLIENT UPDATE ========
            //exit($client_data_save['name'] );
			// *** check first by doing a manual update ***
			$result = $this->Client->query("UPDATE client SET modified = NOW() WHERE clientId = $client_id LIMIT 1");
			if ($this->Client->getAffectedRows()) {
        		$client_data_save['clientId'] = $client_id;
				$client_cake_save = array();
				$client_cake_save['Client'] = $client_data_save;
	        	if (!$this->Client->save($client_cake_save, array('callbacks' => false))) {

                    $errMsg= print_r($client_data_save, true) . print_r($this->Client->validationErrors, true) . print_r($decoded_request, true) . print_r($this->Client->validationErrors, true);
                    if ($_SERVER['ENV'] !== 'development' && ISSTAGE !== true){
					@mail('dev@luxurylink.com', 'SUGAR BUS -- EXISTING CLIENT NOT SAVED',$errMsg );
                    }else{
                     //
                        @mail('devmail@luxurylink.com', 'SUGAR BUS -- EXISTING CLIENT NOT SAVED',$errMsg );
                    }
				} else {
                    @$this->Client->handlePdpUrlDataChanges($client_data_existing,$client_cake_save);
                    // Save succeeded. Also saving to LuxuryLink Database
                    // LuxuryLink DB/Client Table also needs to be updated RE TICKET4270
                    $llquery = "UPDATE luxurylink.client SET ";
                    $llquery .= "name = '$client_data_save[name]', ";
                    $llquery .= "nameNormalized = '$client_data_save[nameNormalized]', ";
                    $llquery .= "seoName = '$client_data_save[seoName]' ";
                    $llquery .= "WHERE clientId = $client_id";

                    $result = $this->Client->query($llquery);
                }
	        	$decoded_request['client']['client_id'] = $client_id;
			} else {
				// the client id was invalid so send devmail, and do nothing
				@mail('dev@luxurylink.com', 'SUGAR BUS [CLIENT] -- INVALID CLIENTID', print_r($client_data_save, true) . print_r($decoded_request, true));
				return false;
			}
		} else {
			
			// 10/7/2011 jwoods - duplicate name check
			$dupClient = $this->Client->query("SELECT * FROM client WHERE name = ?", array($decoded_request['client']['client_name']));
			if (isset($dupClient[0]) && isset($dupClient[0]['client']['clientId'])) {
				@mail('dev@luxurylink.com', 'SUGAR BUS -- DUPLICATE CLIENT NAME', print_r($dupClient, true) . print_r($decoded_request, true));
				$decoded_request['request']['response'] = '-1';
				$decoded_request['request']['response_time'] = time();
				$decoded_request['client']['client_id'] = -1;
				$encoded_response = json_encode($decoded_request);
				return $encoded_response;
			}
			// end duplicate name check
			
			// ======= NEW CLIENT INSERT =============
			$next_auto_inc_result = $this->Client->query("SHOW TABLE STATUS WHERE Name = 'client'");
			$next_client_auto_id = $next_auto_inc_result[0]['TABLES']['Auto_increment'];

			$client_data_save['inactive'] 				= 1; // set new clients from sugar to inactive
			$client_data_save['created'] 				= $date_now;
			$client_data_save['oldProductId']			= "0-$next_client_auto_id";
			$client_data_save['accountingId']			= $next_client_auto_id;

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
		// no need to send new client id back to sugar, it is sent in the intial response.
	    //$this->sendToSugar($encoded_response);

	    // update client contacts
	    // ----------------------------------------------------------------------
	    // clientContactTypeId = 1 is reservation SUGAR -> (AUC, CCALL, ALL_AUC)
	    // clientContactTypeId = 2 is homepage notification SUGAR ->(MKT, CCALL, ALL, ALL_AUC)

	  	$reservationContacts = array('AUC', 'ALL_AUC', 'ALL.AUC');
	  	$homepageContacts = array('MKT', 'MKT_ALL', 'ALL');
		$reservationCopy = array('ALL');
		$deleteContacts = array('NLT');

		$this->ClientContact->recursive = -1;
		
	    if ($client_id) {
            //existing data
            $clientDataCurrent = $this->Client->findByClientId($client_id);
            $clientDataCurrent = $clientDataCurrent['Client'];

                if (empty($clientDataCurrent['numRooms'])) {
                    //current numRooms not set, use sugar setting
                    $client_data_save['numRooms'] = $decoded_request['client']['numRooms'];
                }
                if (empty($clientDataCurrent['url'])) {

                    //if current URL not set, use sugar
                    if (!empty($decoded_request['client']['url'])) {
                        $client_data_save['url'] = $this->httpify($decoded_request['client']['url']);
                    }
                }
                if (empty($clientDataCurrent['locationDisplay'])) {
                    //if locationDisplay is not currently set, let's set it
                    if (!empty($decoded_request['client']['state'])) {
                        //if a state is passed, location display = City, State
                        $client_data_save['locationDisplay'] = $decoded_request['client']['city'] . ', ' . $decoded_request['client']['state'];
                    } else {
                        //No state, location display = City, Country
                        $client_data_save['locationDisplay'] = $decoded_request['client']['city'] . ', ' . ucwords(
                                $decoded_request['client']['country_name']
                            );
                    }
                }
                $client_data_save['clientId'] = $client_id;
                @$this->Client->save($client_data_save, array('callbacks' => false));

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
                $newClientContact['title']					= $contact['title'];
                $newClientContact['fax']					= $contact['phone_fax'];
		    	$newClientContact['sugarContactId']			= $contact_id;

		    	if (in_array($recipient_type, $reservationContacts)) {
		    		// Verify that there isn't already a primary reservation contact
					$existing = $this->ClientContact->find('all',array('conditions' => array('ClientContact.clientId' => $client_id)));
					
					$newClientContact['clientContactTypeId'] = 1;
					
					foreach ($existing as $e) {
						// Set this to a "copy" if a main/primary exists
						if ($e['ClientContact']['clientContactTypeId'] == "1") {
							$newClientContact['clientContactTypeId'] = 3;
							break;
						}
					}
		    		
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
        //clientID exists, begin LOA workflow. Ensure LOA workflow fails gracefully.
           if (isset($decoded_request['loas'])){
               try{
                   $newLoaId = @$this->Loa->processLoaFromSugar($client_id,$decoded_request['loas']);

                   if(isset($newLoaId) && is_numeric($newLoaId)){
                       $decoded_request['loaSugarId'] = $decoded_request['loas'][0]['id'];
                       $decoded_request['newLoaId'] = $newLoaId;

                       $reconcileLoaProposals = @$this->Loa->updateLastSugarLoaProposalWithClientId($client_id, $newLoaId, $decoded_request['loaSugarId']);
                       if($reconcileLoaProposals !== false){
                           $decoded_request['LoaProposalsReconciled'] = 1;
                       }
                   }

               }catch(Exception $e){
                   mail('devmail@luxurylink.com','Loa Import Error','Sugar Name:'.print_r($sugarClientName,true)
                       .'ToolboxName: '.print_r($client['Client']['name'],true).'Sugar Data'.print_r($decoded_request['loas'],true));
               }
           }

		}

        $encoded_response = json_encode($decoded_request);
	    // this tests to see if we were getting correct response from the request
	    return $encoded_response;
	}

	function utf8dec ( $s_String ) {
		$s_String = html_entity_decode(htmlentities($s_String." ", ENT_COMPAT, 'UTF-8'));
		return substr($s_String, 0, strlen($s_String)-1);
	}

    private function prepClientNameForImport($clientName = null)
    {
        if (!isset($clientName)) {
            return '';
        }
        $clientName = str_replace('&#039;', "'", $clientName);
        $clientName = $this->utf8dec($clientName);
        return $clientName;
    }

    function sendToSugar($data) {
		// had to use this custom native soap class and functions because couldn't run both cakephp nusoap server and client
		// this soap call to made to sugar in order to give Sugar the new clientId from toolbox so it's recorded in Sugar
		if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
			//$client = new SoapClient('http://sugardev.luxurylink.com:8888/services2/ClientReceiver2?wsdl'); //this is the old mule web service
			$client = new SoapClient('http://devwest.levementumhosting.com/wendell/luxurylink/post_upgrade/custom/soap.php?wsdl'); //this is the new dev sugar
			
		} else {
			//$client = new SoapClient('http://sugarprod.luxurylink.com:8888/services2/ClientReceiver2?wsdl'); //this is the old mule web service
			$client = new SoapClient('http://devwest.levementumhosting.com/wendell/luxurylink/post_upgrade/custom/soap.php?wsdl'); //this is the new dev sugar
			
		}
		try {
			//$client->soap_call($data);
			$client->update_client($data); //changed method to update client
		} catch (SoapFault $exception) {
			// do nothing
		}
		return true;
	}

    private function httpify($link, $append = 'http://', $allowed = array('http://', 'https://'))
    {
        $found = true;
        foreach ($allowed as $protocol) {
            if (strpos($link, $protocol) === 0) {
                $found = false;
            }
            if (!$found) {
                return $link;
            }
        }
        return $append . $link;
    }

}
?>
