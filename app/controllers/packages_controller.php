<?php
class PackagesController extends AppController {

	var $name = 'Packages';
	var $helpers = array('Html', 'Form');
	var $uses = array('Package', 'Client', 'PackageRatePeriod', 'LoaItem');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
	}

	function index($clientId = null) {
		if (!isset($clientId) && !isset($this->params['named']['clientId'])) {
			$this->cakeError('error404');
		}
		
		if (isset($this->params['named']['clientId']) && $clientId == null) {
		    $clientId = $this->params['named']['clientId'];
		} else {
		    $this->params['named']['clientId'] = $clientId;
		}
		
		$this->set('packages', $this->paginate('ClientLoaPackageRel', array('ClientLoaPackageRel.clientId' => $clientId)));
		$this->set('packageStatusIds', $this->Package->PackageStatus->find('list'));
		$this->set('client', $this->Client->findByClientId($clientId));
		$this->set('clientId', $clientId);
	}
	
	function carveRatePeriods($clientId = null, $id = null) {
		if(empty($this->data['Package']['CheckedLoaItems']))
			return;
		$this->Package->recursive = 2;
		
		$packageStartDate = $this->data['Package']['validityStartDate']['year'] . '-' . $this->data['Package']['validityStartDate']['month'] . '-' . $this->data['Package']['validityStartDate']['day'];
		$packageEndDate = $this->data['Package']['validityEndDate']['year'] . '-' . $this->data['Package']['validityEndDate']['month'] . '-' . $this->data['Package']['validityEndDate']['day'];
		
		$carvedRatePeriods = $this->Package->PackageLoaItemRel->LoaItem->carveRatePeriods($this->data['Package']['CheckedLoaItems'], $this->data['PackageLoaItemRel'], $packageStartDate, $packageEndDate);
		if (isset($carvedRatePeriods['PackageRatePeriod'])) {
		   $this->data['PackageRatePeriod'] = $carvedRatePeriods['PackageRatePeriod'];
		}
	}
	
	function getCarvedRatePeriods($clientId = null, $id = null) {
		if(empty($this->data['Package']['CheckedLoaItems']))
			return;
		$this->Package->recursive = 2;
		
		$packageStartDate = $this->data['Package']['validityStartDate']['year'] . '-' . $this->data['Package']['validityStartDate']['month'] . '-' . $this->data['Package']['validityStartDate']['day'];
		$packageEndDate = $this->data['Package']['validityEndDate']['year'] . '-' . $this->data['Package']['validityEndDate']['month'] . '-' . $this->data['Package']['validityEndDate']['day'];
		
		$carvedRatePeriods = $this->Package->PackageLoaItemRel->LoaItem->carveRatePeriods($this->data['Package']['CheckedLoaItems'], $this->data['PackageLoaItemRel'], $packageStartDate, $packageEndDate);
		return $carvedRatePeriods;
	}
	
	function carveRatePeriodsForDisplay() {
		$this->autoRender = false;
		
		// set recursive to 2 so we can access all the package loa item relations also		
		$this->Package->recursive = 2;

		// retrieve all loa items related to this package id
		$currencyCodes = $this->Package->Currency->find('list', array('fields' => 'currencyCode'));
		$this->set('currencyCodes', $currencyCodes);

		$packageStartDate = $this->data['Package']['validityStartDate']['year'] . '-' . $this->data['Package']['validityStartDate']['month'] . '-' . $this->data['Package']['validityStartDate']['day'];
		$packageEndDate = $this->data['Package']['validityEndDate']['year'] . '-' . $this->data['Package']['validityEndDate']['month'] . '-' . $this->data['Package']['validityEndDate']['day'];
		
		$carvedRatePeriods = $this->Package->PackageLoaItemRel->LoaItem->carveRatePeriods($this->data['Package']['CheckedLoaItems'], $this->data['PackageLoaItemRel'], $packageStartDate, $packageEndDate);
		$this->set('packageRatePeriods', $carvedRatePeriods);
		$this->set('packageRatePreview', true);
		$this->render(null,null,'package_rate_periods');
	}
	
	function add($clientId = null) {
		$this->set('clientId', $clientId);
		$this->set('currentTab', 'property');

		// for hotel offers
		if ($this->data['Package']['externalOfferUrl']) {
			
			$this->data['Package']['packageName'] = $this->data['Package']['packageTitle'];
			$this->data['Package']['validityStartDate'] = $this->data['Package']['startDate'];
			$this->data['Package']['validityEndDate'] = $this->data['Package']['endDate'];
			$this->data['Package']['packageStatusId'] = 4;
			if ($this->Package->saveAll($this->data, array('validate' => false)) && $this->Package->save($this->data, array('validate' => false))) {

				// create schedulingMaster
				$this->Package->SchedulingMaster->create();
				$sched_master['SchedulingMaster']['packageId'] = $this->Package->id;
				$sched_master['SchedulingMaster']['offerTypeId'] = 7;
				$sched_master['SchedulingMaster']['iterationSchedulingOption'] = 1;
				$sched_master['SchedulingMaster']['remittanceTypeId'] = 0;
				$sched_master['SchedulingMaster']['mysteryIncludes'] = '';
				$sched_master['SchedulingMaster']['startDate'] = $this->data['Package']['startDate'];
				$sched_master['SchedulingMaster']['endDate'] = $this->data['Package']['endDate'];
				$sched_master['SchedulingMaster']['siteId'] = $this->data['Package']['siteId'];
				// create schedulingInstance
				if ($this->Package->SchedulingMaster->saveAll($sched_master, array('validate' => false))) {
					$instanceData['SchedulingInstance']['schedulingMasterId'] = $this->Package->SchedulingMaster->id;
					$instanceData['SchedulingInstance']['startDate'] = $this->data['Package']['startDate'];
				    $instanceData['SchedulingInstance']['endDate'] = $this->data['Package']['endDate'];
				    $this->Package->SchedulingMaster->SchedulingInstance->create();
					$this->Package->SchedulingMaster->SchedulingInstance->save($instanceData);
				} else {
					$this->Session->setFlash(__('The Schedule could not be saved. Please correct the errors below.', true), 'default', array(), 'error');
				}			
				
				$this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
				$this->redirect("/clients/$clientId/packages/edit/{$this->Package->id}");
			} else {
				$this->Session->setFlash(__('The Package could not be saved. Please correct the errors below and try again.', true), 'default', array(), 'error');
			}			
			return;
		}
		
		if (!empty($this->data) && isset($this->data['Package']['complete'])) {
            $this->data = $this->setCorrectNumNights($this->data);
			$this->addPackageLoaItems();
			$this->getBlackoutDaysNumber();
			$this->carveRatePeriods($clientId);
			if ($this->Package->saveAll($this->data) && $this->Package->save($this->data)) {
				$this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
				$this->redirect("/clients/$clientId/packages/edit/{$this->Package->id}");
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
			$loaIds = $this->Package->ClientLoaPackageRel->Loa->find('list', array('conditions' => array('Loa.clientId' => $clientId, 'Loa.endDate > NOW()')));
			if(empty($loaIds) && !empty($clients[0]['Client']['parentClientId'])) {
			    $loaIds = $this->Package->ClientLoaPackageRel->Loa->find('list', array('conditions' => array('Loa.clientId' => $clients[0]['Client']['parentClientId'], 'Loa.endDate > NOW()')));
			}
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
		$this->Client->Loa->recursive = 2;
		foreach($this->data['ClientLoaPackageRel'] as $key => $clientLoaPackageRel):
			$loa = $this->Client->Loa->findByLoaId($clientLoaPackageRel['loaId']);
			$track = $this->Client->Loa->Track->findByTrackId($clientLoaPackageRel['trackId']);
			$clientLoaDetails[$key] = $loa;
			$clientLoaDetails[$key]['ClientLoaPackageRel'] = $clientLoaPackageRel;
			$clientLoaDetails[$key]['ClientLoaPackageRel']['Track'] = $track['Track'];
		endforeach;

		$this->set('clientLoaDetails', $clientLoaDetails);
		$this->data['Currency'] = $clientLoaDetails[0]['Currency'];
		$this->data['Package']['currencyId'] = $clientLoaDetails[0]['Currency']['currencyId'];
		$this->set('currencyCodes', $this->Package->Currency->find('list', array('fields' => array('currencyCode'))));

		$loaItemTypes = $this->Package->PackageLoaItemRel->LoaItem->LoaItemType->find('list');
		$trackExpirationCriteriaIds = $this->Package->ClientLoaPackageRel->Loa->Track->ExpirationCriterium->find('list');
		$familyAmenities = $this->Package->FamilyAmenity->find('list');
		$this->set(compact('loaItemTypes', 'trackExpirationCriteriaIds', 'familyAmenities'));
	}
	
	function getBlackoutDaysNumber($reverse = 0) {
	    if ($reverse == 0) {
	        $days = $this->data['Package']['Recurring Day Blackout'];
	        
	        if (empty($days)) {
	            unset($this->data['Package']['blackoutDays']);
	            return;
	        }
	        
	        $this->data['Package']['blackoutDays'] = implode(',', $days);

	        $blackoutDays = $this->_createBlackoutsBasedOnDays($days);

	        if (isset($this->data['PackageValidityPeriod']) && is_array($this->data['PackageValidityPeriod'])) {
	            $this->data['PackageValidityPeriod'] = array_merge($this->data['PackageValidityPeriod'], $blackoutDays);
	        } else {
	            $this->data['PackageValidityPeriod'] = $blackoutDays;
	        }
	    } else {
	        $days = $this->data['Package']['blackoutDays'];
	        $this->data['Package']['Recurring Day Blackout'] = explode(',', $days);
	    }
	}
	
	function _createBlackoutsBasedOnDays($selectedDays) {
	    $validityStartDate  = is_array($this->data['Package']['validityStartDate']) ? implode('/', $this->data['Package']['validityStartDate']): $this->data['Package']['validityStartDate'] ;
	    $validityEndDate    = is_array($this->data['Package']['validityEndDate']) ? implode('/', $this->data['Package']['validityEndDate']): $this->data['Package']['validityEndDate'] ;
	    
	    $weekDays = array(1=>'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
	    
	    $seedDate = strtotime($validityStartDate);
	    
	    $dayOfStartDate = date('N', $seedDate);
	    
	    $isWeekDayRepeat = 1;
	    if (in_array($dayOfStartDate, $selectedDays)) {
	        $startDate = date('Y-m-d', $seedDate);
	        $endDate = $startDate;
	        $blackoutDays[] = compact('startDate', 'endDate', 'isWeekDayRepeat');
	    }
	    
	    $timeStampEndDate = strtotime($validityEndDate);
	    while($seedDate <= $timeStampEndDate): 
	        foreach ($selectedDays as $day):
	            $blackoutDay = strtotime("next {$weekDays[$day]}", $seedDate);
	            
	            if ($blackoutDay < $timeStampEndDate) {
	                $startDate = date('Y-m-d', $blackoutDay);
	                $endDate = $startDate;
	                $blackoutDays[] = compact('startDate', 'endDate', 'isWeekDayRepeat');
	            }
	        endforeach;
	        
	        $seedDate = strtotime('next week', $seedDate);
	    endwhile;
	    
	    return $blackoutDays;
	}
	
	/**
	 * Function is called from {@link add()} to link LOA Items to the package
	 *
	 * @see updatePackageLoaItems()
	 * @todo consolidate with {@link updatePackageLoaItems}
	 */
	function addPackageLoaItems() {
		if(!isset($this->data['PackageLoaItemRel'])) {
			return true;
		}

		$origPackageLoaItemRel = $this->data['PackageLoaItemRel'];
		unset($this->data['PackageLoaItemRel']);
		
		if (isset($this->data['Package']['CheckedLoaItems'])):
		foreach($this->data['Package']['CheckedLoaItems'] as $k=>$checkedLoaItem) {
			$this->data['PackageLoaItemRel'][$checkedLoaItem]['quantity'] = $origPackageLoaItemRel[$checkedLoaItem]['quantity'];
			$this->data['PackageLoaItemRel'][$checkedLoaItem]['weight'] = $origPackageLoaItemRel[$checkedLoaItem]['weight'];
			$this->data['PackageLoaItemRel'][$checkedLoaItem]['loaItemTypeId'] = $origPackageLoaItemRel[$checkedLoaItem]['loaItemTypeId'];
			$this->data['PackageLoaItemRel'][$checkedLoaItem]['loaItemId'] = $checkedLoaItem;
		}
		endif;
		
		return true;
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
		$this->set('data', $this->data);
		if (isset($this->data['search'])):
			$this->set('clients', $this->paginate('Client', array('Client.name LIKE' => "%{$this->data['search']}%")));
		else:
			$this->set('clients', $this->paginate('Client'));
		endif;
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
		$newPackageLoaItemRel = @$this->data['PackageLoaItemRel'];

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
				$packageLoaItemRel['weight'] = $newPackageLoaItemRel[$packageLoaItemRel['loaItemId']]['weight'];
				$packageLoaItemRel['loaItemTypeId'] = $newPackageLoaItemRel[$packageLoaItemRel['loaItemId']]['loaItemTypeId'];
				$this->data['PackageLoaItemRel'][] = $packageLoaItemRel;
			}
		endforeach;
		
		//here we deal with the new items
		if (isset($this->data['Package']['CheckedLoaItems'])):
		foreach($this->data['Package']['CheckedLoaItems'] as $k => $checkedLoaItem) {
			if (!in_array($checkedLoaItem, $currentItemIds)):
				$newPackageLoaItems[$k]['quantity'] = $newPackageLoaItemRel[$checkedLoaItem]['quantity'];
				$newPackageLoaItems[$k]['weight'] = $newPackageLoaItemRel[$checkedLoaItem]['weight'];
				$newPackageLoaItems[$k]['loaItemTypeId'] = $newPackageLoaItemRel[$checkedLoaItem]['loaItemTypeId'];
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
			if (!empty($this->data['Package']['externalOfferUrl'])) { // for hotel offers
			
				$this->data['Package']['packageName'] = $this->data['Package']['packageTitle'];
				$this->data['Package']['validityStartDate'] = $this->data['Package']['startDate'];
				$this->data['Package']['validityEndDate'] = $this->data['Package']['endDate'];
				
				if ($this->Package->saveAll($this->data, array('validate' => false)) && $this->Package->save($this->data, array('validate' => false))) {
					$this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
					$this->redirect("/clients/$clientId/packages/edit/".$this->Package->id);
				} else {
					$this->Session->setFlash(__('The Package could not be saved. Please correct the errors below and try again.', true), 'default', array(), 'error');
				}
				return;
			}
			
			if (@$this->data['clone'] == 'clone') {
				$this->data = $this->Package->cloneData($this->data);
				$this->addPackageLoaItems();
				$cloned = true;
			} else {
				$this->Package->PackageRatePeriod->deleteAll(array('PackageRatePeriod.packageId' => $this->data['Package']['packageId']));
				$this->updatePackageLoaItems();
				$cloned = false;
			}
            
			$this->data = $this->setCorrectNumNights($this->data);
			$this->getBlackoutDaysNumber();
			$this->carveRatePeriods($clientId);
			//remove all offer type defaults so we don't get duplicates
			$this->Package->PackageOfferTypeDefField->deleteAll(array('PackageOfferTypeDefField.packageId' => $this->data['Package']['packageId']), false);
			
			//remove all recurring days so we don't get duplicates
			$this->Package->PackageValidityPeriod->deleteAll(array('PackageValidityPeriod.packageId' => $this->data['Package']['packageId'], 'isWeekDayRepeat' => 1), false);
			
			$this->Package->PackageAgeRange->deleteAll(array('PackageAgeRange.packageId' => $this->data['Package']['packageId']), false);
			
			if ($this->Package->saveAll($this->data) && $this->Package->save($this->data)) {
				if(true == $cloned) {
					$this->Session->setFlash(__('Package was cloned from package #'.$this->data['Package']['copiedFromPackageId'], true), 'default', array(), 'success');
				} else {
					$this->Session->setFlash(__('The Package has been saved', true), 'default', array(), 'success');
				}
				$this->redirect("/clients/$clientId/packages/edit/".$this->Package->id);
			} else {
				$this->Session->setFlash(__('The Package could not be saved. Please correct the errors below and try again.', true), 'default', array(), 'error');
			}
		}
		
		if (empty($this->data)) {
			$package = $this->Package->read(null, $id);
			$this->data = $package;
		} else {			
		    $package = $this->data;
		}
		
		switch($package['Package']['siteId']) {
			case 2:
			   $this->set('siteUrl', 'www.familygetaway.com');
			   break;
			case 1:
			default:
			   $this->set('siteUrl', 'www.luxurylink.com');
		}
		
		$client_trackings = array();
		
		// map clientTracking: use clientTrackingTypeId as key
		if (isset($this->data['ClientTracking'])) {
		   foreach ($this->data['ClientTracking'] as $k => $v) {
			   $client_trackings[$v['clientTrackingTypeId']] = $v;
		   }
		}
		$this->data['ClientTracking'] = $client_trackings;
	
		usort($package['PackageAgeRange'], array($this, 'sortPackageAgeRange')); //sort age ranges

		$this->set('package', $package);
		$this->getBlackoutDaysNumber(1);
		$this->Package->ClientLoaPackageRel->recursive = -1;
		$clientLoaPackageRel = $this->Package->ClientLoaPackageRel->findAllByPackageId($id);
		$this->LoaItem->recursive = 2;
		$this->LoaItem->Behaviors->attach('Containable');

		foreach($this->data['ClientLoaPackageRel'] as $key => $clientLoaPackageRel):
			$clientLoaDetails[$key] = $this->Client->Loa->findByLoaId($clientLoaPackageRel['loaId']);
			
			$clientLoaDetails[$key]['ClientLoaPackageRel'] = $clientLoaPackageRel;
			
			//Get all the fees for each item
			foreach($clientLoaDetails[$key]['LoaItem'] as $k => $v) {
			    $itemId = $v['loaItemId'];
			    $loaItem = $this->LoaItem->read(null, $itemId);
			    $clientLoaDetails[$key]['LoaItem'][$k]['Fee'] = $loaItem['Fee'];
			    $clientLoaDetails[$key]['LoaItem'][$k]['LoaItemRatePeriod'] = $loaItem['LoaItemRatePeriod'];
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
		$approvalNotNeeded = 0;
		foreach($clientLoaDetails as $k => $a):
			uasort($clientLoaDetails[$k]['LoaItem'], array($this, 'sortLoaItemsForEdit'));
			
			$track = $this->Package->ClientLoaPackageRel->Loa->Track->findByTrackId($a['ClientLoaPackageRel']['trackId']);
			
			//Check if the track is a Keep track. Internal approval only needed for keep tracks
			if (empty($track['Track']['applyToMembershipBal']) && isset($approvalNotNeeded)) {
			    $approvalNotNeeded = 1;
			} else {
			    unset($approvalNotNeeded);
			}
			
			$clientLoaDetails[$k]['ClientLoaPackageRel']['Track'] = $track['Track'];
		endforeach;
        
        if (isset($approvalNotNeeded) && $approvalNotNeeded == 1) {
            $this->data['Package']['internalApproval'] = $approvalNotNeeded;
        }
        
		$this->set('clientLoaDetails', $clientLoaDetails);
		
		$client = $this->Client->findByClientId($clientId);
		$this->set('client', $client);
		
		$this->set('clientId', $clientId);
		
		$this->setUpPackageLoaItemRelArray();
		$itemList = $this->Package->PackageLoaItemRel->LoaItem->find('list');
		//$itemCurrencyIds = $this->Package->PackageLoaItemRel->LoaItem->Loa->find('list', array('fields' => array('currencyId'), 'conditions' => array('Loa.loaId' => $this->data['ClientLoaPackageRel'][0]['loaId'])));
		
		$carvedRatePeriods = $this->getCarvedRatePeriods($clientId);

		if (isset($carvedRatePeriods['Boundaries']) && !empty($carvedRatePeriods['Boundaries'])):
		//sort and re-set keys for the boundaries array
			sort($carvedRatePeriods['Boundaries']);
			array_merge($carvedRatePeriods['Boundaries'], array());
		
			$packageRatePeriods = array();
			$packageRatePeriods['IncludedItems'] = $carvedRatePeriods['IncludedItems'];
			$packageRatePeriods['Boundaries'] = $carvedRatePeriods['Boundaries'];
			$this->set('packageRatePeriods', $packageRatePeriods);
		endif;
		
		$this->set('currencyCodes', $this->Package->Currency->find('list', array('fields' => array('currencyCode'))));
		
		if(!isset($this->data['Format']['Format'])):
			$formatList = array();
			foreach ($this->data['Format'] as $format):
		 		$formatList[] = $format['formatId'];
			endforeach;
		
			$this->data['Format']['Format'] = $formatList;
		endif;
		
		$this->setupOfferTypeDefArray();
		
		$familyAmenities = $this->Package->FamilyAmenity->find('list');
		$loaItemTypes = $this->Package->PackageLoaItemRel->LoaItem->LoaItemType->find('list');
		$trackExpirationCriteriaIds = $this->Package->ClientLoaPackageRel->Loa->Track->ExpirationCriterium->find('list');

        // get roomGradeName for this package
        $this->Package->PackageLoaItemRel->recursive = 2;
        $loaItems = $this->Package->PackageLoaItemRel->find('first', array('conditions' => array('Package.packageId' => $id, 'LoaItem.loaItemTypeId' => 1)));
        $roomGradeName = $loaItems['LoaItem']['RoomGrade']['roomGradeName'];
        
		$this->set(compact('loaItemTypes', 'trackExpirationCriteriaIds', 'familyAmenities', 'roomGradeName'));

	}
	
	function preview($clientId = null, $id = null) {
        $this->Package->Behaviors->attach('Containable');
        $this->Package->contain(array('PackageLoaItemRel', 'ClientLoaPackageRel', 'Currency'));

	    $package = $this->Package->read(null, $id);
	    
	    foreach ($package['ClientLoaPackageRel'] as $clientRel) {
	        if ($clientId == $clientRel['clientId']) {
	            $this->Client->recursive = -1;
        	    $client = $this->Client->read(null, $clientId);
        	    $package['Client'] = $client['Client'];
        	    break;
	        }
	    }
	    
	    if (!isset($client)) {
	        $this->cakeError('error404');
	    }
	    
	    $this->Package->PackageLoaItemRel->LoaItem->Behaviors->attach('Containable');
	    $this->Package->PackageLoaItemRel->LoaItem->contain(array('Fee', 'LoaItemRatePeriod'));

	    foreach ($package['PackageLoaItemRel'] as $packageLoaItemRel) {
	        $items[$packageLoaItemRel['loaItemId']] = $this->Package->PackageLoaItemRel->LoaItem->read(null, $packageLoaItemRel['loaItemId']);
	    }

	    foreach ($items as $item) {
	        if($item['LoaItem']['loaItemTypeId'] != 1) {
	            continue;
	        }
	        $ratePeriods[$item['LoaItem']['loaItemId']] = $this->Package->PackageLoaItemRel->LoaItem->carveRatePeriods(array($item['LoaItem']['loaItemId']), array(), $package['Package']['startDate'], $package['Package']['endDate']);
	    }
	    
	    $this->set(compact('package', 'items', 'ratePeriods'));
	    
	    //Fix with the routes... this isn't being automatically pulled
	    if ($this->RequestHandler->prefers('doc')) {
	        $this->layout = 'doc/default';
		    $this->render('doc/preview');
		}
	}
		
	function clonePackage($clientId = null, $id = null)
	{
		$this->Package->clonePackage($id);
	}
	
	function setCorrectNumNights($data) {
	    //set correct number of nights for single product package
	    if ( count( $data['ClientLoaPackageRel']) == 1) {
	        $data['ClientLoaPackageRel'][0]['numNights'] = $data['Package']['numNights'];
	    } if ( count( $data['ClientLoaPackageRel'] > 1)) {
	        $numNights = 0;
	        foreach($data['ClientLoaPackageRel'] as $v) {
	            $numNights += $v['numNights'];
	        }
	        $data['Package']['numNights'] = $numNights;
	    }
	    return $data;
	}
	function sortPackageAgeRange($a, $b) {
		if ($a['rangeLow'] == $b['rangeLow']) {
			return 0;
		}

		return $a['rangeLow'] < $b['rangeLow'] ? -1 : 1;
	}
	function setupOfferTypeDefArray()
	{
		if(empty($this->data['PackageOfferTypeDefField'])) {
			return;
		}
		foreach ($this->data['PackageOfferTypeDefField'] as $defField):
			$defFieldViewArray[$defField['offerTypeId']] = $defField;
			
			if (in_array($defField['offerTypeId'], array(1,2,6)) && $this->data['Package']['approvedRetailPrice'] != 0) {
			    $defFieldViewArray[$defField['offerTypeId']]['percentRetail'] = round($defField['openingBid']/$this->data['Package']['approvedRetailPrice']*100, 2);
			} elseif ($this->data['Package']['approvedRetailPrice'] != 0) {
			    $defFieldViewArray[$defField['offerTypeId']]['percentRetail'] = round($defField['buyNowPrice']/$this->data['Package']['approvedRetailPrice']*100, 2);
			}
		endforeach;
		unset($this->data['PackageOfferTypeDefField']);
		$this->data['PackageOfferTypeDefField'] = $defFieldViewArray;
	}
	
	function sortLoaItemsForEdit($a, $b) {
		if(!isset($this->data['Package']['CheckedLoaItems'])) {
			return;
		}
		return in_array($b['loaItemId'], $this->data['Package']['CheckedLoaItems']);
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
	
	function getOfferTypeDefaultsHtmlFragment($packageId = null) {
		$this->autoRender = false;
		$formatId = $this->data['Format']['Format'][0];
		$this->Package->PackageOfferTypeDefField->recursive = -1;

		$this->setupOfferTypeDefArray();
		
		if(!empty($formatId)):
				$this->render(null, null, "format_defaults_$formatId");
		else:
			return '';
		endif;
	}
	
	function send_for_merch_approval($clientId, $packageId) {
		$clientData = $this->Client->findByClientId($clientId);
	    $this->set('client', $clientData);
	    $this->set('clientId', $clientId);
	    $this->set('packageId', $packageId);

	    if (!empty($this->data)) {
	        $subject = $clientData['Client']['name'] . ' - Package awaiting approval';
	        
	        $body = "The following package was submitted for approval by ".$this->user['LdapUser']['displayname'].": ";
	        $body .= "http://toolbox.luxurylink.com/clients/$clientId/packages/edit/$packageId";
	        $body .= "\n\nAdditional Message: ";
	        $body .= $this->data['additionalMessage'];
	        
	        $headers = "Reply-To: {$this->user['LdapUser']['mail']}\n";
	        $headers .= "From: {$this->user['LdapUser']['mail']}";
                
            if(mail('approval@luxurylink.com', $subject, $body, $headers)) {
                $this->set('closeModalbox', true);
                $this->Session->setFlash(__('The Package has been submitted for Merchandising approval', true), 'default', array(), 'success');
            } else {
                $this->Session->setFlash(__('The Package could not be sent to merchandising for approval', true), 'default', array(), 'error');
            }
	    }
    }
	
	function performanceTooltip($id) {
		$this->Package->PackagePerformance->recursive = -1;
		$metrics = $this->Package->PackagePerformance->find('first', array('conditions' => array('PackagePerformance.packageId' => $id)));

		$this->set('metrics', $metrics['PackagePerformance']);
	}
	
	function tooltipNotes($id) {
	    $this->Package->recursive = -1;
		$notes = $this->Package->find('first', array('fields' => 'notes', 'conditions' => array('Package.packageId' => $id)));

		$this->set('notes', $notes['Package']['notes']);
	}
	
	function age_range_row() {
		$this->autoRender = false;
		$this->set('row', ++$this->params['url']['last']);
		$this->set('data', null);
		$this->render('_age_range_row');
	}

}
?>
