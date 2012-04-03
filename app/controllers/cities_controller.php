<?php
class CitiesController extends AppController {

	var $name = 'Cities';
	var $helpers = array('Html', 'Form');

	function __construct() {
		parent::__construct();
		$this->set('hideSidebar',true);
	}
	
	function index() {
		//$this->City->recursive = -1;
		
		
		if (isset($this->params['named']['query'])) {
			$query = $this->Sanitize->escape($this->params['named']['query']);
			$conditions = array(
				'OR' => array(
					'cityName LIKE' => '%'.$query.'%',
				),
			);
			
			$this->set('query',$query);
		} else {
			$conditions = array();
		}

	
		$this->paginate = array(
/*			'contain' => array(
				'City',
				'State',
				'Country'
			),*/
			'conditions' => $conditions,
		);
		
		//$results = $this->query("SELECT * FROM cityNew as City, stateNew as State, countryNew as Country WHERE City.stateId = State.stateId AND City.countryId = State.countryId AND City.countryId = Country.countryId");
		$this->set('cities', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid City.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('city', $this->City->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->City->create();
			if ($this->City->save($this->data)) {
				$this->Session->setFlash(__('The City has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The City could not be saved. Please, try again.', true));
			}
		}
		
		//$tags = $this->City->Tag->find('list');
		$states = $this->City->State->find('list');
		$countries = $this->City->Country->find('list');
		//$this->set(compact('tags', 'states', 'countries'));
		$this->set(compact('states', 'countries'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid City', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->City->save($this->data)) {
				$this->Session->setFlash(__('The City has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The City could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->City->read(null, $id);
		}
		
		//$tags = $this->City->Tag->find('list');
		//$states = $this->City->State->find('list');
		$countries = $this->City->Country->find('list');
		//$this->set(compact('tags','states','countries'));
		$this->set(compact('states', 'countries'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for City', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->City->del($id)) {
			$this->Session->setFlash(__('City deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function enable($id) {
		$this->disable($id,true);
	}

	function disable($id,$enable = false) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for City', true));
			$this->redirect(array('action'=>'index'));
		}

		$word = "disabled";
		$isDisabled = 1;
		
		if ($enable) {
			$word = "enabled";
			$isDisabled = 0;
		}
		
		$this->data = array('City'=>array('cityId' => $id, 'isDisabled' => $isDisabled));
		if ($this->City->save($this->data)) {
			$this->Session->setFlash(__('City '.$word.'!', true));
		} else {
			$this->Session->setFlash(__('Error saving city!', true));
		}
		
		$this->redirect(array('action'=>'index'));
	}
	
	function search() {
		$this->redirect(array('action'=>'index','query' => $this->params['url']['query']));
	}

	function ajaxinfo() {
		$city = $this->City->read(null, $this->params['url']['id']);
		$info = array('cid'=>$city['Country']['id'], 'countryName'=>$city['Country']['countryName'], 'sid'=>$city['State']['id'], 'stateName'=>$city['State']['stateName'], 'cityName'=>$city['City']['cityName']);
		echo json_encode(array('info'=>$info));
		exit;
	}

}
?>