<?php

App::import('Vendor', 'nusoap/web_services_controller');

require(APP.'/vendors/pp/Processor.class.php');
//require(APP.'/vendors/aes.php');

Configure::write('debug', 0);

class WebServicePaymentsController extends WebServicesController
{
	var $name = 'WebServicePayments';
	var $uses = array('UserPaymentSetting','Ticket', 'PaymentDetail');
	var $serviceUrl = 'http://toolboxdev.luxurylink.com/web_service_payments';
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
					'removeCard' => array(
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'setPrimaryCard' => array(
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						),
					'processPayment' => array(
						'input' => array('in0' => 'xsd:string'),
						'output' => array('return' => 'xsd:string')
						)
					);

	function beforeFilter() { $this->LdapAuth->allow('*'); }

	function setUserPaymentSetting($in0) {
		$data = json_decode($in0, true);
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
	
	function getUserPaymentSetting($in0) {
		$data = json_decode($in0, true);
		if (empty($data['userId'])) {
			return '0';	
		} 
		$this->UserPaymentSetting->recursive = -1;
		$userPaymentSettingData = $this->UserPaymentSetting->find('all', array('conditions' => array('userId' => $data['userId'], 'inactive' => 0)));
		if ($userPaymentSettingData) {
			$response = array();
			foreach ($userPaymentSettingData as $k => $v) {
				$response[] = $v['UserPaymentSetting'];	
			}
			return json_encode($response);	
		} else {
			return '0';	
		}
	}
	
	function setPrimaryCard($in0) {
		$data = json_decode($in0, true);
		if (empty($data['userId']) || empty($data['userPaymentSettingId'])) {						
			return '0';	
		} 	
		$this->UserPaymentSetting->query('UPDATE userPaymentSetting SET primaryCC = 0 WHERE userId = ' . $data['userId']);
		$data['primaryCC'] = 1;
		if ($this->UserPaymentSetting->save($data)) {
			return '1';
		} else {
			return '0';	
		}
	}
	
	function removeCard($in0) {
		$data = json_decode($in0, true);
		if (empty($data['userId']) || empty($data['userPaymentSettingId'])) {
			return '0';	
		} 
		$data['inactive'] = 1;
		if ($this->UserPaymentSetting->save($data)) {
			return '1';
		} else {
			return '0';	
		}
	}
	
	function processPayment($in0) {
		$data = json_decode($in0, true);
		if (!isset($data['userId']) || empty($data['userId'])) {
			return '0';	
		}
		if (!isset($data['userPaymentSettingId']) || empty($data['userPaymentSettingId'])) {
			return '0';	
		}
		if (!isset($data['ticketId']) || empty($data['ticketId'])) {
			return '0';	
		}
		
		$this->Ticket->recursive = -1;
		$ticket = $this->Ticket->read(null, $data['ticketId']);
		if (!$ticket) {
			return '0';	
		} 
		if ($ticket['Ticket']['userId'] != $data['userId']) {
			return '0';
		}
		
		$this->UserPaymentSetting->recursive = -1;
		$userPaymentSetting = $this->UserPaymentSetting->read(null, $data['userPaymentSettingId']);
		if (!$userPaymentSetting) {
			return '0';	
		}
		
		// init payment processing
		$processor = new Processor('AIM');
		$processor->InitPayment($userPaymentSetting, $ticket);	
		//$processor->SubmitPost();
		
		if (!$processor->ChargeSuccess()) {
			return $processor->GetResponseTxt();
		} else {
			return 'good charge';	
		}
	}
}
?>