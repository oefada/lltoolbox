<?php

App::import('Vendor', 'aes.php');
App::import('Vendor', 'nusoap_client/lib/nusoap');
require(APP.'/vendors/pp/Processor.class.php');  

class PaymentDetailsController extends AppController {

	var $name = 'PaymentDetails';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
	var $uses = array('PaymentDetail', 'Ticket', 'UserPaymentSetting', 'PpvNotice', 'Country', 'Track', 'TrackDetail');

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
	        $data['toolboxManualCharge']	= 'toolbox';
	        if (!$data['initials']) {
	        	$data['initials'] = 'MANUALTOOLBOX';	
	        }
	        $data['zAuthHashKey']           = md5('L33T_KEY_LL' . $data['userId'] . $data['ticketId'] . $data['paymentProcessorId'] . $data['paymentAmount'] . $data['initials']);
	        
	        if ($usingNewCard) {
	        	$data['userPaymentSetting'] = $this->data['UserPaymentSetting'];
	        	switch (substr($data['userPaymentSetting']['ccNumber'], 0, 1)) {
	        		case 4:
	        			$ccType = 'VI';
	        			break;
	        		case 5:
	        			$ccType = 'MC';
	        			break;
	        		case 6:
	        			$ccType = 'DS';
	        			break;
	        		case 3:	
	        			$ccType = 'AX';
	        			break;
	        		default:
	        			$ccType = '';
	        			break;
	        	}
				$data['userPaymentSetting']['ccType']			= $ccType;
	        	$data['userPaymentSetting']['ccNumber'] 		= aesEncrypt($data['userPaymentSetting']['ccNumber']);
	        } elseif ($this->data['PaymentDetail']['userPaymentSettingId']) {
	        	$data['userPaymentSettingId'] = $this->data['PaymentDetail']['userPaymentSettingId'];
	        } else {
		        $badPaymentRequest = true;
		    }

	        if (!$badPaymentRequest) {
	        	$webservice_live_url = 'http://toolbox.luxurylink.com/web_service_tickets?wsdl';
	        	if (stristr($_SERVER['HTTP_HOST'], 'dev')) {
	        		die('NO PAYMENT ALLOWED ON DEV');	
	        	}
	        	$webservice_live_url = 'http://toolbox.luxurylink.com/web_service_tickets?wsdl';
				$webservice_live_method_name = 'processPaymentTicket';
				$webservice_live_method_param = 'in0';
				
				$data_json_encoded = json_encode($data);
				$soap_client = new nusoap_client($webservice_live_url, true);
        		$paymentResponse = $soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));

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

		// NO POST BELOW -- GRAB DATA 

		$this->PaymentDetail->Ticket->recursive = 2;
		$ticket = $this->PaymentDetail->Ticket->read(null, $this->params['ticketId']);
		$ticket['Ticket']['totalBillingAmount'] = in_array($ticket['Ticket']['offerTypeId'], array(1,2,6)) ? 30 : 40;
		$ticket['Ticket']['totalBillingAmount'] += $ticket['Ticket']['billingPrice'];
		$promo = $this->Ticket->getTicketOfferPromo($ticket['Ticket']['ticketId']);
		if ($promo && isset($promo['opc']['promoAmount']) && is_numeric($promo['opc']['promoAmount'])) {
			$ticket['Ticket']['totalBillingAmount'] -= $promo['opc']['promoAmount'];
			$ticket['Promo'] = $promo;
		}
		
		$selectExpMonth = array();
		for ($i = 1; $i < 13; $i++) {
			$se_m = str_pad($i, 2, '0', STR_PAD_LEFT);
			$selectExpMonth[] = $se_m;
		}
		$selectExpYear = array();
		$yearPlusSeven = date('Y', strtotime("+7 YEAR"));
		for ($i = date('Y'); $i <= $yearPlusSeven; $i++) {
			$selectExpYear[] = $i;	
		}

		if (isset($_SESSION['Auth']['AdminUser']['mailnickname'])) {
			$initials_user = $_SESSION['Auth']['AdminUser']['mailnickname'];
		} else {
			$initials_user = false;
		}

		if (in_array($initials_user, array('cyoung','alee','bscott'))) {
			if (!empty($ticket['User']['UserPaymentSetting'])) {
				foreach ($ticket['User']['UserPaymentSetting'] as $ups_key => $ups) {
					$cc_full = $this->PaymentDetail->query('SELECT ccNumber FROM userPaymentSetting WHERE userPaymentSettingId = ' . $ups['userPaymentSettingId']);				
					$ticket['User']['UserPaymentSetting'][$ups_key]['ccNumber'] = aesFullDecrypt($cc_full[0]['userPaymentSetting']['ccNumber']);
				}
			}
		}

		$this->set('ticket', $ticket);
		$this->set('countries', $this->Country->find('list'));
		$this->set('selectExpMonth', $selectExpMonth);
		$this->set('selectExpYear', $selectExpYear);
		$this->set('userPaymentSetting', $ticket['User']['UserPaymentSetting']);
		$this->set('paymentTypeIds', $this->PaymentDetail->PaymentType->find('list'));
		$this->set('paymentProcessorIds', $this->PaymentDetail->PaymentProcessor->find('list'));		
		$this->set('initials_user', $initials_user);
	}
	
}
?>
