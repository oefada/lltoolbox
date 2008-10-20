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
		$this->createNewWorksheetFromWorksheet($id);
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
	
	function createNewWorksheetFromWorksheet($id = null) {
		if (!$id) {
			return false;	
		}
		
		//attach  containable behavior, can also be done in the model through an actsAs
		$this->Worksheet->Behaviors->attach('Containable');
		
		//choose which child/sibling models we want to return besides the current one
		$this->Worksheet->contain('Offer');
		
		//do a normal read
		$worksheetData = $this->Worksheet->read(null, $id);
		$offerId = $worksheetData['Offer']['offerId'];
		
		//we only get Worksheet and whatevr we contained by, in this case, Offer
		debug($worksheetData);
		
	}

}
?>