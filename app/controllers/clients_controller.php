<?php
uses ('sanitize');

class ClientsController extends AppController {

	var $name = 'Clients';
	var $helpers = array('Html', 'Form', 'Text', 'Pagination', 'Layout');
	var $components = array('RequestHandler');
	var $Sanitize;
	
	function index() {
		$this->Client->recursive = 0;

		$this->set('clients', $this->paginate());
		
		$page = isset($this->params['named']['page']) ? $this->params['named']['page'] : 1;
		
		$this->set( 'pag_link', '/clients/index/page:' );
        $this->set( 'pag_page', $page );
        $this->set( 'pag_total', $this->Client->findCount() );
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
		$amenities = $this->Client->Amenity->find('list');
		$clientLevels = $this->Client->ClientLevel->find('list');
		$clientStatuses = $this->Client->ClientStatus->find('list');
		$clientTypes = $this->Client->ClientType->find('list');
		$regions = $this->Client->Region->find('list');
		$clientAcquisitionSources = $this->Client->ClientAcquisitionSource->find('list');
		$this->set(compact('amenities', 'clientLevels', 'clientStatuses', 'clientTypes', 'regions', 'clientAcquisitionSources'));
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
		$clientLevelIds = $this->Client->ClientLevel->find('list');
		$clientStatusIds = $this->Client->ClientStatus->find('list');
		$clientTypeIds = $this->Client->ClientType->find('list');
		$regions = $this->Client->Region->find('list');
		$clientAcquisitionSources = $this->Client->ClientAcquisitionSource->find('list');
		$this->set(compact('tags','users','amenities','clientLevelIds','clientStatusIds','clientTypeIds','regions','clientAcquisitionSources'));
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
	
	function search()
	{
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);

			$this->Client->recursive = -1;
			$this->set('query', $query);
			$this->set('results', $this->Client->find('all', array('conditions' => array('name LIKE' => "%$query%"), 'limit' => 5)));
		endif;
	}
	
	function beforeFilter() {
		$this->Sanitize = new Sanitize();
		
		if($this->RequestHandler->isAjax()) {
			Configure::write('debug', '0');
		}
	}
}
?>