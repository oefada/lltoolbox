<?php
class SchedulingMastersController extends AppController {

	var $name = 'SchedulingMasters';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->SchedulingMaster->recursive = 0;
		$this->set('schedulingMasters', $this->paginate());
	}

	function view($id = null) {
		$this->SchedulingMaster->recursive = 2;
		if (!$id) {
			$this->Session->setFlash(__('Invalid SchedulingMaster.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('schedulingMaster', $this->SchedulingMaster->read(null, $id));
		$this->set('temp', $this->SchedulingMaster->SchedulingInstance->find('all'));
	}

	function add() {
		if (!empty($this->data)) {
			$this->SchedulingMaster->create();
			
			if ($this->SchedulingMaster->save($this->data)) {
				$this->createInstances();
				if ($this->RequestHandler->isAjax()) {
					$this->Session->setFlash(__('The Schedule has been saved', true), 'default', array(), 'success');
					$this->set('closeModalbox', true);
				}
			} else {
				$this->Session->setFlash(__('The SchedulingMaster could not be saved. Please, try again.', true));
			}
		}
		 
		if (empty($this->data) && isset($this->params['named']['date'])) {
			$date = explode('-', $this->params['named']['date']);
			$this->data['SchedulingMaster']['startDate']['year'] = $date[0];
			$this->data['SchedulingMaster']['startDate']['month'] = $date[1];
			$this->data['SchedulingMaster']['startDate']['day'] = $date[2];
		}
		$merchandisingFlags = $this->SchedulingMaster->MerchandisingFlag->find('list');
		$schedulingStatusIds = $this->SchedulingMaster->SchedulingStatus->find('list');
		$schedulingDelayCtrlIds = $this->SchedulingMaster->SchedulingDelayCtrl->find('list');
		$remittanceTypeIds = $this->SchedulingMaster->RemittanceType->find('list');
		$packageId = $this->params['named']['packageId'];
		$package = $this->SchedulingMaster->Package->findByPackageId($packageId);
		
		
		$this->set('package', $package);
		$this->set('packageId', $packageId);
		$this->set('merchandisingFlags', $merchandisingFlags);
		$this->set('schedulingStatusIds', $schedulingStatusIds);
		$this->set('schedulingDelayCtrlIds', $schedulingDelayCtrlIds);
		$this->set('remittanceTypeIds', $remittanceTypeIds);
	}

	function createInstances() {
		$masterData = $this->data;
		$iterations = $masterData['SchedulingMaster']['iterations'];
		if ($iterations == 0) $iterations = 1;

		$schedulingDelayCtrlDesc = $this->SchedulingMaster->SchedulingDelayCtrl->findBySchedulingDelayCtrlId($masterData['SchedulingMaster']['schedulingDelayCtrlId']);
		$schedulingDelayCtrlDesc = $schedulingDelayCtrlDesc['SchedulingDelayCtrl']['schedulingDelayCtrlDesc'];
		
		$instanceData = array();
		$instanceData['SchedulingInstance']['schedulingMasterId'] = $masterData['SchedulingMaster']['schedulingMasterId'];
		$instanceData['SchedulingInstance']['startDate'] = $masterData['SchedulingMaster']['startDate'];

		$startDate = $instanceData['SchedulingInstance']['startDate']['year'].'-'.$instanceData['SchedulingInstance']['startDate']['month'].'-'.$instanceData['SchedulingInstance']['startDate']['day'].' ';
		$startDate .= $instanceData['SchedulingInstance']['startDate']['hour'].':'.$instanceData['SchedulingInstance']['startDate']['min'].$instanceData['SchedulingInstance']['startDate']['meridian'];

		for ($i = 0; $i < $iterations; $i++) {
			$endDate = strtotime($startDate. ' + ' . $masterData['SchedulingMaster']['numDaysToRun'] . ' days');
			$instanceData['SchedulingInstance']['endDate'] = date('Y-m-d H:i:s', $endDate);		
			
			echo $instanceData['SchedulingInstance']['endDate']."<br />";
			//$this->SchedulingMaster->SchedulingInstance->create();
			//$this->SchedulingMaster->SchedulingInstance->save($instanceData);

			$startDate = strtotime($instanceData['SchedulingInstance']['endDate'] . ' + ' . $schedulingDelayCtrlDesc);
			$instanceData['SchedulingInstance']['startDate'] = date('Y-m-d H:i:s', $startDate);	
			$startDate = $instanceData['SchedulingInstance']['startDate'];
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid SchedulingMaster', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->SchedulingMaster->save($this->data)) {
				$this->Session->setFlash(__('The SchedulingMaster has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SchedulingMaster could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->SchedulingMaster->read(null, $id);
		}
		$merchandisingFlags = $this->SchedulingMaster->MerchandisingFlag->find('list');
		$schedulingStatusIds = $this->SchedulingMaster->SchedulingStatus->find('list');
		$schedulingDelayCtrlIds = $this->SchedulingMaster->SchedulingDelayCtrl->find('list');
		$remittanceTypeIds = $this->SchedulingMaster->RemittanceType->find('list');
		
		$this->set('merchandisingFlags', $merchandisingFlags);
		$this->set('schedulingStatusIds', $schedulingStatusIds);
		$this->set('schedulingDelayCtrlIds', $schedulingDelayCtrlIds);
		$this->set('remittanceTypeIds', $remittanceTypeIds);
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SchedulingMaster', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->SchedulingMaster->del($id)) {
			$this->Session->setFlash(__('SchedulingMaster deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>