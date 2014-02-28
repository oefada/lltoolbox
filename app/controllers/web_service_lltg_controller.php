<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');

class WebServiceLltgController extends WebServicesController
{
	var $name = 'WebServiceLltg';
	var $uses = array('MailVendorFailure');
	var $serviceUrl = '/web_service_lltg';

	var $errorResponse = false;
	var $api = array(
					'log_mail_failure' => array(
						'doc' => 'Save new client or update record from Sugar',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function beforeFilter() { $this->LdapAuth->allow('*'); }

	// main function to update or insert client records
	function log_mail_failure($in0)
	{
		$data = json_decode($in0, true);
		$failureDetail = array();
		$failureDetail['dateCreated'] = date('Y-m-d H:i:s');
		$failureDetail['isSent'] = 0;
		$failureDetail['originalId'] = 0;
		$failureDetail['messageResponses'] = $data['messageResponses'];
		$failureDetail['messageError'] = $data['messageError'];
		$failureDetail['messageContent'] = $data['messageContent'];
		$this->MailVendorFailure->create();
		$result = $this->MailVendorFailure->save($failureDetail);
	    return '';
	}



}
?>
