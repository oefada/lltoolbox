<?php
class TicketCancellationsController extends AppController {

	var $name = 'TicketCancellations';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->TicketCancellation->recursive = 0;
		$this->set('ticketCancellations', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid TicketCancellation.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('ticketCancellation', $this->TicketCancellation->read(null, $id));
	}

	function add() {
		if (!empty($this->data) && $this->data['TicketCancellation']['ticketId']) {
			$ticket = array();
			$ticket['Ticket']['ticketId'] = $this->data['TicketCancellation']['ticketId'];
			$ticket['Ticket']['ticketStatusId'] = 7;
			$this->TicketCancellation->create();
			if ($this->TicketCancellation->save($this->data) && $this->TicketCancellation->Ticket->save($ticket)) {
				$this->Session->setFlash(__('The TicketCancellation has been saved', true));
				$this->redirect(array('controller' => 'tickets', 'action' => 'view', 'id' => $this->data['TicketCancellation']['ticketId']));
			} else {
				$this->Session->setFlash(__('The TicketCancellation could not be saved. Please, try again.', true));
			}
		}
		
		$ticketId = $this->params['ticketId'];
		
		if (!$ticketId) {
			$this->Session->setFlash(__('Invalid ticket ID', true));
			$this->redirect(array('controller' => 'tickets', 'action'=>'index'));
		} 
		
		$this->set('cancellationReasonIds', $this->TicketCancellation->CancellationReason->find('list'));
		$this->data['TicketCancellation']['ticketId'] = $ticketId;
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid TicketCancellation', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->TicketCancellation->save($this->data)) {
				$this->Session->setFlash(__('The TicketCancellation has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The TicketCancellation could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->TicketCancellation->read(null, $id);
		}
		$this->set('cancellationReasonIds', $this->TicketCancellation->CancellationReason->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for TicketCancellation', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->TicketCancellation->del($id)) {
			$this->Session->setFlash(__('TicketCancellation deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>