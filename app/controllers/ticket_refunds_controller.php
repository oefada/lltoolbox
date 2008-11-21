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
		if (!empty($this->data) && $this->data['TicketRefund']['ticketId']) {
			$ticket = array();
			$ticket['Ticket']['ticketId'] = $this->data['TicketRefund']['ticketId'];
			$ticket['Ticket']['ticketStatusId'] = 8;
			$this->TicketRefund->create();
			if ($this->TicketRefund->save($this->data)  && $this->TicketRefund->Ticket->save($ticket)) {
				$this->Session->setFlash(__('The refund note has been added to this ticket', true));
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
		
		$this->set('refundReasonIds', $this->TicketRefund->RefundReason->find('list'));
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