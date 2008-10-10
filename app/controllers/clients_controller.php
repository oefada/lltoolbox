<?php
class ClientsController extends AppController {

	var $name = 'Clients';
	var $helpers = array('Html', 'Form');
	var $components = array('RequestHandler');
	var $scaffold;
	/*
	function index() {
		$this->Client->recursive = 0;
		$this->set('clients', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Client.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('client', $this->Client->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Client->create();
			if ($this->Client->save($this->data)) {
				$this->Session->setFlash(__('The Client has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Client could not be saved. Please, try again.', true));
			}
		}
		$tags = $this->Client->Tag->find('list');
		$users = $this->Client->User->find('list');
		$amenities = $this->Client->Amenity->find('list');
		$clientLevels = $this->Client->ClientLevel->find('list');
		$clientStatuses = $this->Client->ClientStatus->find('list');
		$clientTypes = $this->Client->ClientType->find('list');
		$regions = $this->Client->Region->find('list');
		$clientAcquisitionSources = $this->Client->ClientAcquisitionSource->find('list');
		$this->set(compact('tags', 'users', 'amenities', 'clientLevels', 'clientStatuses', 'clientTypes', 'regions', 'clientAcquisitionSources'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Client', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Client->save($this->data)) {
				$this->Session->setFlash(__('The Client has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Client could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Client->read(null, $id);
		}
		$tags = $this->Client->Tag->find('list');
		$users = $this->Client->User->find('list');
		$amenities = $this->Client->Amenity->find('list');
		$clientLevels = $this->Client->ClientLevel->find('list');
		$clientStatuses = $this->Client->ClientStatus->find('list');
		$clientTypes = $this->Client->ClientType->find('list');
		$regions = $this->Client->Region->find('list');
		$clientAcquisitionSources = $this->Client->ClientAcquisitionSource->find('list');
		$this->set(compact('tags','users','amenities','clientLevels','clientStatuses','clientTypes','regions','clientAcquisitionSources'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Client', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Client->del($id)) {
			$this->Session->setFlash(__('Client deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	*/
}
?>