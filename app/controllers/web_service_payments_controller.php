<?php

App::import('Vendor', 'nusoap/web_services_controller');

require(APP.'/vendors/pp/Processor.class.php');  
App::Import('Vendor', 'aes.php');

Configure::write('debug', 0);

class WebServicePaymentsController extends WebServicesController
{
	var $name = 'WebServicePayments';
	var $uses = array('UserPaymentSetting','Ticket', 'PaymentDetail');
	var $serviceUrl = 'http://toolbox.luxurylink.com/web_service_payments';
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
	
	// testingdebug
	function processPayment2($in0) {
		// ---------------------------------------------------------------------------
		// SUBMIT PAYMENT VIA PROCESSOR [alee@luxurylink.com]
		// ---------------------------------------------------------------------------
		// REQUIRED: (1) userId
		//           (2) ticketId
		//			 (3) paymentProcessorId
		// 			 (4) paymentAmount
		//           (5) initials
		//			 (6) autoCharge
		//           (7) saveUps
		//           (8) userPaymentSettingId or userPaymentSetting data array
		//
		// SEND TO PAYMENT PROCESSOR: $userPaymentSettingPost
		//
		// ---------------------------------------------------------------------------
		/* START SAMPLE INPUT */
		$tmp = array();
		$tmp['userId'] 								= 1130607;
		$tmp['ticketId'] 							= 132433;
		$tmp['paymentProcessorId'] 					= 3;
		$tmp['paymentAmount'] 						= 1.00; // TODO: handle error check on this
		$tmp['initials'] 							= 'AL';
		$tmp['autoCharge']							= 0;
		$tmp['saveUps'] 							= 0;    // TODO: handle this guy
		$tmp['userPaymentSettingId'] 				= false; //59877;
		
		$tmp['userPaymentSetting'] 					= array();
		$tmp['userPaymentSetting']['ccNumber'] 		= '4640320008760123';
		$tmp['userPaymentSetting']['ccType'] 		= 'VI';
		$tmp['userPaymentSetting']['userId'] 		= 1130607;
		$tmp['userPaymentSetting']['nameOnCard'] 	= 'Arons Lee';
		$tmp['userPaymentSetting']['expYear'] 		= '2012';
		$tmp['userPaymentSetting']['expMonth'] 		= '04';
		$tmp['userPaymentSetting']['address1'] 		= '123 Fake St.';
		$tmp['userPaymentSetting']['address2'] 		= 'Street 2 Fake Test';
		$tmp['userPaymentSetting']['city'] 			= 'Fake Test City';
		$tmp['userPaymentSetting']['state'] 		= 'CA';
		$tmp['userPaymentSetting']['country'] 		= 'US';
		$tmp['userPaymentSetting']['postalCode'] 	= 90501;
		
		$in0 = json_encode($tmp);
		/* END SAMPLE INPUT */
		// ---------------------------------------------------------------------------
		
		// good o' error checking my friends.  make this as strict as possible
		// ---------------------------------------------------------------------------
		$data = json_decode($in0, true);

		if (!isset($data['userId']) || empty($data['userId'])) {
			return '101';
		}
		if (!isset($data['ticketId']) || empty($data['ticketId'])) {
			return '102';
		}
		if (!isset($data['paymentProcessorId']) || !$data['paymentProcessorId']) {
			return '103';	
		}
		if (!isset($data['paymentAmount']) || !$data['paymentAmount']) {
			return '104';	
		}
		if (!isset($data['initials']) || empty($data['initials'])) {
			return '105';	
		}
		if (!isset($data['autoCharge'])) {
			return '106';	
		}
		if (!isset($data['saveUps'])) {
			return '107';	
		}
		
		// and even some more error checking.
		// ---------------------------------------------------------------------------
		$this->Ticket->recursive = -1;
		$ticket = $this->Ticket->read(null, $data['ticketId']);
		if (!$ticket) {
			return '108';
		} 
		if ($ticket['Ticket']['userId'] != $data['userId']) {
			return '109';
		}
		
		// use either the data sent over or retrieve from the db with the id
		// ---------------------------------------------------------------------------
		$userPaymentSettingPost = array();
		
		$usingUpsId = false;
		if (isset($data['userPaymentSettingId']) && !empty($data['userPaymentSettingId']) && is_numeric($data['userPaymentSettingId'])) {
			$tmp_result = $this->Ticket->query('SELECT * FROM userPaymentSetting WHERE userPaymentSettingId = ' . $data['userPaymentSettingId'] . ' LIMIT 1');
			$userPaymentSettingPost['UserPaymentSetting'] = $tmp_result[0]['userPaymentSetting'];
			$userPaymentSettingPost['UserPaymentSetting']['ccNumber'] = aesFullDecrypt($userPaymentSettingPost['UserPaymentSetting']['ccNumber']);
			unset($tmp_result);
			$usingUpsId = true;
		} else {
			$userPaymentSettingPost['UserPaymentSetting'] = $data['userPaymentSetting'];	
		}
		
		if (!$userPaymentSettingPost || empty($userPaymentSettingPost)) {
				return '110';
		} 
		
		// set which processor to use
		// ---------------------------------------------------------------------------
		$paymentProcessorName = false;
		switch($data['paymentProcessorId']) {
			case 1:
				$paymentProcessorName = 'NOVA';
				break;
			case 3:
				$paymentProcessorName = 'PAYPAL';
				break;
			case 4:
				$paymentProcessorName = 'AIM';
				break;
			default:
				break;
		}
		
		// init payment processing and submit payment
		// ---------------------------------------------------------------------------
		$processor = new Processor($paymentProcessorName);
		$processor->InitPayment($userPaymentSettingPost, $ticket);	
		//$processor->SubmitPost();  do not turn on until launch!

		// save the response from the payment processor
		// ---------------------------------------------------------------------------
		$nameSplit 								= str_word_count($userPaymentSettingPost['UserPaymentSetting']['nameOnCard'], 1);
		$firstName 								= trim($nameSplit[0]);
		$lastName 								= trim(array_pop($nameSplit));
		
		$paymentDetail 							= array();
		$paymentDetail 							= $processor->GetMappedResponse();
		$paymentDetail['paymentTypeId'] 		= 1; 
		$paymentDetail['paymentAmount']			= $ticket['Ticket']['billingPrice'];
		$paymentDetail['ticketId']				= $ticket['Ticket']['ticketId'];
		$paymentDetail['userId']				= $ticket['Ticket']['userId'];
		$paymentDetail['userPaymentSettingId']	= ($usingUpsId) ? $data['userPaymentSettingId'] : '';
		$paymentDetail['paymentProcessorId']	= $data['paymentProcessorId'];
		$paymentDetail['ppFirstName']			= $firstName;
		$paymentDetail['ppLastName']			= $lastName;
		$paymentDetail['ppBillingAddress1']		= $userPaymentSettingPost['UserPaymentSetting']['address1'];
		$paymentDetail['ppBillingCity']			= $userPaymentSettingPost['UserPaymentSetting']['city'];
		$paymentDetail['ppBillingState']		= $userPaymentSettingPost['UserPaymentSetting']['state'];
		$paymentDetail['ppBillingZip']			= $userPaymentSettingPost['UserPaymentSetting']['postalCode'];
		$paymentDetail['ppBillingCountry']		= $userPaymentSettingPost['UserPaymentSetting']['country'];
		$paymentDetail['ppCardNumLastFour']		= substr($userPaymentSettingPost['UserPaymentSetting']['ccNumber'], -4, 4);
		$paymentDetail['ppExpMonth']			= $userPaymentSettingPost['UserPaymentSetting']['expMonth'];
		$paymentDetail['ppExpYear']				= $userPaymentSettingPost['UserPaymentSetting']['expYear'];
		$paymentDetail['ppBillingAmount']		= $data['paymentAmount'];
		$paymentDetail['autoProcessed']			= $data['autoCharge'];
		$paymentDetail['initials']				= $data['initials'];

		$this->PaymentDetail->create();
		if (!$this->PaymentDetail->save($paymentDetail)) {
			return '111';
		}
		
		// okay everything good -- lets send a good response back. either charged or declined
		// ---------------------------------------------------------------------------
		return '0';
	}
}
?>