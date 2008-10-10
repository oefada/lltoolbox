<?php
class LoasController extends AppController {

	var $name = 'Loas';
	var $helpers = array('Html', 'Form', 'Ajax');

	function index() {
		$this->Loa->recursive = 0;
		$this->set('loas', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Loa.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('loa', $this->Loa->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Loa->create();
			if ($this->Loa->save($this->data)) {
				$this->Session->setFlash(__('The Loa has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Loa could not be saved. Please, try again.', true));
			}
		}
		$loaCustomerApprovalStatuses = $this->Loa->LoaCustomerApprovalStatus->find('list');
		$clients = $this->Loa->Client->find('list');
		$this->set(compact('loaCustomerApprovalStatuses', 'clients'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Loa', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Loa->save($this->data)) {
				$this->Session->setFlash(__('The Loa has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Loa could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Loa->read(null, $id);
		}
		$loaCustomerApprovalStatuses = $this->Loa->LoaCustomerApprovalStatus->find('list');

		$this->set(compact('loaCustomerApprovalStatuses'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Loa', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Loa->del($id)) {
			$this->Session->setFlash(__('Loa deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>