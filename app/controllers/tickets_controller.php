<?php

//ini_set('max_execution_time', 0);
//ini_set('memory_limit', '256M');

App::import('Vendor', 'nusoap_client/lib/nusoap');
App::Import('Vendor', 'aes.php');

class TicketsController extends AppController {

	var $name = 'Tickets';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
	var $uses = array('Ticket','OfferType', 'Format', 'User', 'ClientLoaPackageRel', 'Track', 'TrackDetail','Offer','Loa','Client');

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
		$s_offer_type_id = isset($form['s_offer_type_id']) ? $form['s_offer_type_id'] : 0;
		$s_ticket_status_id = isset($form['s_ticket_status_id']) ? $form['s_ticket_status_id'] : 0;
		$s_start_y = isset($form['s_start_y']) ? $form['s_start_y'] : date('Y');
		$s_start_m = isset($form['s_start_m']) ? $form['s_start_m'] : date('m');
		$s_start_d = isset($form['s_start_d']) ? $form['s_start_d'] : date('d');
		$s_end_y = isset($form['s_end_y']) ? $form['s_end_y'] : date('Y');
		$s_end_m = isset($form['s_end_m']) ? $form['s_end_m'] : date('m');
		$s_end_d = isset($form['s_end_d']) ? $form['s_end_d'] : date('d');
		
		$this->set('s_ticket_id', $s_ticket_id);
		$this->set('s_offer_id', $s_offer_id);
		$this->set('s_user_id', $s_user_id);
		$this->set('s_format_id', $s_format_id);
		$this->set('s_offer_type_id', $s_offer_type_id);
		$this->set('s_ticket_status_id', $s_ticket_status_id);
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
									'Ticket.ticketId', 'Ticket.offerTypeId', 'Ticket.created', 
									'Ticket.offerId', 'Ticket.userId', 'TicketStatus.ticketStatusName', 
									'Ticket.userFirstName', 'Ticket.userLastName', 'Client.name', 'Ticket.billingPrice', 'Ticket.formatId'),
		                        	'contain' => array(
		                        		'TicketStatus', 'Package', 'Client')
		                        );
		    
		// if search via ticket id, offer id, or user id, then dont use other search conditions
		if ($s_ticket_id) {
			$this->paginate['conditions']['Ticket.ticketId'] = $s_ticket_id;    
		} elseif ($s_offer_id) {
			$this->paginate['conditions']['Ticket.offerId'] = $s_offer_id;    
		} elseif ($s_user_id) {
			$this->paginate['conditions']['Ticket.userId'] = $s_user_id;            		     
		} else {    
			$this->paginate['conditions']['Ticket.created BETWEEN ? AND ?'] = array($s_start_date, $s_end_date);             		
			if ($s_offer_type_id) {
				$this->paginate['conditions']['Ticket.offerTypeId'] = $s_offer_type_id;	
			}
			if ($s_format_id) {
				$this->paginate['conditions']['Ticket.formatId'] = $s_format_id;	
			}
			if ($s_ticket_status_id) {
				$this->paginate['conditions']['Ticket.ticketStatusId'] = $s_ticket_status_id;	
			}
		}
		
		$tickets_index = $this->paginate();
	
		foreach ($tickets_index as $k => $v) {
			$tickets_index[$k]['Ticket']['validCard'] = $this->getValidCcOnFile($v['Ticket']['userId']);

		}
		
		$this->set('tickets', $tickets_index);
		$this->set('format', $this->Format->find('list'));
		$this->set('offerType', $this->OfferType->find('list'));
		$this->set('ticketStatus', $this->Ticket->TicketStatus->find('list'));
	}

	function getValidCcOnFile($userId) {
		$ups = $this->User->query("select * from userPaymentSetting as UserPaymentSetting where userId = $userId and inactive = 0 order by primaryCC desc, expYear desc");
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

		$this->set('ticket', $ticket);
		
		$this->set('offerType', $this->OfferType->find('list'));
		$this->set('ppvNoticeTypes', $this->Ticket->PpvNotice->PpvNoticeType->find('list'));
		
		$this->data = array();
		$this->data['condition1']['field'] = "Offer.offerId";
		$this->data['condition1']['value'] = $ticket['Ticket']['offerId'];
		$offer_search_serialize = serialize($this->data);
		$this->set('offer_search_serialize', $offer_search_serialize);
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
		$this->set('ticketStatusIds', $this->Ticket->TicketStatus->find('list'));
	}

	function updateTrackDetail($id) {
	
		// settings for data retrieval
		// ---------------------------------------------------------
		$this->Ticket->recursive 				= -1;
		$this->Offer->recursive 				=  2;
		$this->Loa->recursive 					= -1;
		$this->Track->recursive 				= -1;

		// data retrieval
		// ---------------------------------------------------------
		$ticket 				= $this->Ticket->read(null, $id);
		$offer 					= $this->Offer->read(null, $ticket['Ticket']['offerId']);
		$schedulingMasterId 	= $offer['SchedulingInstance']['SchedulingMaster']['schedulingMasterId'];
		$smid 					= $this->Track->query("select trackId from schedulingMasterTrackRel where schedulingMasterId = $schedulingMasterId limit 1");
		$trackId 				= $smid[0]['schedulingMasterTrackRel']['trackId'];
		$track 					= $this->Track->read(null, $trackId);
		$last_track_detail 		= $this->Track->query("select * from trackDetail where trackId = $trackId order by trackDetailId desc limit 1");
		
		// vars to work with
		// ---------------------------------------------------------
		$track 					= $track['Track'];
		$last_track_detail		= $last_track_detail[0]['trackDetail'];
		$ticket_amount			= $ticket['Ticket']['billingPrice'];
		$ticket_amount 			= rand(400,1200); // remove remove remove remove after testing remove remove remove remove remove after testing
		$loa					= $this->Loa->read(null, $track['loaId']);
		
		// set new track information for insert into trackDetail
		// ---------------------------------------------------------
		$new_track_detail 							= array();
		$new_track_detail['trackId']				= $trackId;
		$new_track_detail['ticketId']				= $id;
	
		// track detail calculations	
		// ---------------------------------------------------------
		switch ($track['revenueModelId']) {
			case 1:
				// this is a revenue split model
				$new_track_detail['cycle']					= 1;
				$new_track_detail['iteration']				= ++$last_track_detail['iteration'];
				$new_track_detail['amountKept'] 			= ($track['keepPercentage'] / 100) * $ticket_amount;
				$new_track_detail['amountRemitted'] 		= $ticket_amount - $new_track_detail['amountKept'];
				break;
			case 2:
				// this is an x for y average
				
				break;
			case 3:
				// this is an x for y
				if (($last_track_detail['iteration'] + 1) == $track['y']) {
					$new_track_detail['cycle']		= $last_track_detail['cycle'];
					$new_track_detail['iteration']	= ++$last_track_detail['iteration'];
					$new_track_detail['xyRunningTotal'] = $last_track_detail['xyRunningTotal'] + $ticket_amount;
					$new_track_detail['xyAverage']	= (($new_track_detail['xyRunningTotal'] / $track['y']) * $track['x']);
					if ($new_track_detail['xyAverage'] > $ticket_amount) {
						$new_track_detail['keepBalDue']		= $new_track_detail['xyAverage'] - $ticket_amount;
						$new_track_detail['amountKept'] 	= $ticket_amount;
						$new_track_detail['amountRemitted'] = 0;
					} else {
						$new_track_detail['keepBalDue']		= 0;
						$new_track_detail['amountKept'] 	= $new_track_detail['xyAverage'];
						$new_track_detail['amountRemitted'] = $ticket_amount - $new_track_detail['amountKept'];
					}
				} else {
					if ($last_track_detail['iteration'] == $track['y']) {
						$new_track_detail['cycle']			= ++$last_track_detail['cycle'];
						$new_track_detail['iteration']		= 1;
						$new_track_detail['xyRunningTotal'] = $ticket_amount;
						//$new_track_detail['xyRunningTotal'] = $last_track_detail['xyRunningTotal'] - $last_track_detail['keepBalDue'];
					} else {
						$new_track_detail['cycle']			= $last_track_detail['cycle'];
						$new_track_detail['iteration']		= ++$last_track_detail['iteration'];
						$new_track_detail['xyRunningTotal'] = $last_track_detail['xyRunningTotal'] + $ticket_amount;	
					}
					$new_track_detail['amountKept']		= $last_track_detail['keepBalDue'];
					$new_track_detail['amountRemitted']	= $ticket_amount - $last_track_detail['keepBalDue'];
				}
				break;
			default: 
				return false;
				break;
		}
		
		// update the track record (track)
		// ---------------------------------------------------------
		$track['pending']   -= $ticket_amount;
		$track['collected'] += $ticket_amount;
		
		// update the loa record
		// ---------------------------------------------------------
		$loa['Loa']['loaValue']			 += $ticket_amount;
		$loa['Loa']['totalKept']		 += $new_track_detail['amountKept'];
		$loa['Loa']['totalRemitted']	 += $new_track_detail['amountRemitted'];
		$loa['Loa']['membershipBalance'] -= $new_track_detail['amountKept'];
		
		// do all the inserts or updates here
		// ---------------------------------------------------------
		$this->Loa->save($loa);
		$this->Track->save($track);
		$this->Track->TrackDetail->create();
		$this->Track->TrackDetail->save($new_track_detail);
		
		print_r($track);
		print_r($new_track_detail);		
		die();
	}

	// -------------------------------------------
	// NO ONE IS ALLOWED TO EDIT OR DELETE TICKETS
	// -------------------------------------------
	/*

	function add() {
		$this->Session->setFlash(__('Access Denied - You cannot perform that operation.', true), 'default', array(), 'error');
		$this->redirect(array('action'=>'index'));
		die('ACCESS DENIED');
		if (!empty($this->data)) {
			$this->Ticket->create();
			if ($this->Ticket->save($this->data)) {
				$this->Session->setFlash(__('The Ticket has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Ticket could not be saved. Please, try again.', true));
			}
		}
		$this->set('ticketStatusIds', $this->Ticket->TicketStatus->find('list'));
	}

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