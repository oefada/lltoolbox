<?php
class WorksheetRefundsController extends AppController {

	var $name = 'WorksheetRefunds';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->WorksheetRefund->recursive = 0;
		$this->set('worksheetRefunds', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid WorksheetRefund.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('worksheetRefund', $this->WorksheetRefund->read(null, $id));
	}

	function add() {
		if (!empty($this->data) && $this->data['WorksheetRefund']['worksheetId']) {
			$worksheet = array();
			$worksheet['Worksheet']['worksheetId'] = $this->data['WorksheetRefund']['worksheetId'];
			$worksheet['Worksheet']['worksheetStatusId'] = 8;
			$this->WorksheetRefund->create();
			if ($this->WorksheetRefund->save($this->data)  && $this->WorksheetRefund->Worksheet->save($worksheet)) {
				$this->Session->setFlash(__('The WorksheetRefund has been saved', true));
				$this->redirect(array('controller' => 'worksheets', 'action' => 'view', 'id' => $this->data['WorksheetRefund']['worksheetId']));
			} else {
				$this->Session->setFlash(__('The WorksheetRefund could not be saved. Please, try again.', true));
			}
		}
		
		$worksheetId = $this->params['worksheetId'];
		
		if (!$worksheetId) {
			$this->Session->setFlash(__('Invalid worksheet ID', true));
			$this->redirect(array('controller' => 'worksheets', 'action'=>'index'));
		} 
		
		$this->set('refundReasonIds', $this->WorksheetRefund->RefundReason->find('list'));
		$this->data['WorksheetRefund']['worksheetId'] = $worksheetId;
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid WorksheetRefund', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->WorksheetRefund->save($this->data)) {
				$this->Session->setFlash(__('The WorksheetRefund has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The WorksheetRefund could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->WorksheetRefund->read(null, $id);
		}
		
		$this->set('refundReasonIds', $this->WorksheetRefund->RefundReason->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for WorksheetRefund', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->WorksheetRefund->del($id)) {
			$this->Session->setFlash(__('WorksheetRefund deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>