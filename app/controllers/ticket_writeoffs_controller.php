<?php
class TicketWriteoffsController extends AppController {

	var $name = 'TicketWriteoffs';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->TicketWriteoff->recursive = 0;
		$this->set('ticketWriteoffs', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid TicketWriteoff.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('ticketWriteoff', $this->TicketWriteoff->read(null, $id));
	}

	function add() {
		if (!empty($this->data) && $this->data['TicketWriteoff']['ticketId']) {
			$ticket = array();
			$ticket['Ticket']['ticketId'] = $this->data['TicketWriteoff']['ticketId'];
			$ticket['Ticket']['ticketStatusId'] = 7;
			$this->TicketWriteoff->create();
			if ($this->TicketWriteoff->save($this->data) && $this->TicketWriteoff->Ticket->save($ticket)) {
				$this->Session->setFlash(__('This ticket has been written off.', true));
				$this->redirect(array('controller' => 'tickets', 'action' => 'view', 'id' => $this->data['TicketWriteoff']['ticketId']));
			} else {
				$this->Session->setFlash(__('The TicketWriteoff could not be saved. Please, try again.', true));
			}
		}
		
		$ticketId = $this->params['ticketId'];
		
		if (!$ticketId) {
			$this->Session->setFlash(__('Invalid ticket ID', true));
			$this->redirect(array('controller' => 'tickets', 'action'=>'index'));
		} 
		
		$this->set('ticketWriteoffReasonIds', $this->TicketWriteoff->TicketWriteoffReason->find('list'));
		$this->data['TicketWriteoff']['ticketId'] = $ticketId;
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid TicketWriteoff', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->TicketWriteoff->save($this->data)) {
				$this->Session->setFlash(__('The ticket writeoff has been updated.', true));
				$this->redirect(array('controller' => 'tickets', 'action' => 'view', 'id' => $this->data['TicketWriteoff']['ticketId']));
			} else {
				$this->Session->setFlash(__('The ticket writeoff could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->TicketWriteoff->read(null, $id);
		}
		$this->set('ticketWriteoffReasonIds', $this->TicketWriteoff->TicketWriteoffReason->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for TicketWriteoff', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->TicketWriteoff->del($id)) {
			$this->Session->setFlash(__('TicketWriteoff deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>