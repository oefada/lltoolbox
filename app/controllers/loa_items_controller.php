<?php
class LoaItemsController extends AppController {

	var $name = 'LoaItems';
	var $helpers = array('Html', 'Form', 'Ajax');
	var $components = array('RequestHandler');

	function index() {
		$this->LoaItem->recursive = 0;
		$this->set('loaItems', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid LoaItem.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('loaItem', $this->LoaItem->read(null, $id));
		
		$loaItemTypeIds = $this->LoaItem->LoaItemType->find('list');
		$this->set('loaItemTypeIds', ($loaItemTypeIds));
	}

	function add() {
		if (!empty($this->data)) {
			$this->LoaItem->create();
			if ($this->LoaItem->saveAll($this->data)) {
				$this->Session->setFlash(__('The LoaItem has been saved', true));
				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
					$this->Session->setFlash(__('The LoaItem has been saved', true), 'default', array(), 'success');
				} else {
					$this->redirect(array('controller' => 'loas', 'action'=>'view', 'id' => $this->params['data']['LoaItem']['loaId']));
				}
			} else {
				$this->Session->setFlash(__('The LoaItem could not be saved. Please, try again.', true));
			}
		}
		
		$loaItemTypeIds = $this->LoaItem->LoaItemType->find('list');
		$this->set('loaItemTypeIds', ($loaItemTypeIds));
		
		$feeTypeIds = $this->LoaItem->Fee->FeeType->find('list');
		$this->set('feeTypeIds', ($feeTypeIds));
		$loa = $this->LoaItem->Loa->findByLoaId($this->params['loaId']);
		$this->set('currencyId', $loa['Loa']['currencyId']);

		$this->data['LoaItem']['loaId'] = $this->params['loaId'];
	}
	
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid LoaItem', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			
			// Save fee
			$this->LoaItem->Fee->feeId = $this->data['Fee']['feeId'];
			$this->LoaItem->Fee->set($this->data);
			
			/*
			// Save rate period
			$this->LoaItem->LoaItemRatePeriod->loaItemRatePeriodId = $this->data['LoaItemRatePeriod']['loaItemRatePeriodId'];
			$this->LoaItem->LoaItemRatePeriod->set($this->data);
			*/
			
			if ($this->LoaItem->save($this->data) /*&& $this->LoaItem->LoaItemRatePeriod->save()*/ ) {	
				
				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
					$this->Session->setFlash(__('The LoaItem has been saved', true), 'default', array(), 'success');
				} else {
					$this->redirect(array('controller' => 'loas', 'action'=>'view', 'id' => $this->params['data']['LoaItem']['loaId']));
				}
			} else {
				$this->Session->setFlash(__('The LoaItem could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->LoaItem->read(null, $id);
		}
		$loaItemTypeIds = $this->LoaItem->LoaItemType->find('list');
		$this->set('loaItemTypeIds', ($loaItemTypeIds));
		
		$feeTypeIds = $this->LoaItem->Fee->FeeType->find('list');
		$this->set('feeTypeIds', ($feeTypeIds));
		
		$currencyIds = $this->LoaItem->Currency->find('list');
		$this->set(compact('currencyIds'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for LoaItem', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->autoRender = false;
		$this->LoaItem->recursive = -1;
		$loaItem = $this->LoaItem->read(null, $id);
		if ($this->LoaItem->del($id)) {
			$this->Session->setFlash(__('LoaItem deleted', true));
			$this->redirect(array('controller' => 'loas', 'action'=>'edit', $loaItem['LoaItem']['loaId']));
		}
	}
}
?>