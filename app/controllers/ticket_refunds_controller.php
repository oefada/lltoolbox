<?php
class TicketRefundsController extends AppController {

	var $name = 'TicketRefunds';
	var $helpers = array('Html', 'Form');

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
		$refundReasons = $this->TicketRefund->RefundReason->find('list');
		if (!empty($this->data) && $this->data['TicketRefund']['ticketId']) {
			$ticket = array();
			$ticket['Ticket']['ticketId'] = $this->data['TicketRefund']['ticketId'];
			$ticket['Ticket']['ticketStatusId'] = 8;
			$this->TicketRefund->create();
			if ($this->TicketRefund->save($this->data)  && $this->TicketRefund->Ticket->save($ticket)) {
				$this->Session->setFlash(__('The refund note has been added to this ticket', true));
				
				// mail this info to accounting@luxurylink.com
				// TODO:  this is temporary email system.  Must make email wrapper function / class later.
				$dateRequested = $this->data['TicketRefund']['dateRequested']['year'];
				$dateRequested.= '-' . $this->data['TicketRefund']['dateRequested']['month'];
				$dateRequested.= '-' . $this->data['TicketRefund']['dateRequested']['day'];
				$dateRequested.= ' ' . $this->data['TicketRefund']['dateRequested']['hour'];
				$dateRequested.= ':' . $this->data['TicketRefund']['dateRequested']['min'];
				$dateRequested.= ' ' . $this->data['TicketRefund']['dateRequested']['meridian'];
				
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
				
				$this->redirect(array('controller' => 'tickets', 'action' => 'view', 'id' => $this->data['TicketRefund']['ticketId']));
			} else {
				$this->Session->setFlash(__('The ticket refund could not be saved. Please, try again.', true));
			}
		}
		
		$ticketId = $this->params['ticketId'];
		
		if (!$ticketId) {
			$this->Session->setFlash(__('Invalid ticket ID', true));
			$this->redirect(array('controller' => 'tickets', 'action'=>'index'));
		} 
		
		$this->set('refundReasonIds', $refundReasons);
		$this->data['TicketRefund']['ticketId'] = $ticketId;
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid TicketRefund', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->TicketRefund->save($this->data)) {
				$this->Session->setFlash(__('The ticket refund information has been updated.', true));
				$this->redirect(array('controller' => 'tickets', 'action' => 'view', 'id' => $this->data['TicketRefund']['ticketId']));
			} else {
				$this->Session->setFlash(__('The ticket refund could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->TicketRefund->read(null, $id);
		}
		
		$this->set('refundReasonIds', $this->TicketRefund->RefundReason->find('list'));
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