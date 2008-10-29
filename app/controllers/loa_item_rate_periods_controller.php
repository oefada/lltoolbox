<?php
class LoaItemRatePeriodsController extends AppController {

	var $name = 'LoaItemRatePeriods';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->LoaItemRatePeriod->recursive = 0;
		$this->set('loaItemRatePeriods', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid LoaItemRatePeriod.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('loaItemRatePeriod', $this->LoaItemRatePeriod->read(null, $id));
	}

	function add($loaId) {
		if (!empty($this->data)) {
			$this->LoaItemRatePeriod->create();
			if ($this->LoaItemRatePeriod->save($this->data)) {
				$this->Session->setFlash(__('The LOA Item Rate Period has been saved', true));
				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
				} else {
					$this->redirect(array('controller' => 'loa_items', 'action'=>'edit', 'id' => $this->params['data']['LoaItem']['loaItemId']));
				}
			} else {
				$this->Session->setFlash(__('The LoaItemRatePeriod could not be saved. Please, try again.', true));
			}
		}
		/*$loaItems = $this->LoaItemRatePeriod->LoaItem->find('list');
		$this->set(compact('loaItems'));*/
		
		$this->data['LoaItemRatePeriod']['loaItemId'] = $this->params['loaItemId'];
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid LoaItemRatePeriod', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->LoaItemRatePeriod->save($this->data)) {
				$this->Session->setFlash(__('The LoaItemRatePeriod has been saved', true));
				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
				} else {
					$this->redirect(array('controller' => 'loa_items', 'action'=>'edit', 'id' => $this->params['data']['LoaItem']['loaItemId']));
				}
			} else {
				$this->Session->setFlash(__('The LoaItemRatePeriod could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->LoaItemRatePeriod->read(null, $id);
		}
		$loaItems = $this->LoaItemRatePeriod->LoaItem->find('list');
		$this->set(compact('loaItems'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for LoaItemRatePeriod', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->LoaItemRatePeriod->del($id)) {
			if($this->RequestHandler->isAjax()) {
				$this->autoRender = false;
				return true;
			}
			$this->Session->setFlash(__('LoaItemRatePeriod deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function getRatePeriodsForItem($itemId) {
		$loaItems = $this->LoaItemRatePeriod->LoaItem->findByLoaItemId($itemId);
		$loaItem = array_merge_recursive($loaItems, $loaItems['LoaItem']);

		$this->set('loaItem', $loaItem);
		$this->render(null, 'ajax', '/elements/loa_item_rate_periods/table_for_loas_page');
	}

}
?>