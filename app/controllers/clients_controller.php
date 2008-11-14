<?php

App::import('Vendor', 'nusoap_client/lib/nusoap');

class ClientsController extends AppController {

	var $name = 'Clients';

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('clientId', $this->Client->id);
	}
	
	function index() {
		$this->Client->recursive = 0;

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
			if ($this->Client->save($this->data)) {
				$this->Session->setFlash(__('The Client has been saved', true));
				$this->redirect(array('action'=>'edit', 'id' => $id));
			} else {
				$this->Session->setFlash(__('The Client could not be saved. Please, try again.', true));
			}
			$this->set('submission', true);
		}
		//set up our data, if it's a form post, we still need all related data
		if (empty($this->data)) {
			$this->data = $this->Client->read(null, $id);
		} else {
			if(isset($this->data['Amenity']['Amenity'])):
				$amenities = $this->Client->Amenity->find('all', array('conditions' => array('Amenity.amenityId' => $this->data['Amenity']['Amenity'])));
				$this->data['Amenity'] = array();
				foreach($amenities as $amenity):
					$this->data['Amenity'][] = $amenity['Amenity'];
				endforeach;
			else:
				$this->data['Amenity'] = array();
			endif;
		}

		$amenities = $this->Client->Amenity->find('list');
		$clientLevelIds = $this->Client->ClientLevel->find('list');
		$clientStatusIds = $this->Client->ClientStatus->find('list');
		$clientTypeIds = $this->Client->ClientType->find('list');
		$clientAcquisitionSourceIds = $this->Client->ClientAcquisitionSource->find('list');
		$themes = $this->Client->Theme->find('list');
		$this->set('client', $this->data);
		//$this->set(compact('addresses', 'amenities','clientLevelIds','clientStatusIds','clientTypeIds','regions','clientAcquisitionSourceIds', 'loas', 'themes'));
		$this->set(compact('amenities','clientLevelIds','clientStatusIds','clientTypeIds','regions','clientAcquisitionSourceIds', 'loas', 'themes'));
	}
	
	function updateClientLive($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Client', true));
			$this->redirect(array('action'=>'index'));
		}
		
		$this->Client->recursive = 2;
		$client = $this->Client->read(null, $id);
		unset($client['Loa']);
		unset($client['Region']);
		unset($client['ClientAcquisitionSource']);
		unset($client['Audit']);
		unset($client['Tag']);
		unset($client['User']);
		
		$webservice_live_url = 'http://livedev.luxurylink.com/web_services/update_client.php?wsdl';
		$webservice_live_method_name = 'updateClient';
		$webservice_live_method_param = 'in0';

		$soap_client = new nusoap_client($webservice_live_url, true);

		$data_json_encoded = json_encode($client);

        $response = $soap_client->call($webservice_live_method_name, array($webservice_live_method_param => $data_json_encoded));

		echo $response;
 				
		die();
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
	
	function rollback($revisionId) {
		$client = $this->Client->rollback($revisionId);

		$this->Session->setFlash(__('The Client has been reverted to revision '.$revisionId, true));
		$this->redirect(array('action' => 'edit', $client['clientId']));
	}
}
?>