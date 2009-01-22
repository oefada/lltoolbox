<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');

class WebServiceNewClientsController extends WebServicesController
{
	var $name = 'WebServiceNewClients';
	var $uses = 'Client';
	var $serviceUrl = 'http://toolboxdev.luxurylink.com/web_service_new_clients';
	var $errorResponse = false;
	var $api = array(
					'save_client' => array(
						'doc' => 'Save new client or update record from Sugar',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function beforeFilter() { $this->Auth->allow('*'); }

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
        $client_data_save['clientTypeId']		= $decoded_request['client']['client_type_id'];
        
        $client_data_save['contactSalutation']	= $decoded_request['client']['client_name1'];
        //$client_data_save['email']				= $decoded_request['client']['client_email_address1'];
       
        $client_data_save['phone1']				= $decoded_request['client']['client_phone1'];
        $client_data_sawe['phone2']				= $decoded_request['client']['client_phone2'];
        $client_data_save['fax']				= $decoded_request['client']['client_fax1'];
       	$client_data_save['address1']			= $decoded_request['client']['client_address1'];
        $client_data_save['address2']			= $decoded_request['client']['client_address2'];
        $client_data_save['address3']			= $decoded_request['client']['client_address3'];
		
		if ($client_id && is_numeric($client_id)) {
			// ======= EXISTING CLIENT UPDATE ========
        	$client_data_save['clientId'] = $client_id;
        	
        	// send back possible new loa changes back to Sugar
        	$loa_result = $this->Client->query("select min(startDate) as startDate, max(endDate) as endDate from loa where clientId = $client_id and inactive <> 1");
        	$new_start_date = $loa_result[0][0]['startDate'];
        	$new_end_date = $loa_result[0][0]['endDate'];
        	if ($new_start_date && $new_end_date) {
        		$decoded_request['client']['client_date_active'] = $new_start_date;
        		$decoded_request['client']['client_date_expire'] = $new_end_date;
        	}
        	
        	$this->Client->save($client_data_save);	
		} else {
			// ======= NEW CLIENT INSERT =============
			$this->Client->create();
			$this->Client->save($client_data_save);
			
			// get new client id and send back to Sugar
			$decoded_request['client']['client_id'] = $this->Client->getLastInsertId();
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
		$client = new SoapClient('http://sugardev.luxurylink.com:8888/services2/ClientReceiver2?wsdl'); 
		try {
			$client->soap_call($data);
		} catch (SoapFault $exception) {
			@mail('geeks@luxurylink.com', 'need to make a new error handling function', $exception);
		}
		return true;
	}
}
?>