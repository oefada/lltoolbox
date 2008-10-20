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
			$this->data['Worksheet']['worksheetId'] = $this->data['WorksheetCancellation']['worksheetId'];
			$worksheet = $this->data['Worksheet'];
			$worksheetCancellation = $this->data['WorksheetCancellation'];
			$this->WorksheetCancellation->create();
			if ($this->WorksheetCancellation->save($worksheetCancellation) && $this->WorksheetCancellation->Worksheet->save($worksheet)) {
				$this->Session->setFlash(__('The WorksheetCancellation has been saved', true));
				$this->redirect(array('controller' => 'worksheets', 'action' => 'view', 'id' => $this->data['WorksheetCancellation']['worksheetId']));
			} else {
				$this->Session->setFlash(__('The WorksheetCancellation could not be saved. Please, try again.', true));
			}
		}
		
		$worksheetId = $this->params['worksheetId'];
		
		if (!$worksheetId) {
			$this->Session->setFlash(__('Invalid worksheet ID', true));
			$this->redirect(array('controller' => 'worksheets', 'action'=>'index'));
		} 
		
		$this->set('cancellationReasonIds', $this->WorksheetCancellation->CancellationReason->find('list'));
		$this->set('worksheetStatusIds' , $this->WorksheetCancellation->Worksheet->WorksheetStatus->find('list'));
		$this->data['WorksheetCancellation']['worksheetId'] = $worksheetId;
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