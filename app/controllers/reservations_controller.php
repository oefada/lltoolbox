<?php
class ReservationsController extends AppController {

	var $name = 'Reservations';
	var $helpers = array('Html', 'Form');
	var $uses = array('Reservation','TrackDetail','Ticket','Loa');

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
				
				// temp reverse and readd loa for retail value
				if ($this->data['Reservation']['retailValue'] > 0) {
					$this->TrackDetail->adjustForRetailValueTmp($this->data['Reservation']['ticketId'], $this->data['Reservation']['retailValue']);
				}

				$this->Session->setFlash(__('The Reservation has been added', true));
				$this->redirect('/tickets/'. $this->data['Reservation']['ticketId']  .'/ppvNotices/add/1');
			} else {
				$this->Session->setFlash(__('The Reservation could not be added. Please, try again.', true));
			}
		}
		$ticketId = $this->params['ticketId'];
		if (!$ticketId) {
			$this->Session->setFlash(__('Invalid ticket ID', true));
			$this->redirect(array('controller' => 'tickets', 'action'=>'index'));
		} 
		$this->data['Reservation']['ticketId'] = $ticketId;
		$this->set('track', $this->TrackDetail->getTrackRecord($ticketId));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Reservation', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Reservation->save($this->data)) {

				// temp reverse and readd loa for retail value
				if ($this->data['Reservation']['retailValue'] > 0) {
					$this->TrackDetail->adjustForRetailValueTmp($this->data['Reservation']['ticketId'], $this->data['Reservation']['retailValue']);
				}

				$this->Session->setFlash(__('The Reservation has been updated', true));
				$this->redirect('/tickets/'. $this->data['Reservation']['ticketId']  .'/ppvNotices/add/1');
			} else {
				$this->Session->setFlash(__('The Reservation could not be updated. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Reservation->read(null, $id);
			$this->set('track', $this->TrackDetail->getTrackRecord($ticketId));
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
