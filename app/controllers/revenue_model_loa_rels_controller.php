<?php
class RevenueModelLoaRelsController extends AppController {

	var $name = 'RevenueModelLoaRels';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->RevenueModelLoaRel->recursive = 0;
		$this->set('revenueModelLoaRels', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid RevenueModelLoaRel.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('revenueModelLoaRel', $this->RevenueModelLoaRel->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->RevenueModelLoaRel->create();
			if ($this->RevenueModelLoaRel->save($this->data)) {
				$this->Session->setFlash(__('The RevenueModelLoaRel has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The RevenueModelLoaRel could not be saved. Please, try again.', true));
			}
		}
		$expirationCriteria = $this->RevenueModelLoaRel->ExpirationCriterium->find('list');
		$revenueModels = $this->RevenueModelLoaRel->RevenueModel->find('list');
		$this->set('expirationCriteriaIds', $expirationCriteria);
		$this->set('revenueModelIds', $revenueModels);
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid RevenueModelLoaRel', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->RevenueModelLoaRel->save($this->data)) {
				$this->Session->setFlash(__('The RevenueModelLoaRel has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The RevenueModelLoaRel could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->RevenueModelLoaRel->read(null, $id);
		}
		$expirationCriteria = $this->RevenueModelLoaRel->ExpirationCriterium->find('list');
		$revenueModels = $this->RevenueModelLoaRel->RevenueModel->find('list');
		$this->set(compact('expirationCriteria','revenueModels'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for RevenueModelLoaRel', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->RevenueModelLoaRel->del($id)) {
			$this->Session->setFlash(__('RevenueModelLoaRel deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>