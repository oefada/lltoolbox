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
			$package = $this->SchedulingMaster->Package->findByPackageId($this->data['SchedulingMaster']['packageId']);
			$this->data['SchedulingMaster']['validityStartDate'] = $package['Package']['validityStartDate'];
			$this->data['SchedulingMaster']['validityEndDate'] = $package['Package']['validityEndDate'];
			if ($this->SchedulingMaster->save($this->data)) {
				$this->createInstances();
				if ($this->RequestHandler->isAjax()) {
					$this->Session->setFlash(__('The Schedule has been saved', true), 'default', array(), 'success');
					$this->set('closeModalbox', true);
				}
			} else {
				$this->Session->setFlash(__('The Schedule could not be saved. Please correct the errors below.', true), 'default', array(), 'error');
			}
		}
		 
		if (empty($this->data) && isset($this->params['named']['date'])) {
			$date = explode('-', $this->params['named']['date']);

			$this->data['SchedulingMaster']['startDate']['year'] 		= $date[0];
			$this->data['SchedulingMaster']['startDate']['month'] 		= $date[1];
			$this->data['SchedulingMaster']['startDate']['day'] 		= $date[2];
			$this->data['SchedulingMaster']['startDate']['hour']		= date('h');
			$this->data['SchedulingMaster']['startDate']['min'] 		= date('i');
			$this->data['SchedulingMaster']['startDate']['meridian']	= date('A');
		}

		$packageId 				= $this->params['named']['packageId'];
		$package 				= $this->SchedulingMaster->Package->findByPackageId($packageId);

		foreach ($package['Format'] as $format):
			$formatIds[] = $format['formatId'];
		endforeach;
		
		if (count($formatIds) == 0) {
			echo '<h3>This package is not ready to be scheduled because no formats have been associated with it</h3>';
			die();
		}
		
		/* Get all Offer Types available for this package based on Format */
		$this->SchedulingMaster->Package->Format->Behaviors->attach('Containable');
		$formats = $this->SchedulingMaster->Package->Format->find('all', array('conditions' => array('formatId' => $formatIds), 'contain' => array('OfferType')));
		
		foreach ($formats as $format) {
			foreach ($format['OfferType'] as $k => $v) {
				$offerTypeIds[$v['offerTypeId']] = $v['offerTypeName'];
			}
		}
		
		foreach($offerTypeIds as $k => $id) {
			$firstOfferId = $k;
			break;
		}
		$offerTypeId = (isset($this->data['SchedulingMaster']['offerTypeId'])) ? $this->data['SchedulingMaster']['offerTypeId'] : $firstOfferId;
		$this->SchedulingMaster->Package->PackageOfferTypeDefField->recursive = -1;
		$defaults = $this->SchedulingMaster->Package->PackageOfferTypeDefField->find('first', array('conditions' => array('PackageOfferTypeDefField.packageId' => $packageId, 'PackageOfferTypeDefField.offerTypeId' => $offerTypeId)));

		switch ($offerTypeId):
			case 1:
			case 2:
			case 6:
				$this->set('defaultFile', 'offer_type_defaults_1');
			break;
			case 3:
			case 4:
				$this->set('defaultFile', 'offer_type_defaults_2');
			break;
		endswitch;
		$this->set(compact('defaults'));
		
		$merchandisingFlags 					= $this->SchedulingMaster->MerchandisingFlag->find('list');
		$schedulingStatusIds 					= $this->SchedulingMaster->SchedulingStatus->find('list');
		$schedulingDelayCtrlIds 				= $this->SchedulingMaster->SchedulingDelayCtrl->find('list');
		$remittanceTypeIds 						= $this->SchedulingMaster->RemittanceType->find('list');

		$this->set('offerTypeIds', 				$offerTypeIds);
		$this->set('package', 					$package);
		$this->set('packageId', 				$packageId);
		$this->set('merchandisingFlags', 		$merchandisingFlags);
		$this->set('schedulingStatusIds', 		$schedulingStatusIds);
		$this->set('schedulingDelayCtrlIds', 	$schedulingDelayCtrlIds);
		$this->set('remittanceTypeIds', 		$remittanceTypeIds);
	}

	function createInstances() {
		$masterData = $this->SchedulingMaster->read(null);
		$iterations = $masterData['SchedulingMaster']['iterations'];
			
		$instanceData = array();
		$instanceData['SchedulingInstance']['schedulingMasterId'] 	= $masterData['SchedulingMaster']['schedulingMasterId'];
		$instanceData['SchedulingInstance']['startDate'] 			= $masterData['SchedulingMaster']['startDate'];
						
		for ($i = 0; $i < $iterations; $i++) {
			$endDate = strtotime($instanceData['SchedulingInstance']['startDate'] . ' +' . $masterData['SchedulingMaster']['numDaysToRun'] . ' days');
			$instanceData['SchedulingInstance']['endDate'] = date('Y-m-d H:i:s', $endDate);		
				
			$this->SchedulingMaster->SchedulingInstance->create();
			$this->SchedulingMaster->SchedulingInstance->save($instanceData);
		
			$startDate = strtotime($instanceData['SchedulingInstance']['endDate'] . ' +' . $masterData['SchedulingDelayCtrl']['schedulingDelayCtrlDesc']);
			$instanceData['SchedulingInstance']['startDate'] = date('Y-m-d H:i:s', $startDate);	
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid SchedulingMaster', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			$this->data['SchedulingMaster']['schedulingMasterId'] = $id;
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
		
		$packageId 				= $this->data['SchedulingMaster']['packageId'];
		$package 				= $this->SchedulingMaster->Package->findByPackageId($packageId);
		
		foreach ($package['Format'] as $format):
			$formatIds[] = $format['formatId'];
		endforeach;
		
		/* Get all Offer Types available for this package based on Format */
		$this->SchedulingMaster->Package->Format->Behaviors->attach('Containable');
		$formats = $this->SchedulingMaster->Package->Format->find('all', array('conditions' => array('formatId' => $formatIds), 'contain' => array('OfferType')));

		foreach ($formats[0]['OfferType'] as $k => $v) {
			$offerTypeIds[$v['offerTypeId']] = $v['offerTypeName'];
		}
		
		$this->set('offerTypeIds', 				$offerTypeIds);
		$this->set('merchandisingFlags', $merchandisingFlags);
		$this->set('schedulingStatusIds', $schedulingStatusIds);
		$this->set('schedulingDelayCtrlIds', $schedulingDelayCtrlIds);
		$this->set('remittanceTypeIds', $remittanceTypeIds);
		$this->render('add');
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
	
	function getOfferTypeDefaults() {
		$this->autoRender = false;
		$packageId = $this->params['named']['packageId'];
		$offerTypeId = $this->data['SchedulingMaster']['offerTypeId'];
		$this->SchedulingMaster->Package->PackageOfferTypeDefField->recursive = -1;
		$defaults = $this->SchedulingMaster->Package->PackageOfferTypeDefField->find('first', array('conditions' => array('PackageOfferTypeDefField.packageId' => $packageId, 'PackageOfferTypeDefField.offerTypeId' => $offerTypeId)));
		$this->set(compact('defaults'));

		switch ($offerTypeId):
			case 1:
			case 2:
			case 6:
				$this->render('offer_type_defaults_1');
			break;
			case 3:
			case 4:
				$this->render('offer_type_defaults_2');
			break;
		endswitch;
	}
	
	/**
	 * Method called from Prototip using ajax. Finds all of the performance metrics and passes them into a view file.
	 * View file is shown in a tooltip.
	 *
	 * @param int $id of the scheduling master to grab performance metrics for
	 */
	function performanceTooltip($id) {
		$this->SchedulingMaster->SchedulingMasterPerformance->recursive = -1;
		$metrics = $this->SchedulingMaster->SchedulingMasterPerformance->find('first', array('conditions' => array('SchedulingMasterPerformance.schedulingMasterId' => $id)));

		$this->set('metrics', $metrics['SchedulingMasterPerformance']);
	}
}
?>