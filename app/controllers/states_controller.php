<?php
class StatesController extends AppController {

	var $name = 'States';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->State->recursive = 0;
		$this->set('states', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid State.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('state', $this->State->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->State->create();
			if ($this->State->save($this->data)) {
				$this->Session->setFlash(__('The State has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The State could not be saved. Please, try again.', true));
			}
		}
		$tags = $this->State->Tag->find('list');
		$countries = $this->State->Country->find('list');
		$this->set(compact('tags', 'countries'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid State', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->State->save($this->data)) {
				$this->Session->setFlash(__('The State has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The State could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->State->read(null, $id);
		}
		$tags = $this->State->Tag->find('list');
		$countries = $this->State->Country->find('list');
		$this->set(compact('tags','countries'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for State', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->State->del($id)) {
			$this->Session->setFlash(__('State deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function get_cities($stateId = null) {
	    if ($stateId == null) {
	        $stateId = $this->data['Client']['stateId'];
	    }
		$cityIds = $this->State->City->find('list', array('conditions' => array('City.stateId' => $stateId ) ) );		
		$this->set(compact('cityIds'));
		$this->layout = 'ajax';
	}

}
?>