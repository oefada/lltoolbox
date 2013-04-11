<?php
class UserPaymentSettingsController extends AppController {

	var $name = 'UserPaymentSettings';
	var $helpers = array('Html', 'Form');
	var $uses = array('UserPaymentSetting', 'CountryBilling');

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

	function add($userId = null) {
		if (!empty($this->data)) {
			$this->UserPaymentSetting->create();
			if ($this->UserPaymentSetting->save($this->data)) {
				$this->Session->setFlash(__('The UserPaymentSetting has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The UserPaymentSetting could not be saved. Please, try again.', true));
			}
		}
		$this->data['UserPaymentSetting']['userId'] = $userId;
		$paymentTypeIds = $this->UserPaymentSetting->PaymentType->find('list');
		$countries = $this->CountryBilling->getList();
		$this->set(compact('paymentTypeIds', 'countries'));
	}

	function edit($userId = null, $id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid UserPaymentSetting', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->UserPaymentSetting->save($this->data)) {
				$this->Session->setFlash(__('The Credit Card has been updated.', true));
				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
				} else {
					$this->redirect(array('controller' => 'users', 'action'=>'edit', 'id' => $this->data['UserPaymentSetting']['userId']));
				}
			} else {
				$this->Session->setFlash(__('The UserPaymentSetting could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->UserPaymentSetting->read(null, $id);
		} else {
			$ccNum = $this->UserPaymentSetting->read(null, $id);
			$this->data['UserPaymentSetting']['ccNumber'] = $ccNum['UserPaymentSetting']['ccNumber'];
		}
		$paymentTypes = $this->UserPaymentSetting->PaymentType->find('list');
		$this->set(compact('paymentTypes'));
	}

	function delete($userId = null, $id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for UserPaymentSetting', true));
			$this->redirect(array('controller' => 'users', 'action'=>'index'));
		}
		if ($this->UserPaymentSetting->del($id)) {
			$this->Session->setFlash(__('UserPaymentSetting deleted', true));
			$this->redirect(array('controller' => 'users', 'action'=>'edit', $userId));
		}
	}

}
?>