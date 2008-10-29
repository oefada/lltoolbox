<?php
class AddressesController extends AppController {

	var $name = 'Addresses';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Address->recursive = 0;
		$this->set('addresses', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Address.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('address', $this->Address->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Address->create();
			if ($this->Address->save($this->data)) {
				$this->Session->setFlash(__('The Address has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Address could not be saved. Please, try again.', true));
			}
		}
		$addressTypeIds = $this->Address->AddressType->find('list');
		$countryIds = $this->Address->Country->find('list');	
		$this->set(compact('addressTypeIds', 'countryIds', 'stateIds', 'cityIds'));
	}
	
	function getStatesFromCountry() {
		$this->autoRender = false;
		$countryId = $this->data['Address']['countryId'];
		$this->Address->State->recursive = -1;
		$stateIds = $this->Address->State->find('', array('conditions' => array('countryId' => $countryId)));
		$this->set(compact('stateIds'));
		$this->render('state_and_city_chooser');
	}
	
	function getCitiesFromState() {
		$stateIds = $this->Address->State->findAllByCityId();
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Address', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Address->save($this->data)) {
				$this->Session->setFlash(__('The Address has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Address could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Address->read(null, $id);
		}
		$addressTypes = $this->Address->AddressType->find('list');
		$this->set(compact('addressTypes','users','clients','countries','states','cities'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Address', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Address->del($id)) {
			$this->Session->setFlash(__('Address deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>