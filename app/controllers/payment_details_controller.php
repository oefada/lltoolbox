<?php

App::import('Vendor', 'aes.php');
App::import('Vendor', 'nusoap_client/lib/nusoap');
require(APP.'/vendors/pp/Processor.class.php');  

class PaymentDetailsController extends AppController {

	var $name = 'PaymentDetails';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
	var $uses = array('PaymentDetail', 'Ticket', 'UserPaymentSetting', 'PpvNotice', 'Country', 'Track', 'TrackDetail', 'User','creditTracking');

	function beforeFilter() {
		parent::beforeFilter();
		
		$currentUser = $this->LdapAuth->user();		
		if (in_array('Accounting',$currentUser['LdapUser']['groups']) || in_array('concierge',$currentUser['LdapUser']['groups']) || in_array('Geeks',$currentUser['LdapUser']['groups'])) {
			$this->canSave = true;
		}
		
		$this->set('canSave',$this->canSave);
	}

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
			switch ($this->data['PaymentDetail']['paymentTypeId']) {
				case "2":
					$this->data['PaymentDetail']['paymentProcessorId'] = 6;
					break;
				case "3":
					$this->data['PaymentDetail']['paymentProcessorId'] = 6; 
					break;
				case "4":
					$this->data['PaymentDetail']['paymentProcessorId'] = 7;
					break;
				default:
					break;
			}
			
        	//$webservice_live_url = Configure::read("Url.Ws").'/web_service_tickets?wsdl';
			$webservice_live_url = 'http://toolbox.luxurylink.com/web_service_tickets?wsdl';
			$webservice_live_method_name = 'processPaymentTicket';
			$webservice_live_method_param = 'in0';
			
			$this->User->recursive = -1;
			$userData = @$this->User->read(null, $this->data['PaymentDetail']['userId']);
			$ticketId = @$this->data['PaymentDetail']['ticketId'];
			
			$data = array();
	        $data['userId']                 = $this->data['userId'];
	        $data['ticketId']               = $this->data['ticketId'];
	        $data['paymentProcessorId']     = $this->data['PaymentDetail']['paymentProcessorId'];
			$data['paymentTypeId']     		= $this->data['PaymentDetail']['paymentTypeId'];
	        $data['paymentAmount']          = $this->data['PaymentDetail']['paymentAmount'];
	        $data['initials']               = $this->data['PaymentDetail']['initials'];
			$data['ppTransactionId']		= $this->data['PaymentDetail']['ppTransactionId'];
			$data['firstName']				= $userData['User']['firstName'];
			$data['lastName']				= $userData['User']['lastName'];

	        $data['autoCharge']             = 0;
	        $data['saveUps']                = 0;
	        $data['toolboxManualCharge']	= 'toolbox';
			
	        if (!$data['initials']) {
	        	$data['initials'] = 'MANUALTOOLBOX';	
	        }
			
	        $data['zAuthHashKey']           = md5('L33T_KEY_LL' . $data['userId'] . $data['ticketId'] . $data['paymentProcessorId'] . $data['paymentAmount'] . $data['initials']);

			$data_json_encoded = json_encode($data);
			$soap_client = new nusoap_client($webservice_live_url, true);

			if ($this->data['PaymentDetail']['paymentProcessorId'] == 5) {
				// for wire transfers only
				// --------------------------------------------------------
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
				}
	        	$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $ticketId));
				die();
				// --------------------------------------------------------
			} elseif ($this->data['PaymentDetail']['paymentProcessorId'] == 6) {
				// Credit on file or gift
				$paymentResponse = $soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));
			} elseif ($this->data['PaymentDetail']['paymentProcessorId'] == 7) {
				// Promo codes
				
				$this->loadModel('PromoCode');
				$code = $this->PromoCode->findBypromoCode($this->data['PaymentDetail']['ppTransactionId']);
				
				$this->PaymentDetail->Ticket->PromoTicketRel->create();
				$this->PaymentDetail->Ticket->PromoTicketRel->save(array(
						'ticketId' => $this->data['ticketId'],
						'userId'   => $this->data['userId'],
						'promoCodeId' => $code['PromoCode']['promoCodeId']
				));
				
				$this->redirect("/tickets/".$this->data['ticketId']."/payment_details/add");
			} elseif ($this->data['PaymentDetail']['paymentProcessorId'] == 1) {
				// Credit card
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

				$data['saveUps']                = $saveNewCard;
				$badPaymentRequest = false;	
				
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
					$data_json_encoded = json_encode($data);
		        } elseif ($this->data['PaymentDetail']['userPaymentSettingId']) {
		        	$data['userPaymentSettingId'] = $this->data['PaymentDetail']['userPaymentSettingId'];
					$data_json_encoded = json_encode($data);
		        } else {
			        $badPaymentRequest = true;
			    }
	
		        if (!$badPaymentRequest) {
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
						
						$error = $paymentResponse;
		        	} else {
		        		if(in_array($ticketRead['Ticket']['offerId'], array(3,4)) ) {
		        			$ticketData['ticketId'] = $ticketId;
							$webservice_live_method_name = 'autoSendXnetCCDeclined';
							$webservice_live_method_param = 'in0';
							$data_json_encoded = json_encode($ticketData);
							$soap_client = new nusoap_client($webservice_live_url, true);
							$soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));
		        		}
		        	}
		        } else {
		        	$error = "BAD_PAYMENT";
		        }
	        } else {
	        	$error = "NO_ACCT";
	        }
			
			if ($error) {
				echo $error;
			} elseif ($paymentResponse != "CHARGE_SUCCESS") {
				echo $paymentResponse;
			}
			
			exit;
		}

		// NO POST BELOW -- GRAB DATA 

		//$this->PaymentDetail->Ticket->recursive = 0;
		
		$params = array(
			'conditions' => array(
				'Ticket.ticketId' => $this->params['ticketId'],
			),
			'contain' => array(
				'Package',
				'PaymentDetail',
				'User' => array(
					'UserPaymentSetting'
				),
			),
		);
		
		$ticket = $this->PaymentDetail->Ticket->find('first',$params);

		$ticket['UserPromo'] = $this->Ticket->getPromoGcCofData($ticket['Ticket']['ticketId'], $ticket['Ticket']['billingPrice']);
		
		$paymentProcessors = $this->PaymentDetail->PaymentProcessor->find('list', array('conditions' => array('sites LIKE' => '%'.$ticket['Ticket']['siteId'].'%')));
		$paymentTypeIds = $this->PaymentDetail->PaymentType->find('list');
		
		$fee = $this->Ticket->getFeeByTicket($ticket['Ticket']['ticketId']);
		
		// Ajax to get payment in add.ctp
		if (isset($this->params['url']['get_payment']) && $this->params['url']['get_payment'] != "" && isset($ticket['UserPromo'][$this->params['url']['get_payment']]['totalAmountOff'])) {
			$payment_amt = $ticket['UserPromo'][$this->params['url']['get_payment']]['totalAmountOff'];
			if ($payment_amt < 0) {
				$payment_amt = $ticket['UserPromo']['final_price_actual'];	
			}
		} else {
			$payment_amt = $ticket['UserPromo']['final_price_actual'];
		}

		if (isset($this->params['url']['get_payment'])) {
			echo json_encode(array('payment_amt' => $payment_amt,'total_payments' => $ticket['UserPromo']['payments'], 'balance' => $ticket['UserPromo']['final_price_actual']));
			exit;
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
		
		/*
		if (!in_array($initials_user, array('rvella','cholland','bjensen','kferson'))) {
			$this->Session->setFlash("Only accounting and tech can access this page. Please contact Chris or Rob.");
			$this->redirect($_SERVER['HTTP_REFERER']);
		}
		*/
					
		if (in_array($initials_user, array('rvella','cholland','bjensen','kferson'))) {
			if (!empty($ticket['User']['UserPaymentSetting'])) {
				foreach ($ticket['User']['UserPaymentSetting'] as $ups_key => $ups) {
					$cc_full = $this->PaymentDetail->query('SELECT ccNumber FROM userPaymentSetting WHERE userPaymentSettingId = ' . $ups['userPaymentSettingId']);				
					$ticket['User']['UserPaymentSetting'][$ups_key]['ccNumber'] = aesFullDecrypt($cc_full[0]['userPaymentSetting']['ccNumber']);
				}
			}
		}
		
		if (isset($ticket['UserPromo']['Promo']) && $ticket['UserPromo']['Promo']['applied'] == 1) {
			unset($paymentTypeIds[4]);
		}

		$this->set('ticket', $ticket);
		$this->set('countries', $this->Country->find('list'));
		$this->set('selectExpMonth', $selectExpMonth);
		$this->set('selectExpYear', $selectExpYear);
		$this->set('userPaymentSetting', (isset($ticket['User']['UserPaymentSetting']) ? $ticket['User']['UserPaymentSetting'] : array()));
		$this->set('paymentTypeIds', $paymentTypeIds);
		$this->set('paymentProcessorIds', $paymentProcessors);		
		$this->set('initials_user', $initials_user);
		$this->set('nocollapse',1);
		
		if (isset($this->params['url']['payments_applied'])) {
			$this->render("payments_applied","ajax");
		} elseif (isset($this->params['url']['existing_cards'])) {
			$this->render("existing_cards","ajax");
		}
	}
}

?>
