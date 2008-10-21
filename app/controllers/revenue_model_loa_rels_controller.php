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

	function add($loaId) {
		if (!empty($this->data)) {
			$this->data['RevenueModelLoaRel']['loaId'] = $loaId;
			$this->RevenueModelLoaRel->create();
			if ($this->RevenueModelLoaRel->save($this->data)) {
				$this->Session->setFlash(__('The RevenueModelLoaRel has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The RevenueModelLoaRel could not be saved. Please, try again.', true));
			}
		}
		
		$this->data['RevenueModelLoaRel']['loaId'] = $loaId;
		$expirationCriteriaIds = $this->RevenueModelLoaRel->ExpirationCriterium->find('list');
		$revenueModelIds = $this->RevenueModelLoaRel->RevenueModel->find('list');
		$this->set(compact('expirationCriteriaIds', 'revenueModelIds'));
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
	function revenue_model_criteria_form() {
		debug($this->data['RevenueModelLoaRel']['revenueModelId']);
		$this->autoRender = false;
		switch($this->data['RevenueModelLoaRel']['revenueModelId']):
			case 1:
				$fileToRender = '_revenue_split_form';
			break;
			case 2:
			case 3:
				$fileToRender = '_xy_form';
			break;
			default:
				$fileToRender = '';
		endswitch;
		
		$this->render(null, false, $fileToRender);
	}
	
	function expiration_criteria_form() {
		if(is_numeric($this->data['RevenueModelLoaRel']['expirationCriteriaId'])) {
			$this->render(null, false, '_exp_criteria_'.$this->data['RevenueModelLoaRel']['expirationCriteriaId']);
		}
	}
}
?>