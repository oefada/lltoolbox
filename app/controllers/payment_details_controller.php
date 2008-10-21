<?php
class PaymentDetailsController extends AppController {

	var $name = 'PaymentDetails';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->PaymentDetail->recursive = 0;
		$this->set('paymentDetails', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PaymentDetail.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('paymentDetail', $this->PaymentDetail->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->PaymentDetail->create();
			if ($this->PaymentDetail->save($this->data)) {
				$this->Session->setFlash(__('The PaymentDetail has been saved', true));
				$this->redirect(array('controller' => 'tickets', 'action'=>'view', 'id' => $this->data['PaymentDetail']['ticketId']));
			} else {
				$this->Session->setFlash(__('The PaymentDetail could not be saved. Please, try again.', true));
			}
		}
		$this->set('paymentTypeIds', $this->PaymentDetail->PaymentType->find('list'));
		$this->set('paymentProcessorIds', $this->PaymentDetail->PaymentProcessor->find('list'));
		$this->data['PaymentDetail']['ticketId'] = $this->params['ticketId'];
	}

	function edit($id = null) {	
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PaymentDetail', true));
			$this->redirect(array('action'=>'index'));
		}
		
		// added so that user cannot edit or delete payment details
		$this->Session->setFlash(__('You do not have permissions to alter the payment details for this transaction', true));
		$this->redirect(array('action'=>'view', 'id' => $id));
		
		if (!empty($this->data)) {
			if ($this->PaymentDetail->save($this->data)) {
				$this->Session->setFlash(__('The PaymentDetail has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The PaymentDetail could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PaymentDetail->read(null, $id);
		}
		$this->set('paymentProcessorIds', $this->PaymentDetail->PaymentProcessor->find('list'));
		$this->set('paymentTypeIds', $this->PaymentDetail->PaymentType->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for PaymentDetail', true));
			$this->redirect(array('action'=>'index'));
		}
		
		// added so that user cannot edit or delete payment details
		$this->Session->setFlash(__('You do not have permissions to alter the payment details for this transaction', true));
		$this->redirect(array('action'=>'view', 'id' => $id));
		
		if ($this->PaymentDetail->del($id)) {
			$this->Session->setFlash(__('PaymentDetail deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

	function confirmPayment($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PaymentDetail.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('paymentDetail', $this->PaymentDetail->read(null, $id));
	}

	function processPayment($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PaymentDetail.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('paymentDetail', $this->PaymentDetail->read(null, $id));
		$this->set('successfulPayment', 1);
	}
}
?>