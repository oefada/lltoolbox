<?php

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

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
	
		mail('alee@luxurylink.com','sugar',print_r($decoded_request, true));
		/*
	
	    //$tests = ll_execute_sproc('llsp_ins_esb_test', array('text'=> print_r($decoded_request['client'],true)));
	    //$tests = ll_execute_sproc('llsp_ins_esb_test', array('text'=> $sm_request));
	
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
	
	    // this SPROC inserts or updates and checks for client active / expiration dates
	    $result = ll_execute_sproc('llsp_upd_client_mstr_from_sugar', $decoded_request['client']);
	
	    // DB error
	    if (!$result || !mssql_num_rows($result)) {
	        $response_value = '-2';
	    } else {
	        // result set with updated fields after the sproc
	        $sm_sproc_response = mssql_fetch_array($result);
	
	        foreach ($decoded_request['client'] as $k => $v) {
	            $decoded_request['client'][$k] = $sm_sproc_response[$k];
	        }
	    }
	
	    $decoded_request['request']['response'] = $response_value;
	    $decoded_request['request']['response_time'] = time();
	
	    $encoded_response = json_encode($decoded_request);
	
	    // sugar has to update appropiate site manger id.  send to esb client id and sugar id
	    $client = new nusoap_client('http://192.168.100.22:8888/services2/ClientReceiver2?wsdl',true);
	    $response_esb = $client->call('soap_call', array('args' => $encoded_response));
	
	    // tests to see if we were getting correct response from the request
	    return $encoded_response;
	    */
	    return $decoded_request;
	}
}
?>