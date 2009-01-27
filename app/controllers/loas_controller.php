<?php
class LoasController extends AppController {

	var $name = 'Loas';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
	var $paginate;
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
	}

	function index($clientId = null) {
		if(isset($clientId) && $this->Loa->Client->find('count', array('conditions' => array('Client.clientId' => $clientId)))) {
			$this->Loa->recursive = 0;
			$this->set('loas', $this->paginate('Loa', array('Client.clientId' => $clientId)));
		} else {
			$this->cakeError('error404');
		}
		
		$this->set('client', $this->Loa->Client->findByClientId($clientId));
		$this->set('clientId', $clientId);
	}

	function view($id = null) {
		$this->redirect(array('action' => 'edit', $id));
	}

	function add($clientId = null) {
		if (!empty($this->data)) {
			$clientId = $this->data['Loa']['clientId'];
			$this->Loa->create();
			if ($this->Loa->save($this->data)) {
				$this->Session->setFlash(__('The Loa has been saved', true));
				$this->redirect("/clients/$clientId/loas");
			} else {
				$this->Session->setFlash(__('The Loa could not be saved. Please, try again.', true));
			}
		}
		
		if(!$clientId) {
			$this->Session->setFlash(__('Incorrect client id specified. Please try again.', true));
			$this->redirect(array('controller' => 'clients', 'action' => 'index'));
		}
		$this->data['Loa']['clientId'] = $clientId;
		$customerApprovalStatusIds = $this->Loa->LoaCustomerApprovalStatus->find('list');

		$this->Loa->Client->recursive = 1;
		$client = $this->Loa->Client->find('Client.clientId = '.$clientId, 'name');
		$currencyIds = $this->Loa->Currency->find('list');
		$loaLevelIds = $this->Loa->LoaLevel->find('list');
		$this->set('clientName', $client['Client']['name']);
		$this->set('client', $this->Loa->Client->findByClientId($clientId));
		$this->set(compact('customerApprovalStatusIds', 'currencyIds', 'loaLevelIds'));
		
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Loa', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Loa->save($this->data)) {
				$this->Session->setFlash(__('The Loa has been saved', true));
				$this->redirect(array('action'=>'edit', $this->data['Loa']['loaId']));
			} else {
				$loa = $this->Loa->find($this->data['Loa']['loaId']);
				$this->data['Client'] = $loa['Client'];
				$this->Session->setFlash(__('The Loa could not be saved. Please, try again.', true));
			}
		}
		$this->Loa->recursive = 2;
		if (empty($this->data)) {
			$this->data = $this->Loa->read(null, $id);
		}
		$customerApprovalStatusIds = $this->Loa->LoaCustomerApprovalStatus->find('list');
		$currencyIds = $this->Loa->Currency->find('list');
		$loaLevelIds = $this->Loa->LoaLevel->find('list');
		$this->set(compact('customerApprovalStatusIds', 'currencyIds', 'loaLevelIds'));
		$this->set('client', $this->Loa->Client->findByClientId($this->data['Loa']['clientId']));
		$this->set('currencyCodes', $this->Loa->Currency->find('list', array('fields' => array('currencyCode'))));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Loa', true));
			$this->redirect(array('controller' => 'clients', 'action'=>'index'));
		}
		$this->Loa->recursive = -1;
		$clientId = $this->Loa->read('Loa.clientId', $id);
		$clientId = $clientId['Loa']['clientId'];
		
		if ($this->Loa->del($id)) {
			$this->Session->setFlash(__('Loa deleted', true));
			$this->redirect("/clients/$clientId/loas");
		}
	}
	
	/*
	 * Finds an LOA by id and gets the expiration date
	 * Mainly used as an ajax call from the package interface
	 * @params $loaId the id of the loa to find the expiration date of
	 * @returns the expiration date
	 */
	function getExpiration($loaId = null) {
		$this->autoRender = false;
		
		if(!empty($this->data['ClientLoaPackageRel']) && null === $loaId) {
			$clientLoaPackageRel = array_pop($this->data['ClientLoaPackageRel']);
			$loaId = $clientLoaPackageRel['loaId'];
		}
		$loa = $this->Loa->findByLoaId($loaId);

		return $loa['Loa']['endDate'];
	}
}
?>