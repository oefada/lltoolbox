<?php

die("NO LONGER IN USE");

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

class WebServiceUsersController extends WebServicesController
{
	var $name = 'WebServiceUsers';
	var $uses = 'User';
	var $serviceUrl;
	var $errorResponse = array();
	var $api = array(
					'userProcessor1' => array(
						'doc' => 'Any changes to user info from LIVE will be pushed here to update backend',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function __construct() {
		parent::construct();
		
		$this->serviceUrl = Configure::read("Url.Ws");
	}
}
?>
