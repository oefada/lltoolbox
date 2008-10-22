<?php
class PackagesController extends AppController {

	var $name = 'Packages';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Package->recursive = 0;
		$this->set('packages', $this->paginate());
	}

	function view($id = null) {
		$this->Package->recursive = 2;
		if (!$id) {
			$this->Session->setFlash(__('Invalid Package.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('package', $this->Package->read(null, $id));

		$packageRatePeriods = $this->Package->query("SELECT DISTINCT(prp.packageRatePeriodId), startDate, endDate, approvedRetailPrice FROM packageLoaItemRel AS plir INNER JOIN packageRatePeriodItemRel AS prpir ON plir.packageLoaItemRelId = prpir.packageLoaItemRelId INNER JOIN packageRatePeriod AS prp ON prpir.packageRatePeriodId = prp.packageratePeriodId WHERE plir.packageId = $id;");
		$this->set('packageRatePeriods', $packageRatePeriods);			
	}
	
	function carveRatePeriods() {
		$this->Package->recursive = 2;
		
		$packageData = $this->Package->read(null);
		$ratePeriodItemTemp = $packageData['PackageLoaItemRel'][0]['PackageRatePeriodItemRel'];

		foreach ($ratePeriodItemTemp as $rel) {
			$this->Package->PackageLoaItemRel->PackageRatePeriodItemRel->deleteAll(array('PackageRatePeriodItemRel.packageRatePeriodId' => $rel['packageRatePeriodId']));
			$this->Package->PackageLoaItemRel->PackageRatePeriodItemRel->PackageRatePeriod->deleteAll(array('PackageRatePeriod.packageRatePeriodId' => $rel['packageRatePeriodId']));
		}
	
		$data = $this->Package->PackageLoaItemRel->LoaItem->find('all', array('conditions' => array('PackageLoaItemRel.packageId' => $packageData['Package']['packageId'])));

		$itemRatePeriods = array(); // populate with loaitems and their rate periods
		$packageLoaItemRel = array();
		$packageData = $this->Package->read(null);
		
		// populate $packageDates array with carved dates (YYYY-MM-DD format)
		$packageDates = array();
		$packageDates[] = $packageStartDate = substr($packageData['Package']['startDate'], 0, 10);
		$packageDates[] = $packageEndDate = substr($packageData['Package']['endDate'], 0, 10);
								
		// going through every loa item rate period, populate $packageDates array with overall carved dates
		foreach ($data as $k => $v) {
			foreach ($v['LoaItemRatePeriod'] as $a => $b) {
				if (($b['startDate'] >= $packageStartDate) && ($b['startDate'] <= $packageEndDate) && !in_array($b['startDate'], $packageDates)) {
					$packageDates[] = $b['startDate'];
				}
				if (($b['endDate'] >= $packageStartDate) && ($b['endDate'] <= $packageEndDate) && !in_array($b['endDate'], $packageDates)) { 
					$packageDates[] = $b['endDate'];
				}
				$b['packageLoaItemRelId'] = $v['PackageLoaItemRel']['packageLoaItemRelId'];
				$b['itemBasePrice'] = $v['LoaItem']['itemBasePrice'];
				$itemRatePeriods[] = $b;
			}
			$packageLoaItemRel[$v['PackageLoaItemRel']['packageLoaItemRelId']] = $v['PackageLoaItemRel'];
		}
		
		// we now have package rate period dates so sort the dates
		sort($packageDates);
		
		$one_day = 24 * 60 * 60;
		$count = count($packageDates) - 1;
		
		// cycle through each item rate period using $packageDates start-end pairs.
		// for each start-end pair, use $packageLoaItemRelId to obtain item prices and ids.
		for ($i=0; $i < $count; $i++) {
			$packageLoaItemRelId = array();
			$rangeStart = strtotime($packageDates[$i]);
			$rangeEnd = strtotime($packageDates[($i + 1)]) - $one_day;
			
			foreach ($itemRatePeriods as $v) {
				$ratePeriodItemPrice = (($rangeStart >= strtotime($v['startDate'])) && ($rangeEnd <= strtotime($v['endDate']))) ? $v['approvedRetailPrice'] : $v['itemBasePrice'];				
				if ($packageLoaItemRel[$v['packageLoaItemRelId']]['priceOverride']) {
					$ratePeriodItemPrice = $packageLoaItemRel[$v['packageLoaItemRelId']]['priceOverride'];
				}
				if ($packageLoaItemRel[$v['packageLoaItemRelId']]['noCharge']) {
					$ratePeriodItemPrice = 0;	
				}
				$packageLoaItemRelId[$v['packageLoaItemRelId']] = $ratePeriodItemPrice * $packageLoaItemRel[$v['packageLoaItemRelId']]['quantity'];
			}

			$insertSql = "INSERT INTO packageRatePeriod SET packageRatePeriodName = 'PACKAGE RATE PERIOD', ";
			$insertSql.= "startDate = '" . date('Y-m-d', $rangeStart) . "', endDate = '" . date('Y-m-d', $rangeEnd) . "', ";
			$insertSql.= "approvedRetailPrice = " . array_sum($packageLoaItemRelId) . ", approved = 0, approvedBy = 'AUTO'";
			
			if (mysql_query($insertSql)) {
				$packageRatePeriodId = 	mysql_insert_id();
				foreach ($packageLoaItemRelId as $id => $price) {
					mysql_query("INSERT INTO packageRatePeriodItemRel SET packageRatePeriodId = $packageRatePeriodId, packageLoaItemRelId = $id, ratePeriodPrice = $price");
				}	
			}
		}
		$this->Session->setFlash(__('The Package Rate Periods have been recalculated', true));
		$this->redirect(array('controller' => 'Packages', 'action'=>'view', 'id' => $packageData['Package']['packageId']));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Package->create();
			if ($this->Package->save($this->data)) {
				$this->Session->setFlash(__('The Package has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Package could not be saved. Please, try again.', true));
			}
		}
		
		$formats = $this->Package->Format->find('list');
		$this->set('formats', $formats);
		
		$packageStatusIds = $this->Package->PackageStatus->find('list');
		$this->set('packageStatusIds', ($packageStatusIds));
		
		$currencyIds = $this->Package->Currency->find('list');
		$this->set('currencyIds', ($currencyIds));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Package', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Package->save($this->data)) {
				$this->addPackageOfferTypeDefFieldRel();
				$this->Session->setFlash(__('The Package has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Package could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Package->read(null, $id);
		}
						
		$formats = $this->Package->Format->find('list');
		$this->set('formats', $formats);
		
		$packageStatusIds = $this->Package->PackageStatus->find('list');
		$this->set('packageStatusIds', ($packageStatusIds));
		
		$currencyIds = $this->Package->Currency->find('list');
		$this->set('currencyIds', ($currencyIds));
	}

	function addPackageOfferTypeDefFieldRel() {
		$packageData = $this->Package->read(null);
		$formatIds = $this->data['Format']['Format'];
		$offerTypes = $this->Package->Format->OfferType->find('all');
		
		$relData = array();
		$relData['PackageOfferTypeDefFieldRel']['packageId'] = $this->data['Package']['packageId'];

		$this->Package->PackageOfferTypeDefFieldRel->deleteAll(array('packageId' => $this->data['Package']['packageId']));

		foreach ($offerTypes as $type) {
			$relData['PackageOfferTypeDefFieldRel']['offerTypeId'] = $type['OfferType']['offerTypeId'];
			if (in_array($type['Format'][0]['formatId'], $formatIds)) {
				foreach ($type['OfferTypeDefField'] as $typeDefField) {
					$relData['PackageOfferTypeDefFieldRel']['offerTypeDefFieldId'] = $typeDefField['offerTypeDefFieldId'];
					$relData['PackageOfferTypeDefFieldRel']['defValue'] = 0.00;
					$this->Package->PackageOfferTypeDefFieldRel->create();
					$this->Package->PackageOfferTypeDefFieldRel->save($relData);
				}
			} 
		}
	}
	
	function editFormats($id = null) {
		$this->Package->recursive = 2;
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Package', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			$updateArray = array();
			$tmpData = $this->Package->read(null, $id);
			foreach ($tmpData['PackageOfferTypeDefFieldRel'] as $k => $defFields) {
				$tmpData['PackageOfferTypeDefFieldRel'][$k]['defValue'] = $this->data['PackageOfferTypeDefFieldRel']['defValue'][$defFields['packageOfferTypeDefFieldRelId']];
				unset($tmpData['PackageOfferTypeDefFieldRel'][$k]['OfferTypeDefField']);
				$updateArray[] = $tmpData['PackageOfferTypeDefFieldRel'][$k];
			}

			if ($this->Package->PackageOfferTypeDefFieldRel->saveAll($updateArray)) {
				$this->Session->setFlash(__('The Package has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Package could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Package->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Package', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Package->del($id)) {
			$this->Session->setFlash(__('Package deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>