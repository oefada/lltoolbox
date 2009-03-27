<?php

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

class WebServiceUsersController extends WebServicesController
{
	var $name = 'WebServiceUsers';
	var $uses = 'User';
	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_users';
	var $serviceUrlDev = 'http://toolboxdev.luxurylink.com/web_service_users';
	var $errorResponse = array();
	var $api = array(
					'userProcessor1' => array(
						'doc' => 'Any changes to user info from LIVE will be pushed here to update backend',
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function beforeFilter() { $this->LdapAuth->allow('*'); }

	function userProcessor1($in0)
	{
		$json_decoded = json_decode($in0, true);
		$this->errorResponse = false;
		if (!$this->updateUserBackend($json_decoded)) {
			$json_decoded['response'] = $this->errorResponse;
		} 
		return json_encode($json_decoded);
	}
	
	function updateUserBackend($data) 
	{
		if (!isset($data['User']['userId'])) {
			$this->errorResponse[] = 501;	
			return false;
		}

		$this->User->skipBeforeSaveFilter = true;	

		// in the user site extended model, it doesn't check by array key.
		if (isset($data['UserSiteExtended'][0])) {
			$data['UserSiteExtended'] = $data['UserSiteExtended'][0];
		}
	
		if ($this->User->saveAll($data, false)) {
			return true;
		} else {
			@mail('devmail@luxurylink.com', 'WEB SERVICE: User Save Failed', print_r($this->validationErrors, true) . print_r($data, true));	
			$this->errorResponse = 505;
			return false;	
		}
	}
}
?>