<?php
class ReservationsController extends AppController {

	var $name = 'Reservations';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Reservation->recursive = 0;
		$this->set('reservations', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Reservation.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('reservation', $this->Reservation->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Reservation->create();
			if ($this->Reservation->save($this->data)) {
				$this->Session->setFlash(__('The Reservation has been updated', true));
				$this->redirect(array('controller' => 'tickets', 'action' => 'view', 'id' => $this->data['Reservation']['ticketId']));
			} else {
				$this->Session->setFlash(__('The Reservation could not be updated. Please, try again.', true));
			}
		}
		
		$ticketId = $this->params['ticketId'];
		
		if (!$ticketId) {
			$this->Session->setFlash(__('Invalid ticket ID', true));
			$this->redirect(array('controller' => 'tickets', 'action'=>'index'));
		} 

		$this->data['Reservation']['ticketId'] = $ticketId;
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Reservation', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Reservation->save($this->data)) {
				$this->Session->setFlash(__('The Reservation has been updated', true));
				$this->redirect(array('controller' => 'tickets', 'action' => 'view', 'id' => $this->data['Reservation']['ticketId']));
			} else {
				$this->Session->setFlash(__('The Reservation could not be updated. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Reservation->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Reservation', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Reservation->del($id)) {
			$this->Session->setFlash(__('Reservation deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>