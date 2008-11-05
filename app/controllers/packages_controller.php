<?php
class PackagesController extends AppController {

	var $name = 'Packages';
	var $helpers = array('Html', 'Form');
	var $uses = array('Package', 'Client');

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

		// this query grabs all the package rate periods via the packageRatePeriodItemRel table.
		$packageRatePeriods = $this->Package->query("SELECT DISTINCT(prp.packageRatePeriodId), startDate, endDate, approvedRetailPrice FROM packageLoaItemRel AS plir INNER JOIN packageRatePeriodItemRel AS prpir ON plir.packageLoaItemRelId = prpir.packageLoaItemRelId INNER JOIN packageRatePeriod AS prp ON prpir.packageRatePeriodId = prp.packageratePeriodId WHERE plir.packageId = $id;");
		$this->set('packageRatePeriods', $packageRatePeriods);			
	}
	
	function carveRatePeriods() {
		// set recursive to 2 so we can access all the package loa item relations also
		$this->Package->recursive = 2;
		
		$packageData = $this->Package->read(null);
		
		// the first record has the ids we need to handle all the rate periods and rate period item relations
		$ratePeriodItemTemp = $packageData['PackageLoaItemRel'][0]['PackageRatePeriodItemRel'];

		// let's delete ALL the package loa item relations and and package rate periods related to THIS package id
		foreach ($ratePeriodItemTemp as $rel) {
			$this->Package->PackageLoaItemRel->PackageRatePeriodItemRel->deleteAll(array('PackageRatePeriodItemRel.packageRatePeriodId' => $rel['packageRatePeriodId']));
			$this->Package->PackageLoaItemRel->PackageRatePeriodItemRel->PackageRatePeriod->deleteAll(array('PackageRatePeriod.packageRatePeriodId' => $rel['packageRatePeriodId']));
		}
	
		// retrieve all loa items related to this package id
		$data = $this->Package->PackageLoaItemRel->LoaItem->find('all', array('conditions' => array('PackageLoaItemRel.packageId' => $packageData['Package']['packageId'])));

		// populate with loa items and their rate periods
		$itemRatePeriods = array(); 
		$packageLoaItemRel = array();
		$packageData = $this->Package->read(null);
		
		/*  ======= CARVING ==============
		 *  Include the package start and end dates
		 *  Include all unique loa item rate periods
		 *  ==============================
		 */
		 
		$packageDates = array();
		$packageDates[] = $packageStartDate = substr($packageData['Package']['startDate'], 0, 10);
		$packageDates[] = $packageEndDate = substr($packageData['Package']['endDate'], 0, 10);
								
		// go through every loa item rate period
		foreach ($data as $k => $v) {
			foreach ($v['LoaItemRatePeriod'] as $a => $b) {
				if (($b['startDate'] >= $packageStartDate) && ($b['startDate'] <= $packageEndDate) && !in_array($b['startDate'], $packageDates)) {
					$packageDates[] = $b['startDate'];
				}
				if (($b['endDate'] >= $packageStartDate) && ($b['endDate'] <= $packageEndDate) && !in_array($b['endDate'], $packageDates)) { 
					$packageDates[] = $b['endDate'];
				}
				// add the package loa item relation id so we can use as lookup later below
				$b['packageLoaItemRelId'] = $v['PackageLoaItemRel']['packageLoaItemRelId'];
				
				// add the item base price 
				$b['itemBasePrice'] = $v['LoaItem']['itemBasePrice'];
				
				// finally add essential data we need to the overall loa item array
				$itemRatePeriods[] = $b;
			}
			// load this array with overall package to item relation
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
				// if the item rate period falls within the start-end pair, then this rate period has a valid item and use the approved retail price, other use base price
				$ratePeriodItemPrice = (($rangeStart >= strtotime($v['startDate'])) && ($rangeEnd <= strtotime($v['endDate']))) ? $v['approvedRetailPrice'] : $v['itemBasePrice'];				
				
				// if there is an price override, use it.  if there is a no charge, then the price is zeroed out
				if ($packageLoaItemRel[$v['packageLoaItemRelId']]['priceOverride']) {
					$ratePeriodItemPrice = $packageLoaItemRel[$v['packageLoaItemRelId']]['priceOverride'];
				}
				if ($packageLoaItemRel[$v['packageLoaItemRelId']]['noCharge']) {
					$ratePeriodItemPrice = 0;	
				}
				$packageLoaItemRelId[$v['packageLoaItemRelId']] = $ratePeriodItemPrice * $packageLoaItemRel[$v['packageLoaItemRelId']]['quantity'];
			}

			// create package rate period for this start-end pair
			$insertSql = "INSERT INTO packageRatePeriod SET packageRatePeriodName = 'PACKAGE RATE PERIOD', ";
			$insertSql.= "startDate = '" . date('Y-m-d', $rangeStart) . "', endDate = '" . date('Y-m-d', $rangeEnd) . "', ";
			$insertSql.= "approvedRetailPrice = " . array_sum($packageLoaItemRelId) . ", approved = 0, approvedBy = 'AUTO'";
			
			// now we can populate the packageRatePeriodItemRel because we have a package rate period
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

	function add($clientId = null) {
		$this->set('clientId', $clientId);
		$this->set('currentTab', 'property');

		if (!empty($this->data) && isset($this->data['Package']['complete'])) {
			$this->Package->create();
			//the first saveAll saves packages and all associated date
			//the second save saves the HABTM format relationships
			if ($this->Package->saveAll($this->data) && $this->Package->save($this->data)) {
				$packageId = $this->Package->getLastInsertID();
				$this->addPackageOfferTypeDefFieldRel($packageId);
				$this->Session->setFlash(__('The Package has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Package could not be saved. Please, try again.', true));
			}
		}
		
		if(!empty($this->data)):
		//this re-numbers the array so we have a continuous array, since people can add/remove items on the list
			$this->data['ClientLoaPackageRel'] = array_merge($this->data['ClientLoaPackageRel'], array());
		
			if(count($this->data['ClientLoaPackageRel']) == 1) {
				$this->data['ClientLoaPackageRel'][0]['percentOfRevenue'] = '100';
			}
			
			$this->data['ClientLoaPackageRel'] = array_merge($this->data['ClientLoaPackageRel'], array());
			foreach($this->data['ClientLoaPackageRel'] as $key => $clientLoaPackageRel):
				$clientLoaDetails[$key] = $this->Client->Loa->findByClientId($clientLoaPackageRel['clientId']);
				$clientLoaDetails[$key]['ClientLoaPackageRel'] = $clientLoaPackageRel;
			endforeach;
			
			$this->set('clientLoaDetails', $clientLoaDetails);
		endif;
		$client = $this->Client->findByClientId($clientId);
		$this->set('client', $client);
		
		$formats = $this->Package->Format->find('list');
		$this->set('formats', $formats);
		
		$packageStatusIds = $this->Package->PackageStatus->find('list');
		$this->set('packageStatusIds', ($packageStatusIds));
		
		$currencyIds = $this->Package->Currency->find('list');
		$this->set('currencyIds', ($currencyIds));
		
		if(empty($this->data)) {
			$clients[0] = $this->Client->findByClientId($clientId);
			$loaIds = $this->Package->ClientLoaPackageRel->Loa->find('list', array('conditions' => array('Loa.clientId' => $clientId)));
			$loaIds = array($loaIds);
			$this->set('clients', $clients);
			$this->set('loaIds', $loaIds);
			$this->render('add_step_1');
		} else {
			$percentSum = 0;
			$loaIds = array(); //need to reset the array declared before this if/else
			
			foreach($this->data['ClientLoaPackageRel'] as $clientLoaPackageRel):
				$clients[] = $this->Client->findByClientId($clientLoaPackageRel['clientId']);
				$loaIds[] = $this->Client->Loa->find('list', array('conditions' => array('Loa.clientId' => $clientLoaPackageRel['clientId'])));
				$percentSum += $clientLoaPackageRel['percentOfRevenue'];
			endforeach;
			
			//if the percentages don't add up to 100%, re-display the first form
			if (100 != $percentSum):
				$this->Session->setFlash("Total percent of revenue ({$percentSum}%) must add up to 100%");
				$this->set('clients', $clients);
				$this->set(compact('loaIds'));
				$this->render('add_step_1');
			endif;
		}
	}
	
	function fetchMultipleClientsFormFragment($clientId = null) {
		$this->set('rowId', $this->params['named']['rowId']);
		$this->set('clientId', $clientId);
		
		$client = $this->Client->findByClientId($clientId);
		$this->set('client', $client);
		
		$loaIds = $this->Client->Loa->find('list', array('conditions' => array('Loa.clientId' => $clientId)));
		$this->set(compact('loaIds'));
		
		$this->render('_add_step_1_fields');
	}
	
	function selectAdditionalClient() {
		$this->params['form']['rowId'] = $this->params['named']['rowId'];
		$this->set('rowId', $this->params['named']['rowId']);
		$this->set('clients', $this->paginate('Client'));
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

	function addPackageOfferTypeDefFieldRel($packageId = null) {
		// this function setups up new offer type default values
		$packageData = $this->Package->read(null);
		$formatIds = $this->data['Format']['Format'];
		if(empty($formatIds)) {
			return;
		}
		$offerTypes = $this->Package->Format->OfferType->find('all');

		$relData = array();
		$relData['PackageOfferTypeDefFieldRel']['packageId'] = ($packageId) ? $packageId : $this->data['Package']['packageId'];
		
		// delete all existing package to offer type default fields relation
		$this->Package->PackageOfferTypeDefFieldRel->deleteAll(array('packageId' => $packageId));

		// for all the offer types and associated package formats, create new default value rows w/ 0.00
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
		// this is so we can edit existing package offer type default values
		// the default offer types and values are normalized so we have to do this work around
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

			// save any changes to the package offer type default values
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
	
	function addBlackoutPeriodRow() {
		$this->autoRender = false;
		$this->data['PackageValidityPeriod'][] = array();
		
		$this->render('_step_3_blackout_periods');
	}
	
	function removeBlackoutPeriodRow($row) {
		$this->autoRender = false;
		
		unset($this->data['PackageValidityPeriod'][$row]);
		
		$this->data['PackageValidityPeriod'] = array_merge($this->data['PackageValidityPeriod'], array());
		
		$this->render('_step_3_blackout_periods');
	}

}
?>