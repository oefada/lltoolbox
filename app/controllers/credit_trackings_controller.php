<?php
class CreditTrackingsController extends AppController {

	var $name = 'CreditTrackings';
	var $helpers = array('Html', 'Form');
	var $canSave = false;
	
	function beforeFilter() {
		parent::beforeFilter();
		
		$currentUser = $this->LdapAuth->user();		
		if (in_array('Accounting',$currentUser['LdapUser']['groups']) || in_array('Geeks',$currentUser['LdapUser']['groups']) || in_array('cof',$currentUser['LdapUser']['groups'])) {
			$this->canSave = true;
		}
		
		$this->set('canSave',$this->canSave);
	}
	
	function index() {
		//$this->CreditTracking->recursive = -1;

		$this->UserSiteExtended->primaryKey = 'userId';
		$conditions = array();
		
		if (isset($this->params['named']['query'])) {
			$query = $this->params['named']['query'];
			$conditions = array(
				'OR' => array(
					'CreditTracking.userId LIKE' => '%'.$query.'%',
					'CreditTracking.userId' => $query,
					'UserSiteExtended.username LIKE' => '%'.$query.'%',
				),
			);
			
			$this->set('query',$query);
		}
		$this->paginate = array(
			'fields' => array(
				'creditTrackingId',
				'balance',
				'userId',
				'datetime'
			),
			'conditions' => $conditions,
			'limit' => 50,
			'order' => array(
				'creditTrackingId' => 'desc',
			),
			'contain' => array(
				'UserSiteExtended' => array(
					'fields' => array(
						'UserSiteExtended.userId',
						'UserSiteExtended.username'
					),
				),
				'User' => array(
					'fields' => array (
						'User.userId',
						'User.email'
					),
				),
			),
		);
		
		$this->set('creditTrackings', $this->paginate());		
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid CreditTracking.', true));
			$this->redirect(array('action'=>'index'));
		}
		// $this->CreditTracking->primaryKey = 'userId';		
		$trackings = $this->CreditTracking->find('all', array('conditions' => array('CreditTracking.userId' => $id), 'order' => array('CreditTracking.creditTrackingId')));
		$this->set('creditTrackings', $trackings);
	}

	function add() {
		$this->canSave();
		if (!empty($this->data)) {
			//$this->CreditTracking->create();
			if ($this->CreditTracking->saveAll($this->data)) {
				$this->Session->setFlash(__('The CreditTracking has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The CreditTracking could not be saved. Please, try again.', true));
			}
		}
		$creditTrackingTypes = $this->CreditTracking->CreditTrackingType->find('list');
		$this->set('creditTrackingTypeIds', $creditTrackingTypes);
		$this->set(compact('creditTrackingTypes'));
	}

	function edit($id = null) {
		$this->canSave();
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid CreditTracking', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->CreditTracking->save($this->data)) {
				$this->Session->setFlash(__('The CreditTracking has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The CreditTracking could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->CreditTracking->read(null, $id);
		}
		$creditTrackingTypes = $this->CreditTracking->CreditTrackingType->find('list');
		$this->set(compact('creditTrackingTypes'));
	}

	function delete($id = null) {
		$this->canSave();
		
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for CreditTracking', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->CreditTracking->del($id)) {
			$this->Session->setFlash(__('Entry deleted', true));
			
			$userId = "";
			$action = "index";
			
			if (isset($this->params['named']['userId'])) {
				$action = "view";
				$userId = $this->params['named']['userId'];
			}
			
			$this->redirect(array('action'=>$action, $userId));
		}
	}

	function search() {
		$this->redirect(array('action'=>'index','query' => $this->params['url']['query']));
	}

	function canSave() {
		if ($this->canSave == false) {
			$this->Session->setFlash('You are not authorized to view this page');
			$this->redirect("/credit_trackings/");
		}
	}	
}
?>