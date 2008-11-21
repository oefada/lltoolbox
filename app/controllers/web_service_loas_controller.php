<?php

App::import('Vendor', 'nusoap/web_services_controller');

Configure::write('debug', 0);

class WebServiceLoasController extends WebServicesController
{
	var $name = 'WebServiceLoas';
	var $uses = array('ticket', 'Loa');
	var $serviceUrl = 'http://192.168.100.22/web_service_loas';
	var $errorResponse = array();
	var $api = array(
					'updateTrack' => array(
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function updateTrack($in0) {
		$data = json_decode($in0, true);
		return 'asgasdgasdg';
		if (empty($data['userId'])) {
			return '0';
		}
		$this->UserPaymentSetting->recursive = -1;
		$userPaymentSettingData = $this->UserPaymentSetting->find('all', array('conditions' => array('userId' => $data['userId'], 'inactive' => 0)));
		$foundPrimaryCC = false;
		foreach ($userPaymentSettingData as $k => $v) {
			if ($v['UserPaymentSetting']['primaryCC'] == 1) {
				$foundPrimaryCC = true;
				break;
			}	
		}
		if (!$foundPrimaryCC) {
			$data['primaryCC'] = 1;	
		}
		if (!isset($data['userPaymentSettingId']) || empty($data['userPaymentSettingId'])) {
			$this->UserPaymentSetting->create();	
		} 
		if ($this->UserPaymentSetting->save($data)) {
			return '1';
		} else {
			return '0';
		}
	}
	
}
?>