<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');

class WebServiceNewClientsController extends WebServicesController
{
	var $name = 'WebServiceNewClients';
	var $uses = 'Client';
	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_new_clients';
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
		
		// map data from Sugar to toolbox client table structure
		$client_data_save = array();
        $client_data_save['name']				= $decoded_request['client']['client_name'];
        $client_data_save['phone1']				= $decoded_request['client']['client_phone1'];
        $client_data_save['phone2']				= $decoded_request['client']['client_phone2'];
        $client_data_save['fax']				= $decoded_request['client']['client_fax1'];
        
		if ($client_id && is_numeric($client_id)) {
			// ======= EXISTING CLIENT UPDATE ========
        	$client_data_save['clientId'] = $client_id;
        	$this->Client->save($client_data_save);		
		} else {
			// ======= NEW CLIENT INSERT =============
			$client_data_save['inactive'] 				= 1; // set new clients from sugar to inactive
			$client_data_save['created'] 				= date('Y-m-d H:i:s', strtotime('now'));
			$client_data_save['managerUsername'] 		= $decoded_request['client']['manager_ini'];
			
			$this->Client->create();
			$this->Client->save($client_data_save);
			
			// get new client id and send back to Sugar
			$decoded_request['client']['client_id'] = $this->Client->getLastInsertId();
			
			// new client is created now...reupdate with information
			$new_client_data_update = array();
			$new_client_data_update['clientId'] 		= $decoded_request['client']['client_id'];
			$new_client_data_update['oldProductId'] 	= '0-' . $decoded_request['client']['client_id'];
			$this->Client->save($new_client_data_update);
		}
		
		//$decoded_request['client']['client_desc'];        
		//$decoded_request['client']['client_level_id'];
		//$decoded_request['client']['manager_ini'];
        //$decoded_request['client']['manager'];
		//$decoded_request['client']['client_name2'];
        //$decoded_request['client']['client_name3'];
		//$decoded_request['client']['client_note'];
        //$decoded_request['client']['client_email_address2'];
        //$decoded_request['client']['client_email_address3'];        
        //$decoded_request['client']['client_phone3'];		
		//$decoded_request['client']['client_fax2'];
        //$decoded_request['client']['client_fax3'];
        //$decoded_request['client']['client_cell1'];
        //$decoded_request['client']['client_cell2'];
        //$decoded_request['client']['client_cell3'];
	
	    $decoded_request['request']['response'] = $response_value;
	    $decoded_request['request']['response_time'] = time();
	
	    $encoded_response = json_encode($decoded_request);

		// send info back to sugar -- should only go back to Sugar webservice on new clients so we can give Sugar back new client id.
		// look in Sugar : /var/www/html/soap/SoapSugarUsers.php for the web service 'update_client'
		// look in Sugar : /var/www/html/custom/modules/Accounts/SiteManagerAgent.php for the after hook Sugar logic.
		
	    $this->sendToSugar($encoded_response);
	    	
	    // this tests to see if we were getting correct response from the request
	    return $encoded_response;
	}
	
	function sendToSugar($data) {
		// had to use this custom native soap class and functions because couldn't run both cakephp nusoap server and client
		// this soap call to made to sugar in order to give Sugar the new clientId from toolbox so it's recorded in Sugar

		ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
		$client = new SoapClient('http://sugarprod.luxurylink.com:8888/services2/ClientReceiver2?wsdl'); 
		try {
			$client->soap_call($data);
		} catch (SoapFault $exception) {
			@mail('geeks@luxurylink.com', 'need to make a new error handling function', $exception);
		}
		return true;
	}
}
?>