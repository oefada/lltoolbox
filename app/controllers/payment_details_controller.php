<?php

App::import('Vendor', 'aes.php');
App::import('Vendor', 'nusoap_client/lib/nusoap');
require(APP.'/vendors/pp/Processor.class.php');  

class PaymentDetailsController extends AppController {

	var $name = 'PaymentDetails';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
	var $uses = array('PaymentDetail', 'Ticket', 'UserPaymentSetting', 'PpvNotice');

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
	        $data['paymentAmount']          = $this->data['PaymentDetail']['paymentAmount'];
	        $data['initials']               = $this->data['PaymentDetail']['initials'];
	        $data['autoCharge']             = 0;
	        $data['saveUps']                = $saveNewCard;
	        if (!$data['initials']) {
	        	$data['initials'] = 'MANUALTOOLBOX';	
	        }
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
	        	$paymentResponse = $this->processPaymentTicket(json_encode($data));
	        	if (trim($paymentResponse) == 'CHARGE_SUCCESS') {
	        		$this->Session->setFlash(__('Payment was successfully charged.', true), 'default', array(), 'success');
	        		$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $this->data['PaymentDetail']['ticketId']));
	        	} else {
	        		$this->Session->setFlash(__('Payment Not Processed -- Error ' . $paymentResponse, true), 'default', array(), 'error');
	        	}
	        } else {
	        	$this->Session->setFlash(__('No payment account selected. If using new card, must select the Use New Card checkbox', true), 'default', array(), 'error');
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
	
	function processPaymentTicket($in0) {
		// ---------------------------------------------------------------------------
		// SUBMIT PAYMENT VIA PROCESSOR 
		// ---------------------------------------------------------------------------
		// REQUIRED: (1) userId
		//           (2) ticketId
		//			 (3) paymentProcessorId
		// 			 (4) paymentAmount
		//           (5) initials
		//			 (6) autoCharge
		//           (7) saveUps
		//			 (8) zAuthHashKey
		//           (9) userPaymentSettingId or userPaymentSetting data array
		//
		// SEND TO PAYMENT PROCESSOR: $userPaymentSettingPost
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
		if (!isset($data['zAuthHashKey']) || !$data['zAuthHashKey']) {
			return '108';	
		}
		
		// also check the hash for more security
		// ---------------------------------------------------------------------------
		$hashCheck = md5('L33T_KEY_LL' . $data['userId'] . $data['ticketId'] . $data['paymentProcessorId'] . $data['paymentAmount'] . $data['initials']);
		if (trim($hashCheck) !== trim($data['zAuthHashKey'])) {
			return '109';	
		}
		unset($hashCheck);
		
		// and even some more error checking.
		// ---------------------------------------------------------------------------
		$this->Ticket->recursive = -1;
		$ticket = $this->Ticket->read(null, $data['ticketId']);
		if (!$ticket) {
			return '110';
		} 
		if ($ticket['Ticket']['userId'] != $data['userId']) {
			return '111';
		}

		// use either the data sent over or retrieve from the db with the id
		// ---------------------------------------------------------------------------
		$userPaymentSettingPost = array();
		
		$usingUpsId = false;
		if (isset($data['userPaymentSettingId']) && !empty($data['userPaymentSettingId']) && is_numeric($data['userPaymentSettingId'])) {
			$tmp_result = $this->Ticket->query('SELECT * FROM userPaymentSetting WHERE userPaymentSettingId = ' . $data['userPaymentSettingId'] . ' LIMIT 1');
			$userPaymentSettingPost['UserPaymentSetting'] = $tmp_result[0]['userPaymentSetting'];
			unset($tmp_result);
			$usingUpsId = true;
		} else {
			$userPaymentSettingPost['UserPaymentSetting'] = $data['userPaymentSetting'];	
		}
		
		if (!$userPaymentSettingPost || empty($userPaymentSettingPost)) {
				return '113';
		}
		
		$userPaymentSettingPost['UserPaymentSetting']['ccNumber'] = aesFullDecrypt($userPaymentSettingPost['UserPaymentSetting']['ccNumber']);
		
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
		
		if (!$paymentProcessorName) {
			return '114';	
		}
		
		// only for toolbox area -- allow payment override
		// ---------------------------------------------------------------------------
		$ticket['Ticket']['billingAmountOverride'] = $data['paymentAmount'];
		
		// init payment processing and submit payment
		// ---------------------------------------------------------------------------
		$processor = new Processor($paymentProcessorName);
		$processor->InitPayment($userPaymentSettingPost, $ticket);	
		$processor->SubmitPost();  

		// save the response from the payment processor
		// ---------------------------------------------------------------------------
		$nameSplit 								= str_word_count($userPaymentSettingPost['UserPaymentSetting']['nameOnCard'], 1);
		$firstName 								= trim($nameSplit[0]);
		$lastName 								= trim(array_pop($nameSplit));
		$userPaymentSettingPost['UserPaymentSetting']['expMonth'] = str_pad($userPaymentSettingPost['UserPaymentSetting']['expMonth'], 2, '0', STR_PAD_LEFT);
		
		$paymentDetail 							= array();
		$paymentDetail 							= $processor->GetMappedResponse();
		$paymentDetail['paymentTypeId'] 		= 1; 
		$paymentDetail['paymentAmount']			= $data['paymentAmount'];
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
			mail('devmail@luxurylink.com', 'WEB SERVICE ERROR: PAYMENT PROCESSED BUT NOT SAVED', print_r($this->PaymentDetail->validationErrors,true)  . print_r($paymentDetail, true));
		}
				
		// return result whether success or denied
		// ---------------------------------------------------------------------------
		if ($processor->ChargeSuccess()) {
			// if saving new user card information
			// ---------------------------------------------------------------------------
			if ($data['saveUps'] && !$usingUpsId && !empty($userPaymentSettingPost['UserPaymentSetting'])) {
				$this->UserPaymentSetting->create();
				$this->UserPaymentSetting->save($userPaymentSettingPost['UserPaymentSetting']);
			}
			
			$ticketStatusChange = array();
			$ticketStatusChange['ticketId'] = $ticket['Ticket']['ticketId'];
			$ticketStatusChange['ticketStatusId'] = 5;
			$this->Ticket->save($ticketStatusChange);
			
			return 'CHARGE_SUCCESS';
		} else {
			return $processor->GetResponseTxt();
		}
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