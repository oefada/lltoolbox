<?php

//ini_set('max_execution_time', 0);
//ini_set('memory_limit', '256M');

App::import('Vendor', 'nusoap_client/lib/nusoap');
App::Import('Vendor', 'aes.php');

class TicketsController extends AppController {

	var $name = 'Tickets';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');

	var $uses = array('Ticket','OfferType', 'Format', 'User', 'ClientLoaPackageRel', 
					  'Track', 'TrackDetail','Offer','Loa','Client', 'OfferLive', 
					  'Reservation', 'PromoTicketRel', 'Promo', 'PromoCode'
					  );

	function index() {

		// set search criteria from form post or set defaults
		$form = $this->params['form'];
		$named = $this->params['named'];

		// ajaxed paginated form elements come in via params['named']
		if (empty($form) && !empty($named)) {
			$form = $named;
			$this->params['form'] = $this->params['named'];
		}
		
		// set values and set defaults        
		$s_ticket_id = isset($form['s_ticket_id']) ? $form['s_ticket_id'] : '';
		$s_offer_id = isset($form['s_offer_id']) ? $form['s_offer_id'] : '';
		$s_user_id = isset($form['s_user_id']) ? $form['s_user_id'] : '';
		$s_format_id = isset($form['s_format_id']) ? $form['s_format_id'] : '';
		$s_client_id = isset($form['s_client_id']) ? $form['s_client_id'] : '';
		$s_bid_id = isset($form['s_bid_id']) ? $form['s_bid_id'] : '';
		$s_request_queue_id = isset($form['s_request_queue_id']) ? $form['s_request_queue_id'] : '';
		$s_package_id = isset($form['s_package_id']) ? $form['s_package_id'] : '';
		$s_promo_code = isset($form['s_promo_code']) ? $form['s_promo_code'] : '';
		$s_offer_type_id = isset($form['s_offer_type_id']) ? $form['s_offer_type_id'] : 0;
		$s_ticket_status_id = isset($form['s_ticket_status_id']) ? $form['s_ticket_status_id'] : 0;
		$s_res_confirmation_num = isset($form['s_res_confirmation_num']) ? $form['s_res_confirmation_num'] : '';
		$s_res_check_in_date = isset($form['s_res_check_in_date']) ? $form['s_res_check_in_date'] : '';
		$s_has_promo = isset($form['s_has_promo']) ? $form['s_has_promo'] : '';
		$s_start_y = isset($form['s_start_y']) ? $form['s_start_y'] : date('Y');
		$s_start_m = isset($form['s_start_m']) ? $form['s_start_m'] : date('m');
		$s_start_d = isset($form['s_start_d']) ? $form['s_start_d'] : date('d');
		$s_end_y = isset($form['s_end_y']) ? $form['s_end_y'] : date('Y');
		$s_end_m = isset($form['s_end_m']) ? $form['s_end_m'] : date('m');
		$s_end_d = isset($form['s_end_d']) ? $form['s_end_d'] : date('d');

		if (isset($_GET['searchClientId'])) {
			$s_client_id = $_GET['searchClientId'];		
		}
		
		if (isset($_GET['searchUserId'])) {
			$s_user_id = $_GET['searchUserId'];		
		}

		if (isset($_GET['query'])) {
			$query = $_GET['query'];
			if (is_numeric($query)) {
				$s_ticket_id = $_GET['query'];			
			} 
		}
		
		$allowed_query_keys = array('s_ticket_status_id', 's_format_id', 's_offer_type_id');
		foreach ($this->params['url'] as $key => $value) {
			if (in_array($key, $allowed_query_keys)) {
				$$key = $value;
				$this->params['form'][$key] = $value;
			}
		}
	
		if ($s_res_check_in_date || $s_res_confirmation_num) {
			$s_has_reservation = true;
		} else {
			$s_has_reservation = false;
		}

		// use these dates in the sql for date range search
		$s_start_date = $s_start_y . '-' . $s_start_m . '-' . $s_start_d . ' 00:00:00';
		$s_end_date = $s_end_y . '-' . $s_end_m . '-' . $s_end_d . ' 23:59:59';

		$this->paginate = array('fields' => array(
									'Ticket.ticketId', 'Ticket.offerTypeId', 'Ticket.created', 'Ticket.bidId',  
									'Ticket.offerId', 'Ticket.userId', 'TicketStatus.ticketStatusName', 'Ticket.packageId', 'Ticket.requestQueueId',
									'Ticket.userFirstName', 'Ticket.userLastName', 'Ticket.packageId', 'Ticket.billingPrice', 'Ticket.formatId', 'Ticket.ticketNotes',
									'PpvNotice.emailSentDatetime'
									),
		                        'contain' => array('TicketStatus'),
		                        'order' => array(
		                        	'Ticket.ticketId' => 'desc'
		                        	),
		                        'limit' => 50,
								'joins' => array(
												array(
						           				'table' => 'ppvNotice', 
									            'alias' => 'PpvNotice', 
									            'type' => 'left',  
									            'conditions'=> array('PpvNotice.ticketId = Ticket.ticketId', 'PpvNotice.ppvNoticeTypeId IN (2,10)') 
												)
										),
								'group' => array('Ticket.ticketId')
		                        );
		    
		$single_search = true;
		$single_search_override = false;
		// if search via ticket id, offer id, or user id, then dont use other search conditions
		if ($s_ticket_id) {
			$this->paginate['conditions']['Ticket.ticketId'] = $s_ticket_id;    
		} elseif ($s_ticket_status_id == 3 && $s_format_id ==1 ) {
			$this->paginate['conditions']['Ticket.formatId'] = 1;
			$this->paginate['conditions']['Ticket.ticketStatusId'] = 3;
			$this->paginate['order'] = array('PpvNotice.emailSentDatetime' => 'desc');
			$single_search_override = true;
		} elseif ($s_offer_id) {
			$this->paginate['conditions']['Ticket.offerId'] = $s_offer_id;    
		} elseif ($s_user_id) {
			$this->paginate['conditions']['Ticket.userId'] = $s_user_id;       
		} elseif ($s_bid_id) {
			$this->paginate['conditions']['Ticket.bidId'] = $s_bid_id;       
		} elseif ($s_client_id) {
			$this->paginate['joins'][] = 
				array( 
		            'table' => 'clientLoaPackageRel', 
		            'alias' => 'ClientLoaPackageRel', 
		            'type' => 'inner',  
		            'conditions'=> array('ClientLoaPackageRel.packageId = Ticket.packageId') 
			        );

			$this->paginate['joins'][] = 
		        array( 
		            'table' => 'client', 
		            'alias' => 'Client', 
		            'type' => 'inner',  
		            'conditions'=> array( 
		       	   	   'Client.clientId = ClientLoaPackageRel.clientId', 
			           'Client.clientId' => $s_client_id 
					   )
					);
			$this->paginate['group'] = array('Ticket.ticketId');
		} elseif ($s_request_queue_id) {
			$this->paginate['conditions']['Ticket.requestQueueId'] = $s_request_queue_id;    
		} elseif ($s_package_id) {
			$this->paginate['conditions']['Ticket.packageId'] = $s_package_id;    
		} elseif ($s_promo_code) {		
			$promoCodeResult = $this->PromoCode->findBypromoCode($s_promo_code);
			$s_promo_code_id = $promoCodeResult['PromoCode']['promoCodeId'];
			$this->paginate['contain'][] = 'PromoTicketRel';
			$this->paginate['group'] = array('Ticket.ticketId');
			$this->paginate['joins'][] = 
							array(
		           				'table' => 'promoTicketRel', 
					            'alias' => 'PromoTicketRel', 
					            'type' => 'inner',  
					            'conditions'=> array('PromoTicketRel.ticketId = Ticket.ticketId') 
								);
			$this->paginate['conditions']['PromoTicketRel.promoCodeId'] = $s_promo_code_id;
		} else {    
			$single_search = false;
			$this->paginate['conditions']['Ticket.created BETWEEN ? AND ?'] = array($s_start_date, $s_end_date);             		
			if ($s_offer_type_id) {
				$this->paginate['conditions']['Ticket.offerTypeId'] = $s_offer_type_id;	
			}
			if ($s_format_id) {
				$this->paginate['conditions']['Ticket.formatId'] = $s_format_id;	
			}
			if ($s_ticket_status_id) {
				$this->paginate['conditions']['Ticket.ticketStatusId'] = $s_ticket_status_id;	
				if ($s_ticket_status_id == 3) {
		        	$this->paginate['order'] = array('PpvNotice.emailSentDatetime' => 'desc');
				}
			}
			if ($s_has_reservation) {
				$this->paginate['contain'][] = 'Reservation';
				$this->paginate['group'] = array('Ticket.ticketId');
				$this->paginate['conditions']['Reservation.reservationId > '] = 0;
				if ($s_res_confirmation_num) {
					$this->paginate['conditions']['Reservation.reservationConfirmNum'] = trim($s_res_confirmation_num);
					$single_search = true;
					unset($this->paginate['conditions']['Ticket.created BETWEEN ? AND ?']);
				}
				if ($s_res_check_in_date) {
					$this->paginate['conditions']['Reservation.arrivalDate BETWEEN ? AND ?'] = array($s_start_date, $s_end_date);             		
				}
			}
			if ($s_has_promo) {
				$this->paginate['contain'][] = 'PromoTicketRel';
				$this->paginate['group'] = array('Ticket.ticketId');
				$this->paginate['joins'][] = 
								array(
			           				'table' => 'promoTicketRel', 
						            'alias' => 'PromoTicketRel', 
						            'type' => 'inner',  
						            'conditions'=> array('PromoTicketRel.ticketId = Ticket.ticketId') 
								);
				$this->paginate['conditions']['PromoTicketRel.promoCodeId > '] = 0;
			}
		}
	
		if (!$single_search) {
			$s_ticket_id = $s_offer_id = $s_user_id = $s_bid_id = $s_client_id = $s_request_queue_id = $s_package_id = $s_res_confirmation_num = null;
		} else {
			$s_res_check_in_date = $s_offer_type_id = $s_has_promo = null;
			if (!$single_search_override) {
				$s_ticket_status_id = $s_format_id = null;
			}
			$s_start_y = $s_end_y = date('Y');
			$s_start_m = $s_end_m = date('m');
			$s_start_d = $s_end_d = date('d');
		}
		
		$this->set('s_ticket_id', $s_ticket_id);
		$this->set('s_offer_id', $s_offer_id);
		$this->set('s_user_id', $s_user_id);
		$this->set('s_client_id', $s_client_id);
		$this->set('s_bid_id', $s_bid_id);
		$this->set('s_request_queue_id', $s_request_queue_id);
		$this->set('s_package_id', $s_package_id);
		$this->set('s_promo_code', $s_promo_code);
		$this->set('s_format_id', $s_format_id);
		$this->set('s_offer_type_id', $s_offer_type_id);
		$this->set('s_ticket_status_id', $s_ticket_status_id);
		$this->set('s_res_confirmation_num', $s_res_confirmation_num);
		$this->set('s_res_check_in_date', $s_res_check_in_date);
		$this->set('s_has_promo', $s_has_promo);   
		$this->set('s_start_y', $s_start_y);   
		$this->set('s_start_m', $s_start_m);   
		$this->set('s_start_d', $s_start_d);   
		$this->set('s_end_y', $s_end_y);
		$this->set('s_end_m', $s_end_m);   
		$this->set('s_end_d', $s_end_d);   

		$tickets_index = $this->paginate();

		foreach ($tickets_index as $k => $v) {
			$tickets_index[$k]['Ticket']['validCard'] = $this->getValidCcOnFile($v['Ticket']['userId'], $v['Ticket']['bidId']);
			$tracks = $this->TrackDetail->getTrackRecord($v['Ticket']['ticketId']);
			$track = $tracks[0];
			$tickets_index[$k]['Ticket']['trackName'] = ($track['trackName']) ? $track['trackName'] : 'N/A';	
			$clients = $this->Ticket->getClientsFromPackageId($v['Ticket']['packageId']);
			$tickets_index[$k]['Promo'] = $this->Ticket->getTicketPromoData($v['Ticket']['ticketId']);
			$tickets_index[$k]['Client'] = $clients;
		}
		
		$this->set('tickets', $tickets_index);
		$this->set('format', $this->Format->find('list'));
		$this->set('offerType', $this->OfferType->find('list'));
		$this->set('ticketStatus', $this->Ticket->TicketStatus->find('list'));
	}

	function getValidCcOnFile($userId, $bidId = null) {
		$ups = $this->User->query("select * from userPaymentSetting as UserPaymentSetting where userId = $userId and inactive = 0 order by primaryCC desc, expYear desc");
		if ($bidId && is_numeric($bidId)) {
			$ups_bid = $this->User->query("select * from userPaymentSetting as UserPaymentSetting where userPaymentSettingId = (select userPaymentSettingId from bid where bidId = $bidId)");
			if (!empty($ups_bid)) {
				$ups = $ups_bid;
			}
		} 
		$year_now = date('Y');
		$month_now = date('m');
		if (empty($ups)) {
			return 'NONE';
		}
		$found_valid_cc = false;
		foreach ($ups as $k => $v) {
			if (($v['UserPaymentSetting']['expYear'] < $year_now) || ($v['UserPaymentSetting']['expYear'] == $year_now && $v['UserPaymentSetting']['expMonth'] < $month_now)) {
				continue;	
			} else {
				$found_valid_cc = true;
				break;
			}
		}
		return ($found_valid_cc) ? $v['UserPaymentSetting']['ccType'] . '-' . substr(aesDecrypt($v['UserPaymentSetting']['ccNumber']), -4, 4) : 'EXPIRED';
	}

	function view($id = null) {
				
		if (!$id) {
			$this->Session->setFlash(__('Invalid Ticket.', true), 'default', array(), 'error');
			$this->redirect(array('action'=>'index'));
		}

		$this->Ticket->recursive = 2;
		$ticket = $this->Ticket->read(null, $id);

		$ticket['Client'] = $this->Ticket->getClientsFromPackageId($ticket['Ticket']['packageId']);
		$ticket['Promo'] = $this->Ticket->getTicketPromoData($id);

		$this->set('ticket', $ticket);
		
		$this->set('offerType', $this->OfferType->find('list'));
		$this->set('ppvNoticeTypes', $this->Ticket->PpvNotice->PpvNoticeType->find('list'));
		
		$this->data = array();
		$this->data['condition1']['field'] = "Offer.offerId";
		$this->data['condition1']['value'] = $ticket['Ticket']['offerId'];
		$offer_search_serialize = serialize($this->data);
		$this->set('offer_search_serialize', $offer_search_serialize);
		
		// revenue stuff
		$tracks = $this->TrackDetail->getTrackRecord($id);
		$track = $tracks[0];
		$trackDetailExists = false;
		if ($track) {
			$trackDetailExists = $this->TrackDetail->findExistingTrackTicket($track['trackId'], $id);			
			$this->set('trackDetails', $this->TrackDetail->getAllTrackDetails($track['trackId']));	
		}
		$this->set('trackExistsCount', $trackDetailExists ? 1 : 0);
		$this->set('trackDetailExists', $trackDetailExists);
		$this->set('track', $track);
	}

	function phpinfoshow() {
		phpinfo();
		die();	
	}

	function edit($id = null) {	
		// only for updating ticket notes for now.  should not be able to update anything else.
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Ticket.', true), 'default', array(), 'error');
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data) && !empty($this->data['Ticket']['ticketId'])) {
			if ($this->Ticket->save($this->data)) {
				$this->Session->setFlash(__('The ticket note has been saved.', true), 'default', array(), 'success');
				$this->redirect(array('action'=>'view', 'id' => $id));
			} else {
				$this->Session->setFlash(__('The ticket note has not been saved due to an error.', true), 'default', array(), 'error');
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Ticket->read(null, $id);
		}
		if (isset($_SESSION['Auth']['AdminUser']['mailnickname'])) {
			$initials_user = $_SESSION['Auth']['AdminUser']['mailnickname'];
		} else {
			$initials_user = false;
		}
		
		// only ticket status edit only for CHRISTINE YOUNG
		$allow_status_edit = in_array(trim($initials_user), array('cyoung','alee')) ? true : false;
		
		$this->set('allow_status_edit', $allow_status_edit);
		$this->set('ticketStatusIds', $this->Ticket->TicketStatus->find('list'));
	}

	function add() {
		if (!empty($this->data)) {
			if (!$this->data['Ticket']['offerId'] || !is_numeric($this->data['Ticket']['offerId'])) {
				$this->Session->setFlash(__('The ticket was not created.  The offerId cannot be blank and must be a number.', true), 'default', array(), 'error');
			} elseif (!$this->data['Ticket']['userId'] || !is_numeric($this->data['Ticket']['userId'])) {
				$this->Session->setFlash(__('The ticket was not created.  The userId cannot be blank and must be a number.', true), 'default', array(), 'error');
			} elseif (!$this->data['Ticket']['billingPrice'] || !is_numeric($this->data['Ticket']['billingPrice'])) {
				$this->Session->setFlash(__('The ticket was not created.  The billingPrice cannot be blank and must be a number.', true), 'default', array(), 'error');
			} else {
				$this->User->recursive = 1;
				$this->OfferLive->recursive = -1;
				$userData = $this->User->read(null, $this->data['Ticket']['userId']);
				$offerData = $this->OfferLive->read(null, $this->data['Ticket']['offerId']);
				if (empty($userData)) {
					$this->Session->setFlash(__('The ticket was not created.  Invalid User Id.', true), 'default', array(), 'error');
				} elseif (empty($offerData)) {
					$this->Session->setFlash(__('The ticket was not created.  Invalid Offer Id.', true), 'default', array(), 'error');
				} else {
					$this->data['Ticket']['ticketStatusId'] 	= 1;
					$this->data['Ticket']['packageId'] 			= $offerData['OfferLive']['packageId'];
					$this->data['Ticket']['formatId']			= in_array($offerData['OfferLive']['offerTypeId'], array(1,2,6)) ? 1 : 2;
					$this->data['Ticket']['offerTypeId']		= $offerData['OfferLive']['offerTypeId'];
					$this->data['Ticket']['userFirstName']		= $userData['User']['firstName'];
					$this->data['Ticket']['userLastName']		= $userData['User']['lastName'];
					$this->data['Ticket']['userEmail1']			= $userData['User']['email'];
					$this->data['Ticket']['userWorkPhone']		= $userData['User']['workPhone'];
					$this->data['Ticket']['userHomePhone']		= $userData['User']['homePhone'];
					$this->data['Ticket']['userMobilePhone']	= $userData['User']['mobilePhone'];
					$this->data['Ticket']['userFax']			= $userData['User']['fax'];
					$this->data['Ticket']['userAddress1']		= $userData['Address'][0]['address1'];
					$this->data['Ticket']['userAddress2']		= $userData['Address'][0]['address2'];
					$this->data['Ticket']['userCity']			= $userData['Address'][0]['address3'];
					$this->data['Ticket']['userState']			= $userData['Address'][0]['stateName'];
					$this->data['Ticket']['userCountry']		= $userData['Address'][0]['countryText'];
					$this->data['Ticket']['userZip']			= $userData['Address'][0]['postalCode'];
					$this->data['Ticket']['ticketNotes']		= 'MANUALLY CREATED TICKET' . trim($this->data['Ticket']['ticketNotes']);

					$this->Ticket->create();
					if ($this->Ticket->save($this->data)) {
						$this->Session->setFlash(__('This manual ticket has been created successfully', true));
						$this->redirect(array('action'=>'view', 'id' => $this->Ticket->getLastInsertId()));
					} else {
						$this->Session->setFlash(__('The Ticket could not be saved. Please, try again.', true));
					}
				}
			}
		}
		$this->set('ticketStatusIds', $this->Ticket->TicketStatus->find('list'));
		$this->set('formatIds', $this->Format->find('list'));
		$this->set('offerTypeIds', $this->OfferType->find('list'));
		if (isset($_SESSION['Auth']['AdminUser']['mailnickname'])) {
			$manualTicketInitials = $_SESSION['Auth']['AdminUser']['mailnickname'];
		} else {
			$manualTicketInitials = 'MANUAL_TICKET';	
		}
		$this->data['Ticket']['manualTicketInitials'] = $manualTicketInitials;
	}

	function search()
	{
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
		}
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);
			
			$queryPieces = explode(" ", $query);
			
			$sqlquery = '';
			foreach($queryPieces as $piece) {
			    if (strlen($piece) > 3) {
			        $sqlquery .= '+';
			    }
			    $sqlquery .= $piece.'* ';
			}
			
			$this->Client->recursive = -1;
			$conditions = array("(MATCH(Client.name) AGAINST('$sqlquery' IN BOOLEAN MODE))");

			$results = $this->Client->find('all', array(
													'conditions' => $conditions, 
													'limit' => 5
													)
											);

			$this->set('query', $query);
			$this->set('results', $results);
			
			if (isset($this->params['requested'])) {
				return $results;
			} elseif(@$_GET['query'] || @ $this->params['named']['query']) {
				$this->redirect(array('controller' => 'tickets', 'action' => 'index/?query=' . $query));	
			}
		endif;
	}

	function revenue() {
		
		// set search criteria from form post or set defaults
		$form = $this->params['form'];
		$named = $this->params['named'];

		// ajaxed paginated form elements come in via params['named']
		if (empty($form) && !empty($named)) {
			$form = $named;
			$this->params['form'] = $this->params['named'];
		}

		// set values and set defaults        
		$s_ticket_id = isset($form['s_ticket_id']) ? $form['s_ticket_id'] : '';
		$s_offer_id = isset($form['s_offer_id']) ? $form['s_offer_id'] : '';
		$s_format_id = isset($form['s_format_id']) ? $form['s_format_id'] : '';
		$s_client_id = isset($form['s_client_id']) ? $form['s_client_id'] : '';
		$s_start_y = isset($form['s_start_y']) ? $form['s_start_y'] : date('Y');
		$s_start_m = isset($form['s_start_m']) ? $form['s_start_m'] : date('m');
		$s_start_d = isset($form['s_start_d']) ? $form['s_start_d'] : date('d');
		$s_end_y = isset($form['s_end_y']) ? $form['s_end_y'] : date('Y');
		$s_end_m = isset($form['s_end_m']) ? $form['s_end_m'] : date('m');
		$s_end_d = isset($form['s_end_d']) ? $form['s_end_d'] : date('d');
		
		if (isset($_GET['searchClientId'])) {
			$s_client_id = $_GET['searchClientId'];		
		}

		if (isset($_GET['query'])) {
			$query = $_GET['query'];
			if (is_numeric($query)) {
				$s_ticket_id = $_GET['query'];			
			} 
		}
		
		$this->set('s_ticket_id', $s_ticket_id);
		$this->set('s_offer_id', $s_offer_id);
		$this->set('s_client_id', $s_client_id);
		$this->set('s_format_id', $s_format_id);
		$this->set('s_start_y', $s_start_y);   
		$this->set('s_start_m', $s_start_m);   
		$this->set('s_start_d', $s_start_d);   
		$this->set('s_end_y', $s_end_y);
		$this->set('s_end_m', $s_end_m);   
		$this->set('s_end_d', $s_end_d);   
		
		// use these dates in the sql for date range search
		$s_start_date = $s_start_y . '-' . $s_start_m . '-' . $s_start_d . ' 00:00:00';
		$s_end_date = $s_end_y . '-' . $s_end_m . '-' . $s_end_d . ' 23:59:59';
				
		$this->paginate = array('fields' => array(
									'Ticket.ticketId', 'Ticket.created', 'Ticket.offerId', 'Ticket.packageId', 'Ticket.billingPrice', 'Ticket.formatId'
									),
		                        'contain' => array('TicketStatus'),
		                        'order' => array(
		                        	'Ticket.ticketId' => 'desc'
		                        	)
		                        );
		    
		// if search via ticket id, offer id, or user id, then dont use other search conditions
		if ($s_ticket_id) {
			$this->paginate['conditions']['Ticket.ticketId'] = $s_ticket_id;    
		} elseif ($s_offer_id) {
			$this->paginate['conditions']['Ticket.offerId'] = $s_offer_id;    
		} elseif ($s_client_id) {
			$this->paginate['conditions']['Ticket.clientId'] = $s_client_id;       
		} else {    
			$this->paginate['conditions']['Ticket.created BETWEEN ? AND ?'] = array($s_start_date, $s_end_date);             		
			if ($s_format_id) {
				$this->paginate['conditions']['Ticket.formatId'] = $s_format_id;	
			}
		}
		
		$tickets_index = $this->paginate();

		// TODO : OPTIMIZE THIS SECTION
		foreach ($tickets_index as $k => $v) {
			$paymentDetail = $this->Ticket->query("select sum(ppBillingAmount) as sumPayment from paymentDetail where isSuccessfulCharge = 1 and ticketId = " . $v['Ticket']['ticketId']);
			$sumPayment = !empty($paymentDetail) && isset($paymentDetail[0][0]['sumPayment']) ? $paymentDetail[0][0]['sumPayment'] : 0;
			$tickets_index[$k]['Ticket']['sumPayment'] = '$' . number_format($sumPayment, 2, '.',',');
			$clients = $this->Ticket->getClientsFromPackageId($v['Ticket']['packageId']);
			$tickets_index[$k]['Client'] = $clients;
			$tracks = $this->TrackDetail->getTrackRecord($v['Ticket']['ticketId']);
			if (!empty($tracks)) {
				foreach ($tracks as $a => $track) {
					$tracks[$a]['trackDetail'] = $this->TrackDetail->getExistingTrackTicket($track['trackId'], $v['Ticket']['ticketId']);
					if (!empty($tracks[$a]['trackDetail'])) {
						$tracks[$a]['trackDetail']['status'] = '1';	
						$tracks[$a]['trackDetail']['allocatedAmount'] = '$' . number_format($tracks[$a]['trackDetail']['allocatedAmount'], 2, '.',',');
						$tracks[$a]['trackDetail']['amountKept'] = '$' . number_format($tracks[$a]['trackDetail']['amountKept'], 2, '.',',');
						$tracks[$a]['trackDetail']['amountRemitted'] = '$' . number_format($tracks[$a]['trackDetail']['amountRemitted'], 2, '.',',');
					} else {
						$tracks[$a]['trackDetail']['allocatedAmount'] = '-';
						$tracks[$a]['trackDetail']['amountKept'] = '-';
						$tracks[$a]['trackDetail']['amountRemitted'] = '-';
						$tracks[$a]['trackDetail']['status'] = '0';				
					}
				}
				$tickets_index[$k]['Ticket']['is_multi_track'] = count($tracks) > 1 ? true : false;
			} else {
				$tracks[0]['trackId'] = '<strong>NO TRACK!</strong>';
				$tracks[0]['trackName'] = '<strong>NO TRACK!</strong>';
				$tracks[0]['trackDetail']['allocatedAmount'] = '<strong>NO TRACK!</strong>';
				$tracks[0]['trackDetail']['amountKept'] = '<strong>NO TRACK!</strong>';
				$tracks[0]['trackDetail']['amountRemitted'] = '<strong>NO TRACK!</strong>';
				$tracks[0]['trackDetail']['status'] = '0';
				$tickets_index[$k]['Ticket']['is_multi_track'] = false;
			}
			$tickets_index[$k]['Tracks'] = $tracks;
		}
		$this->set('tickets', $tickets_index);
		$this->set('format', $this->Format->find('list'));
	}

	// -------------------------------------------
	// NO ONE IS ALLOWED TO EDIT OR DELETE TICKETS
	// -------------------------------------------
	/*
	
	function delete($id = null) {
		$this->Session->setFlash(__('Access Denied - You cannot perform that operation.', true), 'default', array(), 'error');
		$this->redirect(array('action'=>'index'));
		die('ACCESS DENIED');
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Ticket', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Ticket->del($id)) {
			$this->Session->setFlash(__('Ticket deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	
	// -------------------------------------------------------
	// DO NOT USE OR ALTER ANY OF THE FUNCTIONS BELOW .... YET
	// -------------------------------------------------------
	
	function updateTicketStatus($id = null, $ticketStatusId = null) {
		//dont use this function yet
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Ticket', true));
			//$this->redirect(array('action'=>'index'));				
			return false;
		}	
		$ticketStatusIds = $this->Ticket->TicketStatus->find('list');
		if (!$ticketStatusId || !isset($ticketStatusIds[$ticketStatusId])) {
			$this->Session->setFlash(__('Invalid attempt to update workstatus', true));
			//$this->redirect(array('action'=>'view', 'id' => $id));				
			return false;
		} else {
			$ticket['Ticket']['ticketId'] = $id;
			$ticket['Ticket']['ticketStatusId'] = $ticketStatusId;
			if ($this->Ticket->save($ticket)) {
				$this->Session->setFlash(__("Workstatus has been updated to \"$ticketStatusIds[$ticketStatusId]\"", true));
			} else {
				$this->Session->setFlash(__('Ticket status has NOT been updated', true));
			}
		}
	}
	
	function autoNewTicket($id = null) {
		if ($newTicketId = $this->createNewTicketFromTicket($id)) {
			$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $newTicketId));				
		} else {
			$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $id));			
		}
	}
	
	function createNewTicketFromTicket($id = null) {
		if (!$id) {
			return false;	
		}
		
		$this->Ticket->recursive = 0;
		$ticketData = $this->Ticket->read(null, $id);
		$newTicketData['Ticket'] = $ticketData['Ticket'];
		
		// so we can create a NEW ticket based on current ticket
		// change workstatus to NEW, hold same offer info just change bid and user info
		
		unset($newTicketData['Ticket']['ticketId']);
		$newTicketData['Ticket']['ticketStatusId'] = 1;
		$newTicketData['Ticket']['parentTicketId'] = $id;
		$newTicketData['Ticket']['requestId'] = 0;
		$newTicketData['Ticket']['requestInfo'] = 0;
		$newTicketData['Ticket']['notes'] = "SYSTEM:  This ticket was automatically created and has a status of NEW.";
		$newTicketData['Ticket']['isFlake'] = 0;
		$newTicketData['Ticket']['paymentAuthDate'] = '0000-00-00 00:00:00';
		$newTicketData['Ticket']['paymentSettleDate'] = '0000-00-00 00:00:00';
		$newTicketData['Ticket']['completedUsername'] = 'AUTO';
		$newTicketData['Ticket']['completedDate'] = '0000-00-00 00:00:00';
		$newTicketData['Ticket']['keepAmount'] = 0;
		$newTicketData['Ticket']['remitAmount'] = 0;
		$newTicketData['Ticket']['comissionAmount'] = 0;
		$newTicketData['Ticket']['requestDate'] = date('Y-m-d H:i:s');
		$newTicketData['Ticket']['userAddress1'] = '';
		$newTicketData['Ticket']['userAddress2'] = '';
		$newTicketData['Ticket']['userAddress3'] = '';
		$newTicketData['Ticket']['userCity'] = '';
		$newTicketData['Ticket']['userState'] = '';
		$newTicketData['Ticket']['userCountry'] = '';
		$newTicketData['Ticket']['userZip'] =	'';

		$offerId = $ticketData['Offer']['offerId'];
		$bidId = $ticketData['Ticket']['bidId'];
		$userId = $ticketData['Ticket']['userId'];
		
		if (!$offerId || !$bidId) {
			return false;	
		}
		
		$bids = $this->Ticket->Offer->Bid->query('SELECT * from bid WHERE offerId = ' . $offerId . ' AND bidId != ' . $bidId . ' ORDER BY bidId DESC');
		
		$foundValidNextBid = false;
		foreach ($bids as $bid) {
			// must have a valid active bid -- get the next top bid
			if (($bid['bid']['bidInactive'] != 1) && ($bid['bid']['bidId'] != $bidId) && ($bid['bid']['userId'] != $userId)) {
				$user = new User();
				$userData = $user->read(null, $bid['bid']['userId']);
				if ($userData) {
					$newTicketData['Ticket']['bidId'] = 			$bid['bid']['bidId'];
					$newTicketData['Ticket']['userId'] = 			$userData['User']['userId'];
					$newTicketData['Ticket']['userFirstName'] = 	$userData['User']['firstName'];
					$newTicketData['Ticket']['userLastName'] = 	$userData['User']['lastName'];
					$newTicketData['Ticket']['userEmail1'] = 		$userData['User']['email'];
					$newTicketData['Ticket']['userWorkPhone'] = 	$userData['User']['workPhone'];
					$newTicketData['Ticket']['userHomePhone'] = 	$userData['User']['homePhone'];
					$newTicketData['Ticket']['userMobilePhone'] = $userData['User']['mobilePhone'];
					$newTicketData['Ticket']['userFax'] = 		$userData['User']['fax'];
					
					if (!empty($userData['Address'])) {
						$newTicketData['Ticket']['userAddress1'] =	$userData['Address']['address1'];
						$newTicketData['Ticket']['userAddress2'] = 	$userData['Address']['address2'];
						$newTicketData['Ticket']['userAddress3'] = 	$userData['Address']['address3'];
						$newTicketData['Ticket']['userCity'] = 		$userData['Address']['city'];
						$newTicketData['Ticket']['userState'] = 		$userData['Address']['stateName'];
						$newTicketData['Ticket']['userCountry'] = 	$userData['Address']['countryName'];
						$newTicketData['Ticket']['userZip'] =			$userData['Address']['postalCode'];
					}
					$foundValidNextBid = true;
					break;	
				}
			}	
		}
		
		if ($foundValidNextBid) {
			$this->Ticket->create();
			if ($this->Ticket->save($newTicketData)) {
				$this->Session->setFlash(__('The original ticket was cancelled and a NEW ticket has been created based on the original.', true));
				return $this->Ticket->getLastInsertID();
			} else {
				$this->Session->setFlash(__('There was an error while creating the new ticket.', true));
				return false;
			}
		} else {
			$this->Session->setFlash(__('Could not find the next eligible bid -- a new ticket was NOT created.', true));
			return false;	
		}
	}
	
	*/

}
?>
