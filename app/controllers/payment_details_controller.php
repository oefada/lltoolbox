<?php

App::import('Vendor', 'aes.php');
App::import('Vendor', 'nusoap_client/lib/nusoap');
require(APP.'/vendors/pp/Processor.class.php');  

class PaymentDetailsController extends AppController {

	var $name = 'PaymentDetails';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
	var $uses = array('PaymentDetail', 'Ticket', 'UserPaymentSetting', 'PpvNotice', 'Country', 'Track', 'TrackDetail', 'User','creditTracking');

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
			if ($this->data['PaymentDetail']['paymentProcessorId'] == 5) {
				// for wire transfers only
				// --------------------------------------------------------
				$userData = $this->User->read(null, $this->data['PaymentDetail']['userId']);
				$ticketId = $this->data['PaymentDetail']['ticketId'];
	
				$this->data['PaymentDetail']['ccType'] 		 			= 'WT';
				$this->data['PaymentDetail']['userPaymentSettingId'] 	= '';
				$this->data['PaymentDetail']['isSuccessfulCharge']		= 1;
				$this->data['PaymentDetail']['autoProcessed']			= 0;
				$this->data['PaymentDetail']['ppResponseDate']			= date('Y-m-d H:i:s', strtotime('now'));
				$this->data['PaymentDetail']['ppBillingAmount']			= $this->data['PaymentDetail']['paymentAmount'];	
				$this->data['PaymentDetail']['ppCardNumLastFour']		= 'WIRE';
				$this->data['PaymentDetail']['ppExpMonth']				= 'WT';
				$this->data['PaymentDetail']['ppExpYear']				= 'WIRE';
				$this->data['PaymentDetail']['ppFirstName']				= $userData['User']['firstName'];
				$this->data['PaymentDetail']['ppLastName']				= $userData['User']['lastName'];
				$this->data['PaymentDetail']['ppBillingAddress1']		= 'WIRE';
				$this->data['PaymentDetail']['ppBillingCity']			= 'WIRE';
				$this->data['PaymentDetail']['ppBillingState']			= 'WIRE';
				$this->data['PaymentDetail']['ppBillingZip']			= 'WIRE';
				$this->data['PaymentDetail']['ppBillingCountry']		= 'WIRE';

				if ($this->PaymentDetail->save($this->data['PaymentDetail'])) {
					// update ticket status to FUNDED
					// ---------------------------------------------------------------------------
					$ticketStatusChange = array();
					$ticketStatusChange['ticketId'] = $ticketId;
					$ticketStatusChange['ticketStatusId'] = 5;
					$this->Ticket->save($ticketStatusChange);
		
					// allocate revenue to loa and tracks
					// ---------------------------------------------------------------------------
					$tracks = $this->TrackDetail->getTrackRecord($ticketId);
					if (!empty($tracks)) {
						foreach ($tracks as $track) {
							// track detail stuff and allocation
							// ---------------------------------------------------------------------------
							$trackDetailExists = $this->TrackDetail->findExistingTrackTicket($track['trackId'], $ticketId);	
							if (!$trackDetailExists) {
								// decrement loa number of packages
								// ---------------------------------------------------------------------------
								if ($track['expirationCriteriaId'] == 2) {
									$this->Ticket->query('UPDATE loa SET numberPackagesRemaining = numberPackagesRemaining - 1 WHERE loaId = ' . $track['loaId'] . ' LIMIT 1');
								} elseif ($track['expirationCriteriaId'] == 4) {
									$this->Ticket->query('UPDATE loa SET membershipPackagesRemaining = membershipPackagesRemaining - 1 WHERE loaId = ' . $track['loaId'] . ' LIMIT 1');
								}
								$new_track_detail = $this->TrackDetail->getNewTrackDetailRecord($track, $ticketId);
								if ($new_track_detail) {
									$this->TrackDetail->create();
									$this->TrackDetail->save($new_track_detail);
								}
							}
						}
					}
	        		$this->Session->setFlash(__('Payment was successfully recorded (wire transfer)', true), 'default', array(), 'success');
				} else {
	        		$this->Session->setFlash(__('Payment was not recorded (wire transfer)', true), 'default', array(), 'error');
				}
	        	$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $ticketId));
				die();
				// --------------------------------------------------------
			}

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
	        	if (stristr($_SERVER['HTTP_HOST'], 'dev') || stristr($_SERVER['HTTP_HOST'], 'stage')) {
	        		die('NO PAYMENT ALLOWED ON DEV');	
	        	}
	        	$webservice_live_url = 'http://toolbox.luxurylink.com/web_service_tickets?wsdl';
				$webservice_live_method_name = 'processPaymentTicket';
				$webservice_live_method_param = 'in0';
				
				$data_json_encoded = json_encode($data);
				$soap_client = new nusoap_client($webservice_live_url, true);
        		$paymentResponse = $soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));

        		$this->Ticket->recursive = -1;
				$ticketRead = $this->Ticket->read(null, $ticketId);
	        	if (trim($paymentResponse) == 'CHARGE_SUCCESS') {	        		
					if(in_array($ticketRead['Ticket']['offerId'], array(3,4)) ) {
						$ticketData['ticketId'] = $ticketId;
						$webservice_live_method_name = 'autoSendXnetDatesConfirmedOnlyProperty';
						$webservice_live_method_param = 'in0';
						$data_json_encoded = json_encode($ticketData);
						$soap_client = new nusoap_client($webservice_live_url, true);
						$soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));
					}	
	        		
	        		$this->Session->setFlash(__('Payment was successfully charged.', true), 'default', array(), 'success');
	        		$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $this->data['PaymentDetail']['ticketId']));
	        	} else {
	        		if(in_array($ticketRead['Ticket']['offerId'], array(3,4)) ) {
	        			$ticketData['ticketId'] = $ticketId;
						$webservice_live_method_name = 'autoSendXnetCCDeclined';
						$webservice_live_method_param = 'in0';
						$data_json_encoded = json_encode($ticketData);
						$soap_client = new nusoap_client($webservice_live_url, true);
						$soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));
	        		}
	        		
	        		$this->Session->setFlash(__('Payment Not Processed -- Error ' . $paymentResponse, true), 'default', array(), 'error');
	        	}
	        } else {
	        	$this->Session->setFlash(__('No payment account selected. If using new card, must select the Use New Card checkbox', true), 'default', array(), 'error');
	        }
		}

		// NO POST BELOW -- GRAB DATA 

		$this->PaymentDetail->Ticket->recursive = 2;
		$ticket = $this->PaymentDetail->Ticket->read(null, $this->params['ticketId']);
		$ticket['Ticket']['totalBillingAmount'] = $ticket['Ticket']['billingPrice'];

		$ticket['UserPromo'] = $this->Ticket->getPromoGcCofData($ticket['Ticket']['ticketId'], $ticket['Ticket']['billingPrice']);
		if (!empty($ticket['UserPromo']['Promo']) && !empty($ticket['UserPromo']['Promo']['applied'])) {
			$ticket['Ticket']['totalBillingAmount'] -= $ticket['UserPromo']['Promo']['totalAmountOff'];
		}
		$fee = $this->Ticket->getFeeByTicket($ticket['Ticket']['ticketId']);
		$ticket['Ticket']['totalBillingAmount'] += $fee;
	
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

		if (in_array($initials_user, array('alee','bscott','kferson','mtrinh'))) {
			if (!empty($ticket['User']['UserPaymentSetting'])) {
				foreach ($ticket['User']['UserPaymentSetting'] as $ups_key => $ups) {
					$cc_full = $this->PaymentDetail->query('SELECT ccNumber FROM userPaymentSetting WHERE userPaymentSettingId = ' . $ups['userPaymentSettingId']);				
					$ticket['User']['UserPaymentSetting'][$ups_key]['ccNumber'] = aesFullDecrypt($cc_full[0]['userPaymentSetting']['ccNumber']);
				}
			}
		}
		
		$paymentProcessors = $this->PaymentDetail->PaymentProcessor->find('list');
		if ($ticket['Ticket']['siteId'] == 2) {
			$paymentProcessors = array(3 => 'PAYPAL', 5 => 'WIRE TRANSFER');
		} else {
			$paymentProcessors = array(1 => 'NOVA', 5 => 'WIRE TRANSFER');
		}

		$credit = $this->creditTracking->find('first', array('conditions' => array('CreditTracking.userId' => $ticket['Ticket']['userId'])));
		
		$this->set('ticket', $ticket);
		$this->set('credit',$credit);
		$this->set('countries', $this->Country->find('list'));
		$this->set('selectExpMonth', $selectExpMonth);
		$this->set('selectExpYear', $selectExpYear);
		$this->set('userPaymentSetting', $ticket['User']['UserPaymentSetting']);
		$this->set('paymentTypeIds', $this->PaymentDetail->PaymentType->find('list'));
		$this->set('paymentProcessorIds', $paymentProcessors);		
		$this->set('initials_user', $initials_user);
	}
	
}
?>
