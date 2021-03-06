<?php

Configure::write('debug', 0);

class TicketRefundsController extends AppController {

	var $name = 'TicketRefunds';
	var $helpers = array('Html', 'Form');
	var $uses = array('TicketRefund', 'Ticket', 'CreditTracking', 'CreditTrackingType', 'TicketReferFriend', 'Promo', 'CreditBank'); //'CreditTrackingTicketRel',);

	function beforeFilter() {
		parent::beforeFilter();
		
		$currentUser = $this->LdapAuth->user();
		
		$this->canSave = true;
		//if (in_array('Accounting',$currentUser['LdapUser']['groups']) || in_array('Geeks',$currentUser['LdapUser']['groups'])) {
			//$this->canSave = false;
		//}
		
		$this->set('canSave',$this->canSave);
	}
	
	function index() {
		$this->TicketRefund->recursive = 0;
		$this->set('ticketRefunds', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid TicketRefund.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('ticketRefund', $this->TicketRefund->read(null, $id));
	}

	function add() {
		if (!$this->canSave) {
			$this->Session->setFlash(__('You do not have permission to refund this ticket.', true));
			$this->render();
		} 
		
		$refundReasons = $this->TicketRefund->RefundReason->find('list', array('conditions' => array("inactive" => 0)));
		if (!empty($this->data) && $this->data['TicketRefund']['ticketId']) {
			// 07/19/11 jwoods - ticket 2067 for error handling
			if (is_numeric($this->data['cofAmount']) && $this->data['cofAmount'] > 20000) {
				$this->Session->setFlash(__('$20,000 is the maximum refund.', true));
			} elseif (is_numeric($this->data['TicketRefund']['amountRefunded']) && $this->data['TicketRefund']['amountRefunded'] > 20000) {
				$this->Session->setFlash(__('$20,000 is the maximum refund.', true));
			} else {
				$ticket = array();
				$ticket['Ticket']['ticketId'] = $this->data['TicketRefund']['ticketId'];

				$this->TicketRefund->create();
				if ($this->TicketRefund->save($this->data)) {
					if ($this->data['TicketRefund']['refundEntire'] == 1) {
						$ticket['Ticket']['ticketStatusId'] = 8;
						if (!$this->TicketRefund->Ticket->save($ticket)) {
							$this->Session->setFlash("Could not save ticket. Please contact tech.");
						}
					}
					
					$this->Session->setFlash(__('The refund note has been added to this ticket', true));

					// mail this info to accounting@luxurylink.com
					// TODO:  this is temporary email system.  Must make email wrapper function / class later.
					$dateRequested = $this->data['TicketRefund']['dateRequested']['year'];
					$dateRequested.= '-' . $this->data['TicketRefund']['dateRequested']['month'];
					$dateRequested.= '-' . $this->data['TicketRefund']['dateRequested']['day'];
					$dateRequested.= ' ' . $this->data['TicketRefund']['dateRequested']['hour'];
					$dateRequested.= ':' . $this->data['TicketRefund']['dateRequested']['min'];
					$dateRequested.= ' ' . $this->data['TicketRefund']['dateRequested']['meridian'];
					
					$this->data['TicketRefund']['refundedBy'] = $_SESSION['Auth']['AdminUser']['mailnickname'];
					
					$emailTo = 'accounting@luxurylink.com';
					$emailFrom = 'LuxuryLink.com Accounting<accounting@luxurylink.com>';
					$emailHeaders = "From: $emailFrom\r\n";
					$emailHeaders.= "Content-type: text/html\r\n";
					$emailSubject = 'Refund Initiated for Ticket Id ' . $ticket['Ticket']['ticketId'];
					$emailBody = '<strong>A refund request has been submitted for Ticket Id ' . $ticket['Ticket']['ticketId'] . '</strong><br /><br />';
					$emailBody.= '<table width="700" cellspacing="2" cellpadding="2" border="0">';
					$emailBody.= '<tr><td width="200" valign="top">Ticket Id</td><td>' . $ticket['Ticket']['ticketId'] . '</td></tr>';
					$emailBody.= '<tr><td width="200" valign="top">Date Requested</td><td>' . $dateRequested . '</td></tr>';
					$emailBody.= '<tr><td width="200" valign="top">Amount Refunded</td><td>$'. number_format($this->data['TicketRefund']['amountRefunded'],2,'.','') .'</td></tr>';
					$emailBody.= '<tr><td width="200" valign="top">Refund Reason</td><td>' . $refundReasons[$this->data['TicketRefund']['refundReasonId']] .'</td></tr>';
					$emailBody.= '<tr><td width="200" valign="top">Refund Notes</td><td>'. $this->data['TicketRefund']['refundNotes'] .'</td></tr>';
					$emailBody.= '</table>';

					// send out email now
					@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);

					// credit on file
					if (($this->data['TicketRefund']['ticketRefundTypeId'] == 1 || $this->data['TicketRefund']['ticketRefundTypeId'] == 3) && $this->data['cofAmount'] > 0) {
						$result = $this->Ticket->query('SELECT userId FROM ticket WHERE ticketId = ' . $this->data['TicketRefund']['ticketId']);
						$userId = $result[0]['ticket']['userId'];
						$ctdata['CreditTracking']['creditTrackingTypeId'] = 2;
						$ctdata['CreditTracking']['userId'] = $userId;
						$ctdata['CreditTracking']['amount'] = $this->data['cofAmount'];
						$ctdata['CreditTracking']['notes'] = $this->data['TicketRefund']['refundNotes'];
						$this->CreditTracking->create();
						$this->CreditTracking->save($ctdata);
						$creditTrackingId = $this->CreditTracking->getLastInsertId();

						//$cttrdata['CreditTrackingTicketRel']['ticketId'] = $ticket['Ticket']['ticketId'];
						//$cttrdata['CreditTrackingTicketRel']['creditTrackingId'] = $creditTrackingId;
						//$this->CreditTrackingTicketRel->create();
						//$this->CreditTrackingTicketRel->save($cttrdata);
					}

					// raf adjustments
					$ticketReferFriend = $this->TicketReferFriend->read(null, $ticket['Ticket']['ticketId']);
					if (!empty($ticketReferFriend)) {
						$rafData = $this->Promo->getRafDataByTicketId($ticket['Ticket']['ticketId']);

						$emailTo = $rafData['User']['email'];
						$emailSubject = 'Your Friend Has Refunded Their Luxury Link Purchase';
						$emailFrom = 'LuxuryLink.com <referafriend@luxurylink.com>';
						$emailHeaders = "From: $emailFrom\r\n";
						$emailHeaders.= "Bcc: thread@luxurylink.com\r\n";
						$emailHeaders.= "Content-type: text/html\r\n";

						$url = 'http://www.luxurylink.com';

						ob_start();
						include('../vendors/email_msgs/notifications/22_raf_refund_notification.html');
						$emailBody = ob_get_clean();

						@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);

						$ticketReferFriend['TicketReferFriend']['refunded'] = date('Y:m:d H:i:s', strtotime('now'));
						$this->TicketReferFriend->save($ticketReferFriend);
					}
					// $this->redirect(array('controller' => 'tickets', 'action' => 'view', 'id' => $this->data['TicketRefund']['ticketId']));
					$this->Session->setFlash(__('The Refund has been saved', true), 'default', array(), 'success');
					if (isset($this->params['url']['cof'])) {
						$this->redirect(array('controller' => 'credit_trackings','action' => 'view', $userId));
					}
					$this->set('closeModalbox', true);
				} else {
					$this->Session->setFlash(__('The ticket refund could not be saved. Please, try again.', true));
				}
			}
		}

		$ticketId = $this->params['ticketId'];

		if (!$ticketId) {
			$this->Session->setFlash(__('Invalid ticket ID', true));
			$this->redirect(array('controller' => 'tickets', 'action'=>'index'));
		}

		$refundTypes = $this->TicketRefund->TicketRefundType->find('list');

		//$refundTypes = array($refundTypes[1]);
		
		$this->set('refundReasonIds', $refundReasons);
		$this->set('ticketRefundTypeIds', $refundTypes);
		$this->data['TicketRefund']['ticketId'] = $ticketId;
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid TicketRefund', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {

			if (is_numeric($this->data['TicketRefund']['amountRefunded']) && $this->data['TicketRefund']['amountRefunded'] > 20000) {
							$this->Session->setFlash(__('$20,000 is the maximum refund.', true));

			} elseif ($this->TicketRefund->save($this->data)) {
				$this->Session->setFlash(__('The ticket refund information has been updated.', true), 'default', array(), 'success');
				// $this->redirect(array('controller' => 'tickets', 'action' => 'view', 'id' => $this->data['TicketRefund']['ticketId']));
				$this->set('closeModalbox', true);
				
				// save to creditBank if applicable
				$creditBankData['ticketId'] = $this->data['TicketRefund']['ticketId'];
				$creditBankData['refundType'] = 1;
				$creditBankData['userId'] = $this->LdapAuth->object->viewVars['user']['LdapUser']['samaccountname'];
				$creditBankData['udpate_cof'] = 1;
				$creditBankData['cofRefundAmount'] = $this->data['TicketRefund']['amountRefunded'];
				
				$this->CreditBank->refundCreditBankByTicket($creditBankData);
				
			} else {
				$this->Session->setFlash(__('The ticket refund could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->TicketRefund->read(null, $id);
		}

		$this->set('refundReasonIds', $this->TicketRefund->RefundReason->find('list', array('conditions' => array("inactive" => 0))));
		$refundTypes = $this->TicketRefund->TicketRefundType->find('list');
		$this->set('ticketRefundTypeIds', $refundTypes);
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for TicketRefund', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->TicketRefund->del($id)) {
			$this->Session->setFlash(__('TicketRefund deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>
