<?php

App::import('Vendor', 'nusoap_client/lib/nusoap');

class ClientsController extends AppController {

	var $name = 'Clients';

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('clientId', $this->Client->id);
		$this->Country = new Country;
	}

	function index() {
		$this->Client->recursive = 1;
		$this->paginate = array('contain' => array('ChildClient', 'ClientType'), 'fields' => array('Client.name, Client.clientTypeId, ClientType.clientTypeName'), 'conditions' => array('OR' => array('parentClientId IS NULL')));

		$this->set('clients', $this->paginate());
	}
	
	function view($id = null) {
		$this->redirect(array('action'=>'edit', $id));
	}
	
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Client', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Client->saveAll($this->data)) {
				if (isset($this->data['ClientAmenityRel']) && !empty($this->data['ClientAmenityRel'])) {
			    	foreach ($this->data['ClientAmenityRel'] as $am) {
				        $clientAmenityRelIds[] = @$am['clientAmenityRelId'];
			    	}
			    	$this->Client->ClientAmenityRel->deleteAll(array('clientId' => $this->data['Client']['clientId'], 'NOT' => array('clientAmenityRelId' => $clientAmenityRelIds)));
				}
			    
				$this->Session->setFlash(__('The Client has been saved', true));
				$this->redirect(array('action'=>'edit', 'id' => $id));
			} else {
				$this->Session->setFlash(__('The Client could not be saved. Please, try again.', true));
			}
			$this->set('submission', true);
		}
		//set up our data, if it's a form post, we still need all related data
		if (empty($this->data)) {
		    $this->Client->recursive = 2;
			$this->data = $this->Client->read(null, $id);
		}
		
		$clientTypeIds = $this->Client->ClientType->find('list');
		$themes = $this->Client->Theme->find('list');
		$this->set('client', $this->data);
		//$this->set(compact('addresses', 'amenities','clientLevelIds','clientStatusIds','clientTypeIds','regions','clientAcquisitionSourceIds', 'loas', 'themes'));
		$countryIds = $this->Country->find('list');
		if (!empty($this->data['Client']['countryId'])) {
		    $stateIds = $this->Country->State->find('list', array('conditions' => array('State.countryId' => $this->data['Client']['countryId'])));
		}
		if (!empty($this->data['Client']['stateId'])) {
		    $cityIds = $this->Country->State->City->find('list', array('conditions' => array('City.stateId' => $this->data['Client']['stateId'])));
		}
		$this->set(compact('clientStatusIds','clientTypeIds','regions','clientAcquisitionSourceIds', 'loas', 'themes', 'countryIds', 'stateIds', 'cityIds'));
	}
		
	function search()
	{
	    $inactive = 0;
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
			$inactive = @$_GET['inactive'];
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
		}
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);

			$this->Client->recursive = -1;
			
			$queryPieces = explode(" ", $query);
			
			$sqlquery = '';
			foreach($queryPieces as $piece) {
			    if (strlen($piece) > 3) {
			        $sqlquery .= '+';
			    }
			    $sqlquery .= $piece.'* ';
			}
			
			$conditions = array("MATCH(Client.name) AGAINST('$sqlquery' IN BOOLEAN MODE)");
			
			if (!$inactive) {
			    $conditions['Client.inactive'] = 0;
			}
			$results = $this->Client->find('all', array('conditions' => $conditions, 'limit' => 5));

			$this->set('query', $query);
			$this->set('results', $results);
			
			if (isset($this->params['requested'])) {
				return $results;
			} elseif(@$_GET['query'] || @ $this->params['named']['query']) {
				$this->autoRender = false;
				$this->Client->recursive = 0;

				$this->paginate = array('conditions' => $conditions);
				$this->set('query', $query);
				$this->set('clients', $this->paginate());
				$this->render('index');
			}
		endif;
	}
	
	function rollback($revisionId) {
		$client = $this->Client->rollback($revisionId);

		$this->Session->setFlash(__('The Client has been reverted to revision '.$revisionId, true));
		$this->redirect(array('action' => 'edit', $client['clientId']));
	}
}
?>