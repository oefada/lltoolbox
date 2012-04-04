<?php
class StatesController extends AppController {
	var $name = 'States';
	var $helpers = array('Html', 'Form');

	function __construct() {
		parent::__construct();
		$this->set('hideSidebar',true);
	}

	function index() {
		$this->State->recursive = 0;
		
		if (isset($this->params['named']['query'])) {
			$query = $this->Sanitize->escape($this->params['named']['query']);
			$conditions = array(
				'OR' => array(
					'stateName LIKE' => '%'.$query.'%',
					'stateId LIKE' => '%'.$query.'%',
				),
			);
			
			$this->set('query',$query);
		} else {
			$conditions = array();
		}
		
		$this->paginate = array(
			'order' => 'State.countryId ASC, State.stateId ASC',
			'conditions' => $conditions,
		);
		
		$this->set('states', $this->paginate());
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

		$countries = $this->State->Country->find('list', array('order' => array('Country.countryName') ));
		$this->set(compact('tags', 'countries'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid State', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			$this->State->recursive = -1;
			$stateOld = $this->State->read(null,$id);
			
			if ($this->State->save($this->data)) {
				$this->State->City->updateAll(
					array(
						'State.stateId' => "'".$this->data['State']['stateId']."'",
						'State.countryId' => "'".$this->data['State']['countryId']."'"
					),
					array(
						'City.stateId' => $stateOld['State']['stateId'],
						'City.countryId' => $stateOld['State']['countryId']
					)
				);

				$this->Session->setFlash(__('The State has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The State could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->State->read(null, $id);
		}

		$countries = $this->State->Country->find('list', array('order' => array('Country.countryName') ));
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
	
	function ajax_states() {
		$countryId = $this->params['named']['countryId'];
		
		if (trim($countryId)) {
			$this->State->recursive = -1;
			$this->set('stateId', $this->State->find('list',array(
				'fields' => 'stateId,stateName',
				'conditions' => array(
					'countryId' => $countryId,
				)
			)));
		} else {
			exit;
		}
	}
	
	function get_cities($stateId = null) {
	    if ($stateId == null) {
	        $stateId = $this->data['Client']['stateId'];
	    }
		
		// StateID/Code from geonames DB, not our ID
		list($stateCode,$countryCode) = $this->State->getStateCode($stateId);

		$cityIds = $this->State->City->find('list', array('conditions' => array('City.stateId' => $stateCode, 'City.countryId' => $countryCode ), 'order' => array('City.cityName') ) );		
		$this->set(compact('cityIds'));
		$this->layout = 'ajax';
	}

	function get_cities_locator() {
		list($stateCode,$countryCode) = $this->State->getStateCode($this->params['url']['id']);
		$cityIds = $this->State->City->find('list', array('conditions' => array('City.stateId' => $stateCode, 'City.countryId' => $countryCode ), 'order' => array('City.cityName') ) );		
		echo json_encode(array('cities'=>$cityIds));
		exit;
		
	}

	function search() {
		$this->redirect(array('action'=>'index','query' => $this->params['url']['query']));
	}
}
?>