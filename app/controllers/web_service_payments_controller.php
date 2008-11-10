<?php

App::import('Vendor', 'nusoap/web_services_controller');
Configure::write('debug', 0);

class WebServicePaymentsController extends WebServicesController
{
	var $name = 'WebServicePayments';
	var $uses = 'UserPaymentSetting';
	var $serviceUrl = 'http://192.168.100.111/web_service_payments';
	var $errorResponse = array();
	var $api = array(
					'setUserPaymentSetting' => array(
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'getUserPaymentSetting' => array(
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'processPayment' => array(
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function setUserPaymentSetting($in0) {
		$data = json_decode($in0, true);
		if (empty($data['userId'])) {
			return '0';
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
	
	function getUserPaymentSetting($in0) {
		$data = json_decode($in0, true);
		if (empty($data['userId'])) {
			return '0';	
		} 
		$userPaymentSettingData = $this->UserPaymentSetting->find('all', array('conditions' => array('userId' => $data['userId'])));
		if ($userPaymentSettingData) {
			return json_encode($userPaymentSettingData);	
		} else {
			return '0';	
		}
	}
	
	function processPayment($in0) {
		$data = json_decode($in0, true);
		$this->initProcessPayment($data);	
	}
}
?>