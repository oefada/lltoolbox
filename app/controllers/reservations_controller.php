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
				$this->Session->setFlash(__('The Reservation has been saved', true));
				$this->redirect(array('controller' => 'worksheets', 'action' => 'view', 'id' => $this->data['Reservation']['worksheetId']));
			} else {
				$this->Session->setFlash(__('The Reservation could not be saved. Please, try again.', true));
			}
		}
		
		$worksheetId = $this->params['worksheetId'];
		
		if (!$worksheetId) {
			$this->Session->setFlash(__('Invalid worksheet ID', true));
			$this->redirect(array('controller' => 'worksheets', 'action'=>'index'));
		} 

		$this->data['Reservation']['worksheetId'] = $worksheetId;
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Reservation', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Reservation->save($this->data)) {
				$this->Session->setFlash(__('The Reservation has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Reservation could not be saved. Please, try again.', true));
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