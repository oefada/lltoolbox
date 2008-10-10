<?php
class WorksheetCancellationsController extends AppController {

	var $name = 'WorksheetCancellations';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->WorksheetCancellation->recursive = 0;
		$this->set('worksheetCancellations', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid WorksheetCancellation.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('worksheetCancellation', $this->WorksheetCancellation->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->WorksheetCancellation->create();
			if ($this->WorksheetCancellation->save($this->data)) {
				$this->Session->setFlash(__('The WorksheetCancellation has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The WorksheetCancellation could not be saved. Please, try again.', true));
			}
		}
		
		$this->set('cancellationReasonIds', $this->WorksheetCancellation->CancellationReason->find('list'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid WorksheetCancellation', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->WorksheetCancellation->save($this->data)) {
				$this->Session->setFlash(__('The WorksheetCancellation has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The WorksheetCancellation could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->WorksheetCancellation->read(null, $id);
		}
		$this->set('cancellationReasonIds', $this->WorksheetCancellation->CancellationReason->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for WorksheetCancellation', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->WorksheetCancellation->del($id)) {
			$this->Session->setFlash(__('WorksheetCancellation deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>