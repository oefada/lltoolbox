<?php
class PpvNoticesController extends AppController {

	var $name = 'PpvNotices';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->PpvNotice->recursive = 0;
		$this->set('ppvNotices', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PpvNotice.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('ppvNotice', $this->PpvNotice->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->PpvNotice->create();
			if ($this->PpvNotice->save($this->data)) {
				$this->Session->setFlash(__('The PpvNotice has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The PpvNotice could not be saved. Please, try again.', true));
			}
		}
		$worksheets = $this->PpvNotice->Worksheet->find('list');
		$this->set(compact('worksheets'));
		
		$this->set('ppvNoticeTypeIds', $this->PpvNotice->PpvNoticeType->find('list'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PpvNotice', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->PpvNotice->save($this->data)) {
				$this->Session->setFlash(__('The PpvNotice has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The PpvNotice could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PpvNotice->read(null, $id);
		}
		$worksheets = $this->PpvNotice->Worksheet->find('list');
		$this->set(compact('worksheets'));
		
		$this->set('ppvNoticeTypeIds', $this->PpvNotice->PpvNoticeType->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for PpvNotice', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PpvNotice->del($id)) {
			$this->Session->setFlash(__('PpvNotice deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>