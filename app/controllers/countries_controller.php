<?php
class CountriesController extends AppController {

	var $name = 'Countries';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Country->recursive = 0;
		
		if (isset($this->params['named']['query'])) {
			$query = $this->Sanitize->escape($this->params['named']['query']);
			$conditions = array(
				'OR' => array(
					'countryName LIKE' => '%'.$query.'%',
					'countryId LIKE' => '%'.$query.'%',
				),
			);
			
			$this->set('query',$query);
		} else {
			$conditions = array();
		}

		$this->paginate = array(
			'order' => 'countryId ASC',
			'conditions' => $conditions,
		);

		$this->set('countries', $this->paginate());
	}

	function add() {
		$this->redirect(array('action'=>'index'));
		if (!empty($this->data)) {
			$this->Country->create();
			if ($this->Country->save($this->data)) {
				$this->Session->setFlash(__('The Country has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Country could not be saved. Please, try again.', true));
			}
		}
		$tags = $this->Country->Tag->find('list');
		$this->set(compact('tags'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Country', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Country->save($this->data)) {
				$this->Session->setFlash(__('The Country has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Country could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Country->read(null, $id);
		}
		$tags = $this->Country->Tag->find('list');
		$this->set(compact('tags'));
	}

	function delete($id = null) {
		$this->redirect(array('action'=>'index'));
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Country', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Country->del($id)) {
			$this->Session->setFlash(__('Country deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function search() {
		$this->redirect(array('action'=>'index','query' => $this->params['url']['query']));
	}
	
	function get_countries_json() {
		$this->Country->recursive = -1;
		$countryList = $this->Country->find('list');
		$this->set(compact('countryList'));
		$this->layout = 'ajax';
	}
	
	function get_states() {
	    $stateIds = $this->Country->State->find('list', array('conditions' => array('State.countryId' => $this->data['Client']['countryId'])));
		$this->set(compact('stateIds'));
		$this->layout = 'ajax';
	}

}
?>