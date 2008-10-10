<?php
class UserPaymentSettingsController extends AppController {

	var $name = 'UserPaymentSettings';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->UserPaymentSetting->recursive = 0;
		$this->set('userPaymentSettings', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid UserPaymentSetting.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('userPaymentSetting', $this->UserPaymentSetting->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->UserPaymentSetting->create();
			if ($this->UserPaymentSetting->save($this->data)) {
				$this->Session->setFlash(__('The UserPaymentSetting has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The UserPaymentSetting could not be saved. Please, try again.', true));
			}
		}
		$paymentTypes = $this->UserPaymentSetting->PaymentType->find('list');
		$this->set(compact('paymentTypes'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid UserPaymentSetting', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->UserPaymentSetting->save($this->data)) {
				$this->Session->setFlash(__('The UserPaymentSetting has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The UserPaymentSetting could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->UserPaymentSetting->read(null, $id);
		}
		$paymentTypes = $this->UserPaymentSetting->PaymentType->find('list');
		$this->set(compact('paymentTypes'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for UserPaymentSetting', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->UserPaymentSetting->del($id)) {
			$this->Session->setFlash(__('UserPaymentSetting deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>