<?php
class TicketsController extends AppController {

	var $name = 'Tickets';
	var $helpers = array('Html', 'Form');
	
	function index() {
		$this->set('tickets', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Ticket.', true));
			$this->redirect(array('action'=>'index'));
		}
		
		$ticket = $this->Ticket->read(null, $id);
		
		if (($ticket['Ticket']['isFlake'] || ($ticket['Ticket']['ticketStatusId'] == 7)) && empty($ticket['TicketCancellation']['ticketId'])) {
			$showCancellation = true;
		} else {
			$showCancellation = false;	
		}
		
		$refundWholeTicket = false;
		foreach ($ticket['PaymentDetail'] as $pd) {
			if ($pd['refundWholeTicket']) {
				$refundWholeTicket = true;	
				break;
			}	
		}
		
		if (($ticket['Ticket']['ticketStatusId'] == 8 || $refundWholeTicket) && empty($ticket['TicketRefund']['ticketId'])) {
			$showRefund = true;
		} else {
			$showRefund = false;	
		}

		$validNextBid = false;
		if (!empty($ticket['TicketCancellation']['ticketId'])) {
			$bids = $this->Ticket->Offer->Bid->query('SELECT * from bid WHERE offerId = ' . $ticket['Offer']['offerId'] . ' AND bidId != ' . $ticket['Ticket']['bidId'] . ' ORDER BY bidId DESC');
			foreach ($bids as $bid) {
				if (($bid['bid']['bidInactive'] != 1) && ($bid['bid']['bidId'] != $ticket['Ticket']['bidId']) && ($bid['bid']['userId'] != $ticket['Ticket']['userId'])) {
					$validNextBid = true;
					break;
				}
			}	
		}
		
		$showNewReservationLink = !empty($ticket['Reservation']['ticketId']) ? true : false;
		
		$this->set('showNewReservationLink', $showNewReservationLink);
		$this->set('validNextBid', $validNextBid);
		$this->set('showCancellation', $showCancellation);
		$this->set('showRefund', $showRefund);
		$this->set('ticket', $ticket);
	}

	function add() {
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

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Ticket', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Ticket->save($this->data)) {
				$this->Session->setFlash(__('The Ticket has been saved', true));
				$this->redirect(array('action'=>'view', 'id' => $id));
			} else {
				$this->Session->setFlash(__('The Ticket could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Ticket->read(null, $id);
		}
		$this->set('ticketStatusIds', $this->Ticket->TicketStatus->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Ticket', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Ticket->del($id)) {
			$this->Session->setFlash(__('Ticket deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function updateTicketStatus($id = null, $ticketStatusId = null) {
		//bah dont use this function yet
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
		
		/*
		
		//attach  containable behavior, can also be done in the model through an actsAs
		$this->Ticket->Behaviors->attach('Containable');
		
		//choose which child/sibling models we want to return besides the current one
		$this->Ticket->contain('Offer');
		
		//do a normal read
		$ticketData = $this->Ticket->read(null, $id);
		$offerId = $ticketData['Offer']['offerId'];
		
		//we only get Ticket and whatevr we contained by, in this case, Offer
		debug($ticketData);
		*/
	}

}
?>