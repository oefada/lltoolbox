<?php
class TracksController extends AppController {

	var $name = 'Tracks';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Track->recursive = 0;
		$this->set('tracks', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Track.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('track', $this->Track->read(null, $id));
	}

	function add($loaId) {
		if (!empty($this->data)) {
			$this->data['Track']['loaId'] = $loaId;
			$this->Track->create();
			if ($this->Track->save($this->data)) {
				$this->Session->setFlash(__('The Track has been saved', true));
				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
				} else {
					$this->redirect(array('action'=>'index'));
				}
			} else {
				$this->Session->setFlash(__('The Track could not be saved. Please, try again.', true));
			}
		}

		$this->data['Track']['loaId'] = $loaId;
		$expirationCriteriaIds = $this->Track->ExpirationCriterium->find('list');
		$revenueModelIds = $this->Track->RevenueModel->find('list');
		$this->set(compact('expirationCriteriaIds', 'revenueModelIds'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Track', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Track->save($this->data)) {
				$this->Session->setFlash(__('The Track has been saved', true));
				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
				} else {
					$this->redirect(array('action'=>'index'));
				}
			} else {
				$this->Session->setFlash(__('The Track could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Track->read(null, $id);
		}

		$expirationCriteriaIds = $this->Track->ExpirationCriterium->find('list');
		$revenueModelIds = $this->Track->RevenueModel->find('list');
		$this->set(compact('expirationCriteriaIds', 'revenueModelIds'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Track', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Track->inUse($id)) {
			$this->Session->setFlash(__('Track is currently in use and can not be deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Track->del($id)) {
			$this->Session->setFlash(__('Track deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	function revenue_model_criteria_form() {
		$this->autoRender = false;
		switch($this->data['Track']['revenueModelId']):
			case 1:
				$fileToRender = '_revenue_split_form';
			break;
			case 2:
			case 3:
			case 5:
				$fileToRender = '_xy_form';
			break;
			case 4:
				$fileToRender = '_xy_commission_form';
			break;
			default:
				$fileToRender = '';
		endswitch;

		$this->render(null, false, $fileToRender);
	}

	function expiration_criteria_form() {
		if(is_numeric($this->data['Track']['expirationCriteriaId'])) {

			// set Exp Date from loaId in form
			if (!isset($this->data['Track']['expDate'])) {
				$this->data['Track']['expDate']=($this->Track->getLoaEndDate($this->data['Track']['loaId']));
			}

			$this->render(null, false, '_exp_criteria_'.$this->data['Track']['expirationCriteriaId']);
		}
	}
}
?>
