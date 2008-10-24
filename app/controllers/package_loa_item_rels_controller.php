<?php
class PackageLoaItemRelsController extends AppController {

	var $name = 'PackageLoaItemRels';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->PackageLoaItemRel->recursive = 0;
		$this->set('packageLoaItemRels', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PackageLoaItemRel.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('packageLoaItemRel', $this->PackageLoaItemRel->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->PackageLoaItemRel->create();
			if ($this->PackageLoaItemRel->save($this->data)) {
				$this->Session->setFlash(__('The PackageLoaItemRel has been saved', true));
				$this->redirect(array('controller' => 'packages', 'action'=>'carveRatePeriods', 'id' => $this->data['PackageLoaItemRel']['packageId']));
			} else {
				$this->Session->setFlash(__('The PackageLoaItemRel could not be saved. Please, try again.', true));
			}
		}
		
		$loaItemIds = $this->PackageLoaItemRel->LoaItem->find('list');
		$this->set('loaItemIds', ($loaItemIds));
		
		$this->data['PackageLoaItemRel']['packageId'] = $this->params['packageId'];
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PackageLoaItemRel', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->PackageLoaItemRel->save($this->data)) {
				$this->Session->setFlash(__('The PackageLoaItemRel has been saved', true));
				$this->redirect(array('controller' => 'packages', 'action'=>'carveRatePeriods', 'id' => $this->data['PackageLoaItemRel']['packageId']));
			} else {
				$this->Session->setFlash(__('The PackageLoaItemRel could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PackageLoaItemRel->read(null, $id);
		}
		$loaItemIds = $this->PackageLoaItemRel->LoaItem->find('list');
		$this->set('loaItemIds', ($loaItemIds));
	}

	function delete($id = null) {
		$packageLoaItemRel = $this->PackageLoaItemRel->read(null);
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for PackageLoaItemRel', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PackageLoaItemRel->del($id)) {
			$this->Session->setFlash(__('PackageLoaItemRel deleted', true));
			$this->redirect(array('controller' => 'packages', 'action'=>'carveRatePeriods', 'id' => $packageLoaItemRel['PackageLoaItemRel']['packageId']));
		}
	}

}
?>