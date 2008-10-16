<?php
class WorksheetsController extends AppController {

	var $name = 'Worksheets';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Worksheet->recursive = 0;
		$this->set('worksheets', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Worksheet.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('worksheet', $this->Worksheet->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Worksheet->create();
			if ($this->Worksheet->save($this->data)) {
				$this->Session->setFlash(__('The Worksheet has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Worksheet could not be saved. Please, try again.', true));
			}
		}
		
		$this->set('worksheetStatusIds', $this->Worksheet->WorksheetStatus->find('list'));
		
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Worksheet', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Worksheet->save($this->data)) {
				$this->Session->setFlash(__('The Worksheet has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Worksheet could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Worksheet->read(null, $id);
		}
		$this->set('worksheetStatusIds', $this->Worksheet->WorksheetStatus->find('list'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Worksheet', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Worksheet->del($id)) {
			$this->Session->setFlash(__('Worksheet deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function updateWorksheetStatus($id = null, $worksheetStatusId = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Worksheet', true));
			$this->redirect(array('action'=>'index'));				
		}	
		$worksheetStatusIds = $this->Worksheet->WorksheetStatus->find('list');
		if (!$worksheetStatusId || !isset($worksheetStatusIds[$worksheetStatusId])) {
			$this->Session->setFlash(__('Invalid attempt to update workstatus', true));
			$this->redirect(array('action'=>'view', 'id' => $id));				
		} else {
			$worksheet['Worksheet']['worksheetId'] = $id;
			$worksheet['Worksheet']['worksheetStatusId'] = $worksheetStatusId;
			if ($this->Worksheet->save($worksheet)) {
				$this->Session->setFlash(__("Workstatus has been updated to \"$worksheetStatusIds[$worksheetStatusId]\"", true));
			} else {
				$this->Session->setFlash(__('Worksheet status has NOT been updated', true));
			}
			$this->redirect(array('action'=>'view', 'id' => $id));						
		}
	}

}
?>