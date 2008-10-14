<?php
class LoasController extends AppController {

	var $name = 'Loas';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
	var $paginate;
	
	function index($clientId = null) {
		if(isset($clientId) && $this->Loa->Client->find('count', array('conditions' => array('clientId' => $clientId)))) {
			$this->Loa->recursive = 0;
			$this->set('loas', $this->paginate('Loa', array('Client.clientId' => $clientId)));
		} else {
			$this->cakeError('error404');
		}
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Loa.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('loa', $this->Loa->read(null, $id));
	}

	function add($clientId = null) {
		if (!empty($this->data)) {
			$clientId = $this->data['Loa']['clientId'];
			$this->Loa->create();
			if ($this->Loa->save($this->data)) {
				$this->Session->setFlash(__('The Loa has been saved', true));
				$this->redirect("/clients/$clientId/loas");
			} else {
				$this->Session->setFlash(__('The Loa could not be saved. Please, try again.', true));
			}
		}
		
		if(!$clientId) {
			$this->Session->setFlash(__('Incorrect client id specified. Please try again.', true));
			$this->redirect(array('controller' => 'clients', 'action' => 'index'));
		}
		$this->data['Loa']['clientId'] = $clientId;
		$customerApprovalStatusIds = $this->Loa->LoaCustomerApprovalStatus->find('list');

		$this->Loa->Client->recursive = -1;
		$client = $this->Loa->Client->find('clientId = '.$clientId, 'name');
		$this->set('clientName', $client['Client']['name']);
		$this->set(compact('customerApprovalStatusIds'));
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
			$this->redirect(array('controller' => 'clients', 'action'=>'index'));
		}
		$this->Loa->recursive = -1;
		$clientId = $this->Loa->read('Loa.clientId', $id);
		$clientId = $clientId['Loa']['clientId'];
		
		if ($this->Loa->del($id)) {
			$this->Session->setFlash(__('Loa deleted', true));
			$this->redirect("/clients/$clientId/loas");
		}
	}

}
?>