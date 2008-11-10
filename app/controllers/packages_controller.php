<?php
class PackagesController extends AppController {

	var $name = 'Packages';
	var $helpers = array('Html', 'Form');
	var $uses = array('Package', 'Client', 'PackageRatePeriod', 'PackageRatePeriodItemRel');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
	}

	function index($clientId = null) {
		if (!isset($clientId)) {
			$this->cakeError('error404');
		}
		$this->Package->recursive = 0;
		$this->set('packages', $this->paginate('ClientLoaPackageRel', array('ClientLoaPackageRel.clientId' => $clientId)));
		
		$this->set('client', $this->Client->findByClientId($clientId));
		$this->set('clientId', $clientId);
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
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
	
	function carveRatePeriods($clientId = null, $id = null) {
		// set recursive to 2 so we can access all the package loa item relations also
		$this->Package->recursive = 2;
		
		$packageData = $this->Package->read(null, $id);

		// the first record has the ids we need to handle all the rate periods and rate period item relations
		if(!isset($packageData['PackageLoaItemRel'][0])) {
			return;
		}
		$ratePeriodItemTemp = $packageData['PackageLoaItemRel'][0]['PackageRatePeriodItemRel'];

		// let's delete ALL the package loa item relations and and package rate periods related to THIS package id
		foreach ($ratePeriodItemTemp as $rel) {
			$this->Package->PackageLoaItemRel->PackageRatePeriodItemRel->deleteAll(array('PackageRatePeriodItemRel.packageRatePeriodId' => $rel['packageRatePeriodId']));
			$this->Package->PackageLoaItemRel->PackageRatePeriodItemRel->PackageRatePeriod->deleteAll(array('PackageRatePeriod.packageRatePeriodId' => $rel['packageRatePeriodId']));
		}
		
		// retrieve all loa items related to this package id
		$data = $this->Package->PackageLoaItemRel->LoaItem->find('all', array('conditions' => array('PackageLoaItemRel.packageId' => $id)));

		// populate with loa items and their rate periods
		$itemRatePeriods = array(); 
		$packageLoaItemRel = array();
		
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
			/*
			$insertSql = "INSERT INTO packageRatePeriod SET packageRatePeriodName = 'PACKAGE RATE PERIOD', ";
			$insertSql.= "startDate = '" . date('Y-m-d', $rangeStart) . "', endDate = '" . date('Y-m-d', $rangeEnd) . "', ";
			$insertSql.= "approvedRetailPrice = " . array_sum($packageLoaItemRelId) . ", approved = 0, approvedBy = 'AUTO';";
			*/
			$packageRatePeriod = array('PackageRatePeriod' => array('packageRatePeriodName' => 'PACKAGE RATE PERIOD',
																	'startDate' => date('Y-m-d', $rangeStart),
																	'endDate' => date('Y-m-d', $rangeEnd),
																	'approvedRetailPrice' => array_sum($packageLoaItemRelId),
																	'approved' => 0,
																	'approvedBy' => 'AUTO'));
			// now we can populate the packageRatePeriodItemRel because we have a package rate period
			$this->PackageRatePeriod->create();
			$this->PackageRatePeriod->set($packageRatePeriod);
			if ($this->PackageRatePeriod->save()) {
				$packageRatePeriodId = $this->PackageRatePeriod->id;

				foreach ($packageLoaItemRelId as $id => $price) {
					$packageRatePeriodItemRel = array(
										'PackageRatePeriodItemRel' => array(
																'packageRatePeriodId' => $packageRatePeriodId,
																'packageLoaItemRelId' => $id,
																'ratePeriodPrice' => $price
																	)
														);
					$this->PackageRatePeriodItemRel->create();
					$this->PackageRatePeriodItemRel->save($packageRatePeriodItemRel);
				}	
			}
		}
	}
	
	function carveRatePeriodsForDisplay() {
		$this->autoRender = false;
		
		// set recursive to 2 so we can access all the package loa item relations also		
		$this->Package->recursive = 2;

		foreach ($this->data['PackageLoaItemRel'] as $k => $v) {
			if (in_array($k, $this->data['Package']['CheckedLoaItems'])) {
				$loas[$k] = $v;
				$loaIds[] = $k;
			}
		}

		// retrieve all loa items related to this package id
		$data = $this->Package->PackageLoaItemRel->LoaItem->find('all', array('conditions' => array('LoaItem.loaItemId' => $loaIds)));
		$currencyCodes = $this->Package->Currency->find('list', array('fields' => 'currencyCode'));
		foreach ($data as $k => $v) {
			if (isset($loas[$v['LoaItem']['loaItemId']])) {
				$loas[$v['LoaItem']['loaItemId']]['itemName'] = $v['LoaItem']['itemName'];
				$loas[$v['LoaItem']['loaItemId']]['currencyCode'] = $currencyCodes[$v['LoaItem']['currencyId']];
				$currencyCode = $currencyCodes[$v['LoaItem']['currencyId']];
			}
		}
		$this->set('currencyCode', $currencyCode);
		// populate with loa items and their rate periods
		$itemRatePeriods = array(); 
		
		/*  ======= CARVING ==============
		 *  Include the package start and end dates
		 *  Include all unique loa item rate periods
		 *  ==============================
		 */
		 
		$packageDates = array();
		$packageDates[] = $packageStartDate = $this->data['Package']['startDate']['year'] . '-' . $this->data['Package']['startDate']['month'] . '-' . $this->data['Package']['startDate']['day'];
		$packageDates[] = $packageEndDate = $this->data['Package']['endDate']['year'] . '-' . $this->data['Package']['endDate']['month'] . '-' . $this->data['Package']['endDate']['day'];
								
		// go through every loa item rate period
		$i = 0;
		foreach ($data as $k => $v) {
			foreach ($v['LoaItemRatePeriod'] as $a => $b) {
				if (($b['startDate'] >= $packageStartDate) && ($b['startDate'] <= $packageEndDate) && !in_array($b['startDate'], $packageDates)) {
					$packageDates[] = $b['startDate'];
				}
				if (($b['endDate'] >= $packageStartDate) && ($b['endDate'] <= $packageEndDate) && !in_array($b['endDate'], $packageDates)) { 
					$packageDates[] = $b['endDate'];
				}
				// add the package loa item relation id so we can use as lookup later below
				$i++;
				$b['packageLoaItemRelId'] = $i;
				
				// add the item base price 
				$b['itemBasePrice'] = $v['LoaItem']['itemBasePrice'];
				
				// finally add essential data we need to the overall loa item array
				$itemRatePeriods[] = $b;
			}
		}
		
		// we now have package rate period dates so sort the dates
		sort($packageDates);
		
		$one_day = 24 * 60 * 60;
		$count = count($packageDates) - 1;

		// cycle through each item rate period using $packageDates start-end pairs.
		// for each start-end pair, use $packageLoaItemRelId to obtain item prices and ids.

		$packageRatePeriods = array();
		$pCount = 0;
		
		for ($i=0; $i < $count; $i++) {
		
			$rangeStart = strtotime($packageDates[$i]);
			$rangeEnd = strtotime($packageDates[($i + 1)]);
			
			foreach ($itemRatePeriods as $v) {
				// if the item rate period falls within the start-end pair, then this rate period has a valid item and use the approved retail price, other use base price
				$ratePeriodItemPrice = (($rangeStart >= strtotime($v['startDate'])) && ($rangeEnd <= strtotime($v['endDate']))) ? $v['approvedRetailPrice'] : $v['itemBasePrice'];	
				
				$packageLoaItemRelId[$v['loaItemId']] = $loas[$v['loaItemId']]['Periods'][$pCount] = $ratePeriodItemPrice * $loas[$v['loaItemId']]['quantity'];

				if($ratePeriodItemPrice != $v['itemBasePrice']) {
					break;
				}
			}
										
			$packageRatePeriods[$pCount]['startDate'] = $packageDates[$i];
			$packageRatePeriods[$pCount]['endDate'] = $packageDates[($i + 1)];
			$packageRatePeriods[$pCount]['packageRatePeriodPrice'] = array_sum($packageLoaItemRelId);
				
			$pCount++;
		}

		$this->set('loas', $loas);
		$this->set('packageRatePeriods', $packageRatePeriods);

		$this->render(null,null,'package_rate_periods_display');
	}
	
	function add($clientId = null) {
		$this->set('clientId', $clientId);
		$this->set('currentTab', 'property');

		if (!empty($this->data) && isset($this->data['Package']['complete'])) {
			$this->Package->create();
			
			$this->addPackageLoaItems();
			
			//the first saveAll saves packages and all associated date
			//the second save saves the HABTM format relationships

			if ($this->Package->saveAll($this->data) && $this->Package->save($this->data)) {
				$packageId = $this->Package->getLastInsertID();
				$this->addPackageOfferTypeDefFieldRel($packageId);
				$this->carveRatePeriods($clientId, $packageId);
				$this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
				$this->redirect("/clients/$clientId/packages");
			} else {
				$this->Session->setFlash(__('The Package could not be saved. Please correct the errors below and try again.', true), 'default', array(), 'error');
			}
		}
		
		
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
			return;
		}
		
		$percentSum = 0;
		$loaIds = array(); //need to reset the array declared before this if/else
			
		$this->data['ClientLoaPackageRel'] = array_merge($this->data['ClientLoaPackageRel'], array());
		
		if(count($this->data['ClientLoaPackageRel']) == 1) {
			$this->data['ClientLoaPackageRel'][0]['percentOfRevenue'] = '100';
		}
			
		foreach($this->data['ClientLoaPackageRel'] as $clientLoaPackageRel):
			$percentSum += $clientLoaPackageRel['percentOfRevenue'];
		endforeach;
		
		//if the percentages don't add up to 100%, re-display the first form
		if (100 != $percentSum):
			$this->Session->setFlash("Total percent of revenue ({$percentSum}%) must add up to 100%");
			$this->set('clients', $clients);
			$this->set(compact('loaIds'));
			$this->render('add_step_1');
		endif;

		//this re-numbers the array so we have a continuous array, since people can add/remove items on the list
		$this->data['ClientLoaPackageRel'] = array_merge($this->data['ClientLoaPackageRel'], array());
		foreach($this->data['ClientLoaPackageRel'] as $key => $clientLoaPackageRel):
			$loa = $this->Client->Loa->findByClientId($clientLoaPackageRel['clientId']);
			
			//remove all of the LOA Items that are not the same currency as the LOA
			foreach($loa['LoaItem'] as $k => $loaItem) {
				if($loaItem['currencyId'] != $loa['Currency']['currencyId']) {
					unset($loa['LoaItem'][$k]);
				}
			}
			$clientLoaDetails[$key] = $loa;
			$clientLoaDetails[$key]['ClientLoaPackageRel'] = $clientLoaPackageRel;
		endforeach;
			
		$this->set('clientLoaDetails', $clientLoaDetails);
		$this->data['Currency'] = $clientLoaDetails[0]['Currency'];
		$this->data['Package']['currencyId'] = $clientLoaDetails[0]['Currency']['currencyId'];
		$this->set('currencyCodes', $this->Package->Currency->find('list', array('fields' => array('currencyCode'))));
	}
	
	/**
	 * Function is called from {@link add()} to link LOA Items to the package
	 *
	 * @see updatePackageLoaItems()
	 * @todo consolidate with {@link updatePackageLoaItems}
	 */
	function addPackageLoaItems() {
		if(!isset($this->data['PackageLoaItemRel'])) {
			return;
		}
		$origPackageLoaItemRel = $this->data['PackageLoaItemRel'];
		unset($this->data['PackageLoaItemRel']);
		
		if (isset($this->data['Package']['CheckedLoaItems'])):
		foreach($this->data['Package']['CheckedLoaItems'] as $k=>$checkedLoaItem) {
			$this->data['PackageLoaItemRel'][$checkedLoaItem]['quantity'] = $origPackageLoaItemRel[$checkedLoaItem]['quantity'];
			$this->data['PackageLoaItemRel'][$checkedLoaItem]['loaItemId'] = $checkedLoaItem;
		}
		endif;
	}
	
	/**
	 * This method is called from the view for {@link selectAdditionalClient()} after a client is selected
	 * It uses the clientId passed to retrieve all of the fields needed to add a new client to step 1
	 *
	 * @param int $clientId
	 * @author Victor Garcia
	 */
	function fetchMultipleClientsFormFragment($clientId = null) {
		$this->set('rowId', $this->params['named']['rowId']);
		$this->set('clientId', $clientId);
		
		$client = $this->Client->findByClientId($clientId);
		$this->set('client', $client);
		
		$loaIds = $this->Client->Loa->find('list', array('conditions' => array('Loa.clientId' => $clientId)));
		$this->set(compact('loaIds'));
		
		$this->render('_add_step_1_fields');
	}
	
	/**
	 * Method displays a modal dialog box with all the clients. Used in conjunction with {@link fetchMultipleClientsFormFragment()}
	 *
	 * @author Victor Garcia
	 */
	function selectAdditionalClient() {
		$this->set('clients', $this->paginate('Client'));
	}
	
	/**
	 * Method works just like {@link addPackageLoaItems()} but for existing packages. It is called from {@link edit()}
	 * It goes through all checked items and updates quantities or removes them from the relationship as needed
	 *
	 * @see addPackageLoaItems()
	 * @todo consolidate with {@link addPackageLoaItems}
	 */
	function updatePackageLoaItems() {
		//grab the new quantities from the form, the data array looks like the one from the databases but with only the quantity field
		$currentItemIds = array();
		$newPackageLoaItemRel = $this->data['PackageLoaItemRel'];
		
		unset($this->data['PackageLoaItemRel']);
	
		//set the PackageLoaItemRel array to the arrays stored in this package
		$this->Package->PackageLoaItemRel->recursive = -1;
		$packageLoaItemRelations = $this->Package->PackageLoaItemRel->find('all', array('conditions' => array('PackageLoaItemRel.packageId' => $this->data['Package']['packageId'])));
	
		//loop through all of the loa items associated to this package
		if (!isset($this->data['Package']['CheckedLoaItems'])) {
			$this->Package->PackageLoaItemRel->deleteAll(array('PackageLoaItemRel.packageId' => $this->data['Package']['packageId'], true));
			return true;
		}
		
		foreach ($packageLoaItemRelations as $k => &$packageLoaItemRel):
			$packageLoaItemRel = $packageLoaItemRel['PackageLoaItemRel'];
			//delete all of the items that are no longer associated with this package
			if (!in_array($packageLoaItemRel['loaItemId'], $this->data['Package']['CheckedLoaItems'])) {
				if ($this->Package->PackageLoaItemRel->delete($packageLoaItemRel['packageLoaItemRelId'])) {
					unset($this->data['PackageLoaItemRel'][$k]);										//unset the array so when we don't re-save this 
				}
			} else {																					//if the new quantity is different from the old, update the field
				$currentItemIds[] = $packageLoaItemRel['loaItemId'];
				$packageLoaItemRel['quantity'] = $newPackageLoaItemRel[$packageLoaItemRel['loaItemId']]['quantity'];
				$this->data['PackageLoaItemRel'][] = $packageLoaItemRel;
			}
		endforeach;
		
		//here we deal with the new items
		if (isset($this->data['Package']['CheckedLoaItems'])):
		foreach($this->data['Package']['CheckedLoaItems'] as $k => $checkedLoaItem) {
			if (!in_array($checkedLoaItem, $currentItemIds)):
				$newPackageLoaItems[$k]['quantity'] = $newPackageLoaItemRel[$checkedLoaItem]['quantity'];
				$newPackageLoaItems[$k]['loaItemId'] = $checkedLoaItem;
				$newPackageLoaItems[$k]['packageId'] = $this->data['Package']['packageId'];
			endif;
		}
		endif;
			if (isset($this->data['PackageLoaItemRel']) && is_array($this->data['PackageLoaItemRel']) && isset($newPackageLoaItems)):
				$this->data['PackageLoaItemRel'] = array_merge_recursive($this->data['PackageLoaItemRel'], $newPackageLoaItems);
			elseif(isset($newPackageLoaItems)):
				$this->data['PackageLoaItemRel'] = $newPackageLoaItems;
			endif;
			
		$this->setUpPackageLoaItemRelArray();
		return true;
	}
	
	function setUpPackageLoaItemRelArray() {
		$tmp = array();
		if(isset($this->data['PackageLoaItemRel'])):
		foreach($this->data['PackageLoaItemRel'] as $v) {
			$tmp[$v['loaItemId']] = $v;
		}
		
		$this->data['PackageLoaItemRel'] = $tmp;
		
		endif;
	}
	
	function edit($clientId = null, $id = null) {
		if (!$clientId && !$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Package or Client', true));
			$this->redirect(array('controller' => 'clients', 'action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->updatePackageLoaItems() && $this->Package->saveAll($this->data) && $this->Package->save($this->data)) {
				$this->addPackageOfferTypeDefFieldRel($id);
				$this->carveRatePeriods($clientId, $id);
				$this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
				$this->redirect("/clients/$clientId/packages/edit/$id");
			} else {
				$this->Session->setFlash(__('The Package could not be saved. Please correct the errors again and try again.', true), 'default', array(), 'error');
			}
		}

		$this->Package->recursive = 2;
		$package = $this->Package->read(null, $id);
		$this->set('package', $package);
		
		if (empty($this->data)) {
			$this->data = $package;
		}
		
		$this->Package->ClientLoaPackageRel->recursive = -1;
		$clientLoaPackageRel = $this->Package->ClientLoaPackageRel->findAllByPackageId($id);
	
		foreach($this->data['ClientLoaPackageRel'] as $key => $clientLoaPackageRel):
			$clientLoaDetails[$key] = $this->Client->Loa->findByClientId($clientLoaPackageRel['clientId']);
			
			$clientLoaDetails[$key]['ClientLoaPackageRel'] = $clientLoaPackageRel;
			
			//remove all LOA Items that don't have the same currency as the package
			foreach($clientLoaDetails[$key]['LoaItem'] as $k => $v) {
				if($v['currencyId'] != $this->data['Package']['currencyId']) {
					unset($clientLoaDetails[$key]['LoaItem'][$k]);
				}
			}
		endforeach;
		
		
		$formats = $this->Package->Format->find('list');
		$this->set('formats', $formats);
		
		$packageStatusIds = $this->Package->PackageStatus->find('list');
		$this->set('packageStatusIds', ($packageStatusIds));

		$currencyIds = $this->Package->Currency->find('list');
		$this->set('currencyIds', ($currencyIds));
		
		$packageLoaItems = $this->Package->PackageLoaItemRel->findAllByPackageId($this->data['Package']['packageId']);
		
		foreach ($packageLoaItems as $k => $v):
			$this->data['Package']['CheckedLoaItems'][] = $v['PackageLoaItemRel']['loaItemId'];
		endforeach;
		
		//sort the LOA Items so that the checked ones appear at the top
		foreach($clientLoaDetails as $k => $a) {
			uasort($clientLoaDetails[$k]['LoaItem'], array($this, 'sortLoaItemsForEdit'));
		}
		
		$this->set('clientLoaDetails', $clientLoaDetails);
		
		$client = $this->Client->findByClientId($clientId);
		$this->set('client', $client);
		
		$this->set('clientId', $clientId);
		
		$this->setUpPackageLoaItemRelArray();
		
		// this query grabs all the package rate periods via the packageRatePeriodItemRel table.
		$packageRatePeriods = $this->Package->query("SELECT DISTINCT(prp.packageRatePeriodId), startDate, endDate, approvedRetailPrice FROM packageLoaItemRel AS plir INNER JOIN packageRatePeriodItemRel AS prpir ON plir.packageLoaItemRelId = prpir.packageLoaItemRelId INNER JOIN packageRatePeriod AS prp ON prpir.packageRatePeriodId = prp.packageratePeriodId WHERE plir.packageId = $id;");
		$this->set('packageRatePeriods', $packageRatePeriods);
		
		$this->set('currencyCodes', $this->Package->Currency->find('list', array('fields' => array('currencyCode'))));
	}
	
	function sortLoaItemsForEdit($a, $b) {
		if(!isset($this->data['Package']['CheckedLoaItems'])) {
			return;
		}
		return in_array($b['loaItemId'], $this->data['Package']['CheckedLoaItems']);
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
		$this->Package->PackageOfferTypeDefFieldRel->deleteAll(array('PackageOfferTypeDefFieldRel.packageId' => $packageId), true);

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
				$this->Session->setFlash(__('The Package could not be saved. Please correct the errors below and try again.', true));
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
		
		if($row == 'all') {
			unset($this->data['PackageValidityPeriod']);
		} else {
			if(isset($this->data['PackageValidityPeriod'][$row]['packageValidityPeriodId'])) {
				$this->Package->PackageValidityPeriod->delete($this->data['PackageValidityPeriod'][$row]);
			}
			unset($this->data['PackageValidityPeriod'][$row]);
		}
		
		$this->data['PackageValidityPeriod'] = array_merge($this->data['PackageValidityPeriod'], array());
		
		$this->render('_step_3_blackout_periods');
	}

}
?>