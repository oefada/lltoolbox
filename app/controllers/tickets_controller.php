<?php

App::import('Vendor', 'nusoap_client/lib/nusoap');

class TicketsController extends AppController {

	var $name = 'Tickets';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
	var $uses = array('Ticket','OfferType', 'User', 'ClientLoaPackageRel', 'RevenueModelLoaRel', 'RevenueModelLoaRelDetail');
	var $paginate;

	function index() {
		$this->Ticket->recursive = 1;
		$this->set('tickets', $this->paginate());
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
		if (!$id) {
			$this->Session->setFlash(__('Invalid Ticket.', true), 'default', array(), 'error');
			$this->redirect(array('action'=>'index'));
		}
		
		$this->Ticket->recursive = -1;
		$ticket = $this->Ticket->read(null, $id);
		
		// [send to webservice tickets]
		$webservice_live_url = 'http://192.168.100.22/web_service_tickets?wsdl';
		$webservice_live_method_name = 'updateTrackDetail';
		$webservice_live_method_param = 'in0';

		$data = array();
		$data['key'] = 'AXPAdFKlo3kZSfk30kA30234SDfjpgQ';
		$data['ticketId'] = $ticket['Ticket']['ticketId'];
		$data['packageId'] = $ticket['Ticket']['packageId'];

		$data_json_encoded = json_encode($data);

		$soap_client = new nusoap_client($webservice_live_url, true);
        $response = $soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));
 			
 		echo $response;
 			
		//$this->redirect(array('action'=>'view', 'id' => $id));
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