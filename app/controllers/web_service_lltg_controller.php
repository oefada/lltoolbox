<?php

Configure::write('debug', 0);
App::import('Vendor', 'nusoap/web_services_controller');

define('DEV_USER_TOOLBOX_HOST', 'http://' . $_SERVER['ENV_USER'] . '-toolboxdev.luxurylink.com/web_service_lltg');

class WebServiceLltgController extends WebServicesController
{
	var $name = 'WebServiceLltg';
	var $uses = array('MailVendorFailure');
	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_lltg';
	var $serviceUrlStage = 'http://stage-toolbox.luxurylink.com/web_service_tickets';
	var $serviceUrlDev = DEV_USER_TOOLBOX_HOST;

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
		$failureDetail['dateCreated'] = date('Y-m-d h:i:s');
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
