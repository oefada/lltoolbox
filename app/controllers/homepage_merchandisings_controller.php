<?php
class HomepageMerchandisingsController extends AppController {

	var $name = 'HomepageMerchandisings';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->HomepageMerchandising->recursive = 0;
		$this->set('homepageMerchandisings', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid HomepageMerchandising.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('homepageMerchandising', $this->HomepageMerchandising->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->HomepageMerchandising->create();
			if ($this->HomepageMerchandising->save($this->data)) {
				$this->Session->setFlash(__('The HomepageMerchandising has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The HomepageMerchandising could not be saved. Please, try again.', true));
			}
		}
		$homepageMerchandisingTypeIds = $this->HomepageMerchandising->HomepageMerchandisingType->find('list');
		$this->set('homepageMerchandisingTypeIds', $homepageMerchandisingTypeIds);
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid HomepageMerchandising', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->HomepageMerchandising->save($this->data)) {
				$this->Session->setFlash(__('The HomepageMerchandising has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The HomepageMerchandising could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->HomepageMerchandising->read(null, $id);
		}
		$homepageMerchandisingTypeIds = $this->HomepageMerchandising->HomepageMerchandisingType->find('list');
		$this->set('homepageMerchandisingTypeIds', $homepageMerchandisingTypeIds);
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for HomepageMerchandising', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->HomepageMerchandising->del($id)) {
			$this->Session->setFlash(__('HomepageMerchandising deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>
