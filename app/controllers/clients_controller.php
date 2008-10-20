<?php
class ClientsController extends AppController {

	var $name = 'Clients';
	var $uses = array('Client', 'User');
	
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
		$amenities = $this->Client->Amenity->find('list');
		$clientLevelIds = $this->Client->ClientLevel->find('list');
		$clientStatusIds = $this->Client->ClientStatus->find('list');
		$clientTypeIds = $this->Client->ClientType->find('list');
		$clientAcquisitionSourceIds = $this->Client->ClientAcquisitionSource->find('list');
		$this->set(compact('tags','users','amenities','clientLevelIds','clientStatusIds','clientTypeIds','regions','clientAcquisitionSourceIds'));
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
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
		}
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);

			$this->Client->recursive = -1;
			$results = $this->Client->find('all', array('conditions' => array('name LIKE' => "%$query%"), 'limit' => 5));
			$this->set('query', $query);
			$this->set('results', $results);
			
			if (isset($this->params['requested'])) {
				return $results;
			} elseif($_GET['query'] ||  $this->params['named']['query']) {
				$this->autoRender = false;
				$this->Client->recursive = 0;

				$this->paginate = array('conditions' => array('name LIKE' => "%$query%"));
				$this->set('query', $query);
				$this->set('clients', $this->paginate());
				$this->render('index');
			}
		endif;
	}
}
?>