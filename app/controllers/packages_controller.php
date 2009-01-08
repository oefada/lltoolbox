<?php
class PackagesController extends AppController {

	var $name = 'Packages';
	var $helpers = array('Html', 'Form');
	var $uses = array('Package', 'Client', 'PackageRatePeriod');
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
	}

	function index($clientId = null) {
		if (!isset($clientId)) {
			$this->cakeError('error404');
		}
		
		$this->set('packages', $this->paginate('ClientLoaPackageRel', array('ClientLoaPackageRel.clientId' => $clientId)));
		$this->set('packageStatusIds', $this->Package->PackageStatus->find('list'));
		$this->set('client', $this->Client->findByClientId($clientId));
		$this->set('clientId', $clientId);
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
	}
	
	function carveRatePeriods($clientId = null, $id = null) {
		if(empty($this->data['Package']['CheckedLoaItems']))
			return;
		$this->Package->recursive = 2;
		
		$packageStartDate = $this->data['Package']['validityStartDate']['year'] . '-' . $this->data['Package']['validityStartDate']['month'] . '-' . $this->data['Package']['validityStartDate']['day'];
		$packageEndDate = $this->data['Package']['validityEndDate']['year'] . '-' . $this->data['Package']['validityEndDate']['month'] . '-' . $this->data['Package']['validityEndDate']['day'];
		
		$carvedRatePeriods = $this->Package->PackageLoaItemRel->LoaItem->carveRatePeriods($this->data['Package']['CheckedLoaItems'], $this->data['PackageLoaItemRel'], $packageStartDate, $packageEndDate); 
		$this->data['PackageRatePeriod'] = $carvedRatePeriods['PackageRatePeriod'];
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
		
		if (!empty($this->data) && isset($this->data['Package']['complete'])) {
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
			$loa = $this->Client->Loa->findByLoaId($clientLoaPackageRel['loaId']);
			
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
				$this->data['PackageLoaItemRel'][] = $packageLoaItemRel;
			}
		endforeach;
		
		//here we deal with the new items
		if (isset($this->data['Package']['CheckedLoaItems'])):
		foreach($this->data['Package']['CheckedLoaItems'] as $k => $checkedLoaItem) {
			if (!in_array($checkedLoaItem, $currentItemIds)):
				$newPackageLoaItems[$k]['quantity'] = $newPackageLoaItemRel[$checkedLoaItem]['quantity'];
				$newPackageLoaItems[$k]['weight'] = $newPackageLoaItemRel[$checkedLoaItem]['weight'];
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
			if (@$this->data['clone'] == 'clone') {
				$this->data = $this->Package->cloneData($this->data);
				$this->addPackageLoaItems();
				$cloned = true;
			} else {
				$this->Package->PackageRatePeriod->deleteAll(array('PackageRatePeriod.packageId' => $this->data['Package']['packageId']));
				$this->updatePackageLoaItems();
				$cloned = false;
			}

			$this->getBlackoutDaysNumber();
			$this->carveRatePeriods($clientId);
			//remove all offer type defaults so we don't get duplicates
			$this->Package->PackageOfferTypeDefField->deleteAll(array('PackageOfferTypeDefField.packageId' => $this->data['Package']['packageId']), false);
			
			//remove all recurring days so we don't get duplicates
			$this->Package->PackageValidityPeriod->deleteAll(array('PackageValidityPeriod.packageId' => $this->data['Package']['packageId'], 'isWeekDayRepeat' => 1), false);
			
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
			$this->Package->recursive = 2;
			$package = $this->Package->read(null, $id);
			$this->data = $package;
		}
		$this->set('package', $package);
		$this->getBlackoutDaysNumber(1);
		$this->Package->ClientLoaPackageRel->recursive = -1;
		$clientLoaPackageRel = $this->Package->ClientLoaPackageRel->findAllByPackageId($id);
	
		foreach($this->data['ClientLoaPackageRel'] as $key => $clientLoaPackageRel):
			$clientLoaDetails[$key] = $this->Client->Loa->findByLoaId($clientLoaPackageRel['loaId']);
			
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
		
		$itemList = $this->Package->PackageLoaItemRel->LoaItem->find('list');
		$itemCurrencyIds = $this->Package->PackageLoaItemRel->LoaItem->find('list', array('fields' => array('currencyId')));
		
		foreach($this->data['PackageRatePeriod'] as $ratePeriod):
			//setup the arrays needed to draw the rate period table			
			//the boundaries are used to draw all of the columns
			$boundaries[$ratePeriod['startDate']]['rangeStart'] = $ratePeriod['startDate'];
			$boundaries[$ratePeriod['startDate']]['rangeEnd'] = $ratePeriod['endDate'];
			
			if(!isset($boundaries[$ratePeriod['startDate']]['rangeSum'])) $boundaries[$ratePeriod['startDate']]['rangeSum'] = 0;
			$boundaries[$ratePeriod['startDate']]['rangeSum'] += $ratePeriod['ratePeriodPrice']*$ratePeriod['quantity']+($ratePeriod['ratePeriodPrice']*$ratePeriod['quantity']*$ratePeriod['feePercent']/100);
			
			//setup an array that has itemId => array('startDate', 'endDate', 'price'), so we can draw each row
			$itemRatePeriods['IncludedItems'][$ratePeriod['loaItemId']]['itemName'] = $itemList[$ratePeriod['loaItemId']];
			$itemRatePeriods['IncludedItems'][$ratePeriod['loaItemId']]['currencyId'] = $itemCurrencyIds[$ratePeriod['loaItemId']];
			$itemRatePeriods['IncludedItems'][$ratePeriod['loaItemId']]['PackageRatePeriod'][] = $ratePeriod;
		endforeach;
		
		if (isset($boundaries)):
		//sort and re-set keys for the boundaries array
			sort($boundaries);
			array_merge($boundaries, array());
		
			$packageRatePeriods = $itemRatePeriods;
			$packageRatePeriods['Boundaries'] = $boundaries;
		
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
	}
		
	function clonePackage($clientId = null, $id = null)
	{
		$this->Package->clonePackage($id);
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
	
	function performanceTooltip($id) {
		$this->Package->PackagePerformance->recursive = -1;
		$metrics = $this->Package->PackagePerformance->find('first', array('conditions' => array('PackagePerformance.packageId' => $id)));

		$this->set('metrics', $metrics['PackagePerformance']);
	}

}
?>