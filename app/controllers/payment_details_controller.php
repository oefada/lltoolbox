<?php

App::import('Vendor', 'aes.php');

class PaymentDetailsController extends AppController {

	var $name = 'PaymentDetails';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');

	function index() {
		$this->PaymentDetail->recursive = 0;
		$this->set('paymentDetails', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Payment Detail Id.', true), 'default', array(), 'error');
		}
		$this->set('paymentDetail', $this->PaymentDetail->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			
			$usingNewCard = 0;
			$saveNewCard = 0;
			
			if (isset($this->data['UserPaymentSetting']['useNewCard'])) {
				unset($this->data['UserPaymentSetting']['useNewCard']);
				$usingNewCard = 1;
			} 
			if (isset($this->data['UserPaymentSetting']['save'])) {
				unset($this->data['UserPaymentSetting']['save']);	
				$saveNewCard = 1;	
			}
			
			$badPaymentRequest = false;	
			
			$data = array();
	        $data['userId']                 = $this->data['PaymentDetail']['userId'];
	        $data['ticketId']               = $this->data['PaymentDetail']['ticketId'];
	        $data['paymentProcessorId']     = $this->data['PaymentDetail']['paymentProcessorId'];
	        $data['paymentAmount']          = $this->data['PaymentDetail']['billingPrice'];
	        $data['initials']               = $this->data['PaymentDetail']['initials'];
	        $data['autoCharge']             = 0;
	        $data['saveUps']                = $saveNewCard;
	        $data['zAuthHashKey']           = md5('L33T_KEY_LL' . $data['userId'] . $data['ticketId'] . $data['paymentProcessorId'] . $data['paymentAmount'] . $data['initials']);
	        
	        if ($usingNewCard) {
	        	$data['userPaymentSetting'] = $this->data['UserPaymentSetting'];
	        	$data['userPaymentSetting']['ccNumber'] 		= aesEncrypt($data['userPaymentSetting']['ccNumber']);
	        } elseif ($this->data['PaymentDetail']['userPaymentSettingId']) {
	        	$data['userPaymentSettingId'] = $this->data['PaymentDetail']['userPaymentSettingId'];
	        } else {
		        $badPaymentRequest = true;
		    }
	        
	        if (!$badPaymentRequest) {
	        	$paymentResponse = $this->sendToPayment(json_encode($data));
	        	if (trim($paymentResponse) == 'CHARGE_SUCCESS') {
	        		$this->Session->setFlash(__('Payment was successfully charged.', true), 'default', array(), 'success');
	        		$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $this->data['PaymentDetail']['ticketId']));
	        	} else {
	        		$this->Session->setFlash(__('Payment Not Processed -- Error: ' . $paymentResponse, true), 'default', array(), 'error');
	        	}
	        } else {
	        	$this->Session->setFlash(__('No payment account selected.', true), 'default', array(), 'error');
	        }
		}
		$this->PaymentDetail->Ticket->recursive = 2;
		$ticket = $this->PaymentDetail->Ticket->read(null, $this->params['ticketId']);
		$ticket['Ticket']['totalBillingAmount'] = in_array($ticket['Ticket']['offerTypeId'], array(1,2,6)) ? 30 : 40;
		$ticket['Ticket']['totalBillingAmount'] += $ticket['Ticket']['billingPrice'];
		$this->set('ticket', $ticket);
		$this->set('userPaymentSetting', $ticket['User']['UserPaymentSetting']);
		$this->set('paymentTypeIds', $this->PaymentDetail->PaymentType->find('list'));
		$this->set('paymentProcessorIds', $this->PaymentDetail->PaymentProcessor->find('list'));		
	}
	
	function sendToPayment($data) {
		ini_set("soap.wsdl_cache_enabled", "0"); // disabling WSDL cache
		$client = new SoapClient('http://toolbox.luxurylink.com/web_service_payments/?wsdl'); 
		try {
			$response = $client->processPayment($data);
			return $response;
		} catch (SoapFault $exception) {
			@mail('geeks@luxurylink.com', 'WEB SERVICE ERROR:  paymentDetail LL manual charge error', $exception);
			return false;
		}
	}

	function test() {
		$data['ppResponseDate'] 			= '2009-02-21 10:13:43';
	    $data['ppTransactionId'] 			= '15FE8E86B-DDD9-F891-7432-D3C25AF1F6BF';
	    $data['ppApprovalText'] 			= 'APPROVAL';
	    $data['ppApprovalCode'] 			= 0;
	    $data['ppAvsCode'] 					= 'Y';
	    $data['ppResponseText'] 			= '';
	    $data['ppResponseSubCode'] 			= '';
	    $data['ppReasonCode'] 				= '';
	    $data['isSuccessfulCharge'] 		= 1;
	    $data['paymentTypeId'] 				= 1;
	    $data['paymentAmount'] 				= 1385;
	    $data['ticketId'] 					= 132654;
	    $data['userId'] 					= 1197768;
	    $data['userPaymentSettingId'] 		= '';
	    $data['paymentProcessorId'] 		= 1;
	    $data['ppFirstName'] 				= 'Wofford';
	    $data['ppLastName'] 				= 'III';
	    $data['ppBillingAddress1'] 			= '5421 Princess St';
	    $data['ppBillingCity'] 				= 'Charlotte';
	    $data['ppBillingState'] 			= 'NC';
	    $data['ppBillingZip'] 				= 28269;
		$data['ppBillingCountry'] 			= 'US';
	    $data['ppCardNumLastFour'] 			= 5796;
	    $data['ppExpMonth'] 				= 10;
	    $data['ppExpYear'] 					= 2012;
	    $data['ppBillingAmount'] 			= 1415;
	    $data['autoProcessed'] 				= 0;
	    $data['initials'] 					= 'USERCHECKOUT';
		
		$this->PaymentDetail->create();
		if (!$this->PaymentDetail->save($data)) {
			echo '1111111111';	
		} else {
			echo '22222222222';	
		}
		die('asdfs');	
	}

	// ----------------------------------------------------------
	// NO ONE IS ALLOWED TO EDIT OR DELETE PAYMENT DETAIL RECORDS
	// ----------------------------------------------------------
	
	/*

	function edit($id = null) {	
		$this->Session->setFlash(__('Access Denied - You cannot perform that operation.', true), 'default', array(), 'error');
		$this->redirect(array('action'=>'index'));
		die('ACCESS DENIED');
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PaymentDetail', true));
			$this->redirect(array('action'=>'index'));
		}		
		if (!empty($this->data)) {
			if ($this->PaymentDetail->save($this->data)) {
				$this->Session->setFlash(__('The PaymentDetail has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The PaymentDetail could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PaymentDetail->read(null, $id);
		}
		$this->set('paymentProcessorIds', $this->PaymentDetail->PaymentProcessor->find('list'));
		$this->set('paymentTypeIds', $this->PaymentDetail->PaymentType->find('list'));
	}

	function delete($id = null) {
		$this->Session->setFlash(__('Access Denied - You cannot perform that operation.', true), 'default', array(), 'error');
		$this->redirect(array('action'=>'index'));
		die('ACCESS DENIED');
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for PaymentDetail', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PaymentDetail->del($id)) {
			$this->Session->setFlash(__('PaymentDetail deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	*/
}
?>