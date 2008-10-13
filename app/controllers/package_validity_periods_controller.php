<?php
class PackageValidityPeriodsController extends AppController {

	var $name = 'PackageValidityPeriods';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->PackageValidityPeriod->recursive = 0;
		$this->set('packageValidityPeriods', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PackageValidityPeriod.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('packageValidityPeriod', $this->PackageValidityPeriod->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			if ($this->PackageValidityPeriod->save($this->data) && ($this->cleanDates())) {
				$this->Session->setFlash(__('The PackageValidityPeriod has been saved', true));
				$this->redirect(array('controller' => 'packages', 'action'=>'view', 'id' => $this->params['data']['PackageValidityPeriod']['packageId']));
			} else {
				$this->Session->setFlash(__('The PackageValidityPeriod could not be saved. Please, try again.', true));
			}
		}
		$this->data['PackageValidityPeriod']['packageId'] = $this->params['packageId'];
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PackageValidityPeriod', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->PackageValidityPeriod->save($this->data) && $this->cleanDates()) {
				$this->Session->setFlash(__('The PackageValidityPeriod has been saved', true));
				$this->redirect(array('controller' => 'packages', 'action'=>'view', 'id' => $this->params['data']['PackageValidityPeriod']['packageId']));
			} else {
				$this->Session->setFlash(__('The PackageValidityPeriod could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PackageValidityPeriod->read(null, $id);
		}
	}
	
	function cleanDates() {
		$packageId = $this->params['packageId'];
		$pvp_data = $this->PackageValidityPeriod->findAllBypackageid($packageId);
		$dates = array();
		
		foreach ($pvp_data as $k => $pvp) {
			$ts_start = strotime(substr($pvp['PackageValidityPeriod']['startDate']));
			$ts_end = strotime(substr($pvp['PackageValidityPeriod']['endDate']));
			if (isset($dates[$ts_start])) {
				
			} else {
				$dates[$ts_start] = $ts_end;	
			}
		}
		asort($dates);
		print_r($dates);
		
		return false;	
	}
	
	function addNewDateRange() {
		// ====================================================
		// get new date range that is added, edited, or deleted		
		$newStartTs = strtotime($this->data['PackageValidityPeriod']['startDate']['year'] . '-' . $this->data['PackageValidityPeriod']['startDate']['month'] . '-' . $this->data['PackageValidityPeriod']['startDate']['day']);
		$newEndTs = strtotime($this->data['PackageValidityPeriod']['endDate']['year'] . '-' . $this->data['PackageValidityPeriod']['endDate']['month'] . '-' . $this->data['PackageValidityPeriod']['endDate']['day']);
		$newValidityFlag = $this->data['PackageValidityPeriod']['validityFlag'];
		
		// ====================================================
		// get already existing package validity periods
		$packageId = $this->data['PackageValidityPeriod']['packageId'];
		$packageValidityPeriods = $this->PackageValidityPeriod->findAllBypackageid($packageId);
		
		// ====================================================
		// if there are no pvp's, then just add a new one
		if (!$packageValidityPeriods) {
			$this->PackageValidityPeriod->create();
			$this->PackageValidityPeriod->save($this->data);	
			$this->Session->setFlash(__('The Package Blackout / Validity date range has been added', true));
			return true;
		}
		
		// ====================================================
		// carve necessary periods and insert new periods
		$insertData = array();
		$insertData['packageId'] = $packageId;
		
		foreach ($packageValidityPeriods as $pvp) {
			$pvpStartTs = strtotime($pvp['PackageValidityPeriod']['startDate']);
			$pvpEndTs = strtotime($pvp['PackageValidityPeriod']['endDate']);
			
			if (($newStartTs >= $pvpStartTs) && ($newEndTs <= $pvpEndTs)) {
				$newPeriods = $this->carveDatesTs(array($pvpStartTs, $pvpEndTs), $newStartTs, $newEndTs);
				if (!$newPeriods) {
					$this->Session->setFlash(__('There was a problem and the Blackout / Validity date range NOT saved.  Please contact your local friendly programmer.', true));
					return false;	
				}
				$this->PackageValidityPeriod->delete(array('packageValidityPeriodId' => $pvp['PackageValidityPeriod']['packageValidityPeriodId']));
				
				foreach ($newPeriods as $start => $end) {	
					$insertData['startDate'] = date('Y-m-d', $start);
					$insertData['endDate'] = date('Y-m-d', $end);
					$insertData['validityFlag'] = (($start == $newStartTs) && ($end == $newEndTs)) ? $newValidityFlag : $pvp['PackageValidityPeriod']['validityFlag'];
					$this->PackageValidityPeriod->create();
					$this->PackageValidityPeriod->save($insertData);
				}
				return true;
			} elseif ($damnit) {
				
			}
		}
		return false;
	}

	function carveDatesTs($data, $newStart, $newEnd) {
		$one_day = 24 * 60 * 60;
		$data[] = $newStart;
		$data[] = $newEnd + $one_day;
		
		sort($data);
		$count = count($data) - 1;
		$carvedDates = array();
		for ($i = 0; $i < $count; $i++) {
			$start = $data[$i];
			$end = $data[($i + 1)];
			if (($i +1) < $count) {
				$end-= $one_day;
			}
			$carvedDates[$start] = $end;
		}
		
		return $carvedDates;	
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for PackageValidityPeriod', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PackageValidityPeriod->del($id)) {
			$this->Session->setFlash(__('PackageValidityPeriod deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>