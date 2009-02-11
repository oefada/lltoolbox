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
	    $packageId 				= $this->params['named']['packageId'];
		$package 				= $this->SchedulingMaster->Package->findByPackageId($packageId);
	    
		if (!empty($this->data)) {
			$this->SchedulingMaster->create();
			$package = $this->SchedulingMaster->Package->findByPackageId($this->data['SchedulingMaster']['packageId']);
			$this->data['SchedulingMaster']['validityStartDate'] = $package['Package']['validityStartDate'];
			$this->data['SchedulingMaster']['validityEndDate'] = $package['Package']['validityEndDate'];
			$this->data['SchedulingMaster']['retailValue'] = $package['Package']['approvedRetailPrice'];

			$date = explode('-', $this->data['SchedulingMaster']['startDatePicker']);

			$this->data['SchedulingMaster']['startDate']['month']     =   $date[0];
			$this->data['SchedulingMaster']['startDate']['day']   =   $date[1];
			$this->data['SchedulingMaster']['startDate']['year']    =   $date[2];

            $this->data['SchedulingMaster']['startDate'] = array_merge($this->data['SchedulingMaster']['startDate'], $this->data['SchedulingMaster']['startDateTime']);

			//if this is a mystery auction we override some fields
			if (in_array(3, $this->data['MerchandisingFlag']['MerchandisingFlag'])) {
			    $this->data['SchedulingMaster']['openingBid']   = $this->data['Mystery']['openingBid'];
			    $this->data['SchedulingMaster']['bidIncrement'] = $this->data['Mystery']['bidIncrement'];
			    $this->data['SchedulingMaster']['packageName']  = $this->data['Mystery']['packageName'];
			    $this->data['SchedulingMaster']['subtitle']     = $this->data['Mystery']['subtitle'];
			    $this->data['SchedulingMaster']['shortBlurb']   = $this->data['Mystery']['shortBlurb'];
			}
			
			//associate the tracks from each client to this offer
			foreach ($package['ClientLoaPackageRel'] as $v) {
			    $this->data['Track']['Track'][] = $v['trackId'];
			}

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
		
		$packageEndDate = explode('-', $package['Package']['endDate']);
		$packageEndDate = array('year' => $packageEndDate[0], 'month' => $packageEndDate[1], 'day' => $packageEndDate['2']);
		$this->set('packageEndDate', $packageEndDate);

		foreach ($package['Format'] as $format):
			$formatIds[] = $format['formatId'];
		endforeach;
		
		//if no formats were selected for this package, we can't schedule it
		if (count($formatIds) == 0) {
			echo '<h3>This package is not ready to be scheduled because no formats have been associated with it</h3>';
			die();
		}
		//if package is not approved, do not allow scheduling
		if ($package['Package']['packageStatusId'] != 4) {
			echo '<h3>This package cannot be scheduled because it is not approved.</h3>';
			die();
		}
		
		if (empty($this->data) && isset($this->params['named']['date'])) {
			$date = explode('-', $this->params['named']['date']);
            
            $this->data['SchedulingMaster']['startDatePicker'] = date("m-d-Y", strtotime($this->params['named']['date']));
            //populate the start date to the date selected from the datepicker
			$this->data['SchedulingMaster']['startDateTime']['hour'] 		= date('g');
			$this->data['SchedulingMaster']['startDateTime']['min']         = 0;
			$this->data['SchedulingMaster']['startDateTime']['meridian']    = date('a');
			
			//default end date to the end date for the package scheduling range
			
			$this->data['SchedulingMaster']['endDate']['year']          = $packageEndDate['year'];
			$this->data['SchedulingMaster']['endDate']['month']         = $packageEndDate['month'];
			$this->data['SchedulingMaster']['endDate']['day']           = $packageEndDate['day'];
			$this->data['SchedulingMaster']['numDaysToRun']             = 3;
			
			$this->data['SchedulingMaster']['packageName']              = trim($package['Package']['packageName']);
			$this->data['SchedulingMaster']['retailValue']              = $package['Package']['approvedRetailPrice'];
		}
	
	    $this->setOfferTypeDefaultAndDropdown($packageId, $formatIds);
		
		$merchandisingFlags 					= $this->SchedulingMaster->MerchandisingFlag->find('list');
		$schedulingStatusIds 					= $this->SchedulingMaster->SchedulingStatus->find('list');
		$schedulingDelayCtrlIds 				= $this->SchedulingMaster->SchedulingDelayCtrl->find('list');
		$remittanceTypeIds 						= $this->SchedulingMaster->RemittanceType->find('list');
    
		
		$this->set('package', 					$package);
		$this->set('packageId', 				$packageId);
		$this->set('merchandisingFlags', 		$merchandisingFlags);
		$this->set('schedulingStatusIds', 		$schedulingStatusIds);
		$this->set('schedulingDelayCtrlIds', 	$schedulingDelayCtrlIds);
		$this->set('remittanceTypeIds', 		$remittanceTypeIds);
	}
	
	function setOfferTypeDefaultAndDropdown($packageId, $formatIds) {
	    /* Get all Offer Types available for this package based on Format */
		$this->SchedulingMaster->Package->Format->Behaviors->attach('Containable');
		$formats = $this->SchedulingMaster->Package->Format->find('all', array('conditions' => array('formatId' => $formatIds), 'contain' => array('OfferType')));
		
		foreach ($formats as $format) {
			foreach ($format['OfferType'] as $k => $v) {
			    //set the firstOffer Id to the first one in the array, so we have something to pull during initial form loading
			    if (!isset($firstOfferId) ) {
			        $firstOfferId = $v['offerTypeId'];
			    }
			    $offerTypeIds[$v['offerTypeId']] = $v['offerTypeName'];
			}
		}
		
		$offerTypeId = (isset($this->data['SchedulingMaster']['offerTypeId'])) ? $this->data['SchedulingMaster']['offerTypeId'] : $firstOfferId;
		$this->SchedulingMaster->Package->PackageOfferTypeDefField->recursive = -1;
		$defaults = $this->SchedulingMaster->Package->PackageOfferTypeDefField->find('first', array('conditions' => array('PackageOfferTypeDefField.packageId' => $packageId, 'PackageOfferTypeDefField.offerTypeId' => $offerTypeId)));        
		$this->set(compact('defaults'));    //send defaults to the view for the drop down
		
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
			    
			default:
			    $this->set('defaultFile', false);
			    break;
		endswitch;
		
		$this->set('offerTypeIds', $offerTypeIds);
	}
	
	/**
	 * Temporary method to be used during migration. This will fill in the iterations for a master with missing iterations.
	 * METHOD NOT TO BE USED AFTER LAUNCH OR THE SKY WILL FALL OUT OF THE HEAVENS!!!
	 */
	function fix_instances() {
	    Configure::write('debug', 3);
	    
	    $sql = 'SELECT schedulingMasterId, offerTypeId, numDaysToRun, SchedulingMaster.endDate, MAX(SchedulingInstance.endDate) as maxEndDate, iterations, COUNT(DISTINCT schedulingInstanceId) as numIterations 
                                                                                        FROM schedulingMaster AS SchedulingMaster
                                                                                        INNER JOIN schedulingInstance AS SchedulingInstance USING (schedulingMasterId) 
                                                                                        WHERE SchedulingMaster.offerTypeId IN (1, 2, 6) AND (iterations IS NULL OR SchedulingMaster.endDate IS NULL)
                                                                                        AND SchedulingMaster.endDate >= NOW()
                                                                                        GROUP BY schedulingMasterId 
                                                                                        HAVING numIterations <> iterations OR iterations IS NULL';
	    $masters = $this->SchedulingMaster->query($sql);

	    foreach ($masters as $master):
            set_time_limit(120);
	        $masterData = $master;
	        
	        if ($master['SchedulingMaster']['endDate'] == null) {
    	        $masterData['SchedulingMaster']['iterationSchedulingOption'] = 0;
    	        $masterData['SchedulingMaster']['iterations'] = $masterData['SchedulingMaster']['iterations'] - $master[0]['numIterations'];
	        } else {
    	        $masterData['SchedulingMaster']['iterationSchedulingOption'] = 1;
	        }
	        $masterData['SchedulingMaster']['startDate'] = $master[0]['maxEndDate'];
	        $masterData['SchedulingDelayCtrl']['schedulingDelayCtrlDesc'] = '1 hour';

	        $this->createInstances($masterData, &$out);
	        
		    $this->SchedulingMaster->id = $masterData['SchedulingMaster']['schedulingMasterId'];
	        if ($master['SchedulingMaster']['endDate'] == null) {
                $this->SchedulingMaster->saveField('endDate', $out['endDate']);
	        } else {
    	        $iterations = $out['iterations'] + $master[0]['numIterations'];
    	        $this->SchedulingMaster->saveField('iterations', $iterations);
	        }
	    endforeach;
	    
	    $this->autoRender = false;
	}
	
	/**
	 * Method creates instances for a master.
	 * @param array optional array to be used as a fake master to override how the method works (@see fix_instances)
	 */
	function createInstances($masterData = array(), &$out = null) {
	    
	    if (empty($masterData)):
		    $masterData                 = $this->SchedulingMaster->read(null);
		endif;
		
		$iterations                 = $masterData['SchedulingMaster']['iterations'];
		$masterStartDate            = $masterData['SchedulingMaster']['startDate'];
		$numDaysToRun               = $masterData['SchedulingMaster']['numDaysToRun'];
		$endDate                    = $masterData['SchedulingMaster']['endDate'];
		$masterEndDate              = strtotime($endDate);
		$iterationSchedulingOption  = $masterData['SchedulingMaster']['iterationSchedulingOption'];
		$instanceData = array();
		
		$instanceData['SchedulingInstance']['schedulingMasterId'] 	= $masterData['SchedulingMaster']['schedulingMasterId'];
		$instanceData['SchedulingInstance']['startDate'] 			= $masterData['SchedulingMaster']['startDate'];
		
		/*
		 * For fixed price offers, we only grab the start and end dates. Don't care about number of iterations, or delays.
		 */
		if (in_array($masterData['SchedulingMaster']['offerTypeId'], array(3,4))) {
		    $instanceData['SchedulingInstance']['startDate']    = $masterData['SchedulingMaster']['startDate'];
		    $instanceData['SchedulingInstance']['endDate']      = $masterData['SchedulingMaster']['endDate'];
		    $this->SchedulingMaster->SchedulingInstance->create();
			$this->SchedulingMaster->SchedulingInstance->save($instanceData);
		    return true;
		}
		/* 
		 * If using the endDate to determine how many iterations to create, we need to calculate this number
		 * This basically does the following arithmethic:
		 *                  end   - start
		 *  #iterations =   -------------
		 *                  start + delay
		 * and then rounds it down to the nearest whole number
		 */
		if ($iterationSchedulingOption == 1) {
		    $totalInstanceTime = strtotime($masterStartDate . ' +' . $masterData['SchedulingDelayCtrl']['schedulingDelayCtrlDesc']. ' +' . $numDaysToRun.' days');
		    $totalInstanceTime = $totalInstanceTime - strtotime($masterStartDate);

		    $startEndRange = strtotime($endDate) - strtotime($masterStartDate);

		    $iterations = floor($startEndRange / $totalInstanceTime);
		    $out['iterations'] = $iterations;
		    $this->SchedulingMaster->id = $masterData['SchedulingMaster']['schedulingMasterId'];
		    $this->SchedulingMaster->saveField('iterations', $iterations);
		}
        if ($iterations > 30) $iterations = 30;
        //TODO: Put this logic in the model where it belongs
		for ($i = 0; $i < $iterations; $i++) {
			$endDate = strtotime($instanceData['SchedulingInstance']['startDate'] . ' +' . $masterData['SchedulingMaster']['numDaysToRun'] . ' days');
			
			while ($this->_isHoliday($endDate)) {
			    $endDate = strtotime('+1 day', $endDate);
			}
			
			if ($startDate > $masterEndDate) {
			    break;
			}
			
			$instanceData['SchedulingInstance']['endDate'] = date('Y-m-d H:i:s', $endDate);
				
            $out['endDate'] = $instanceData['SchedulingInstance']['endDate'];
            
            $instancesToSave[] = $instanceData;
		
			$startDate = strtotime($instanceData['SchedulingInstance']['endDate'] . ' +' . $masterData['SchedulingDelayCtrl']['schedulingDelayCtrlDesc']);
			
			//check if the start date is greater than 4pm, 16 in 24-hour format
			if (date('G', $startDate) >= 16) {
			    $startDate = strtotime('+1 day', $startDate);   //push to next day
			    $startDate = date('Y-m-d', $startDate);         //conver to just Y-m-d
			    $startDate = $startDate.' 08:00:00';            //set time to 8am
			    $startDate = strtotime($startdate);             //save ne date as timestamp
			}
			//check if new start date is to open on a holiday, if so, push it ahead a day
			while ($this->_isHoliday($startDate)) {
			    $startDate = strtotime('+1 day', $startDate);
			}
			
			if ($startDate > $masterEndDate) {
			    break;
			}
			
			$instanceData['SchedulingInstance']['startDate'] = date('Y-m-d H:i:s', $startDate);	
		}
		
		if (!empty($instancesToSave)) {
    		$this->SchedulingMaster->SchedulingInstance->create();
    		$this->SchedulingMaster->SchedulingInstance->saveAll($instancesToSave);
		}
	}
	
	/**
	 * Method takes a timestamp and determins if it falls on a predeterined holiday
	 * @param $timestamp the timestamp to check
	 * @return boolean true if timestamp falls on a holiday, false if not
	 */
	function _isHoliday($timestamp) {
	    $holidays = array('1/1','7/4','12/24','12/25');
	    //memorial day, labor day
	    date('Y', $timestamp);
	    //thanksgiving, friday after thanksgiving
	    $thanksgiving = strtotime("third thursday",mktime(0,0,0,11,1,date('Y', $timestamp)));
	    $holidays[] = date('n/j', $thanksgiving);
	    $holidays[] = date('n/j', strtotime('+1 day', $thanksgiving));
	    
	    //memorial day
	    $lastMondayInMay = strtotime("fourth monday",mktime(0,0,0,5,1,date('Y', $timestamp)));
	    $nextMonday =  strtotime("fifth monday",mktime(0,0,0,5,1,date('Y', $timestamp)));
	    
	    if(date('n', $nextMonday) == '5') {
	        $lastMondayInMay = $nextMonday;
	    }
	    $holidays[] = date('n/j', $lastMondayInMay);
	    
	    //labor day
	    $holidays[] = date('n/j', strtotime('monday', mktime(0,0,0,9,1,date('y', $timestamp))));
	    
	    //check all of the days in the holidays array
	    if (in_array(date('n/j', $timestamp), $holidays)) {
	        return true;
	    }
    
        return false;
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid SchedulingMaster', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			$this->data['SchedulingMaster']['schedulingMasterId'] = $id;
			$originalData = $this->SchedulingMaster->read(null, $id);
			
			$remainingIterations = 0;
			foreach ($originalData['SchedulingInstance'] as $k => $instance):
    		    if (strtotime($instance['startDate']) > time()) {
    		        $remainingIterations++;
                }
    		endforeach;
    		
    		/* If there are no future iterations, we can't do anything. */
    		if ($remainingIterations == 0) {
    		    if ($this->RequestHandler->isAjax()) {
					$this->Session->setFlash(__('The Schedule could not be saved', true), 'default', array(), 'error');
					$this->set('closeModalbox', true);
				}
    		}
    		
    		/* If no iterations have gone live yet, we can do whatever we want to this */
    		if ($remainingIterations == count($originalData['SchedulingInstance'])) {
    		    $this->SchedulingMaster->SchedulingInstance->deleteAll(array('SchedulingInstance.schedulingMasterId' => $id));
            			//if this is a mystery auction we override some fields
            			if (in_array(3, $this->data['MerchandisingFlag']['MerchandisingFlag'])) {
            			    $this->data['SchedulingMaster']['openingBid']   = $this->data['Mystery']['openingBid'];
            			    $this->data['SchedulingMaster']['bidIncrement'] = $this->data['Mystery']['bidIncrement'];
            			    $this->data['SchedulingMaster']['packageName']  = $this->data['Mystery']['packageName'];
            			    $this->data['SchedulingMaster']['subtitle']     = $this->data['Mystery']['subtitle'];
            			    $this->data['SchedulingMaster']['shortBlurb']   = $this->data['Mystery']['shortBlurb'];
            			}
            			
            			$date = explode('-', $this->data['SchedulingMaster']['startDatePicker']);

            			$this->data['SchedulingMaster']['startDate']['month']     =   $date[0];
            			$this->data['SchedulingMaster']['startDate']['day']   =   $date[1];
            			$this->data['SchedulingMaster']['startDate']['year']    =   $date[2];

                        $this->data['SchedulingMaster']['startDate'] = array_merge($this->data['SchedulingMaster']['startDate'], $this->data['SchedulingMaster']['startDateTime']);
            			

            			if ($this->SchedulingMaster->save($this->data)) {
            				$this->createInstances();
            				if ($this->RequestHandler->isAjax()) {
            					$this->Session->setFlash(__('The Schedule has been saved', true), 'default', array(), 'success');
            					$this->set('closeModalbox', true);
            				}
            			} else {
            				$this->Session->setFlash(__('The Schedule could not be saved. Please correct the errors below.', true), 'default', array(), 'error');
            			}
    		} else {
    		    echo '<h3 class="icon-error">Could not save changes. Atleast one offer has already gone live. You must delete all future offers and create a new scheduling master. ';
    		}
		}
		if (empty($this->data)) {
			$this->data = $this->SchedulingMaster->read(null, $id);
		
			$date       = explode(' ', $this->data['SchedulingMaster']['startDate']);
			$datePicker = explode('-', $date[0]);

			$this->data['SchedulingMaster']['startDatePicker']     =   date('m-d-Y', strtotime($date[0]));
			
			$this->data['SchedulingMaster']['startDateTime'] = $this->data['SchedulingMaster']['startDate'];
		}
		
		/* Check if there are any iterations left. If there are none, then we can't edit this */
		$remainingIterations = 0;
		foreach ($this->data['SchedulingInstance'] as $k => $instance):
		    if (strtotime($instance['startDate']) > time()) {
		        $remainingIterations++;
            }
		endforeach;
		$merchandisingFlags = $this->SchedulingMaster->MerchandisingFlag->find('list');
		$schedulingStatusIds = $this->SchedulingMaster->SchedulingStatus->find('list');
		$schedulingDelayCtrlIds = $this->SchedulingMaster->SchedulingDelayCtrl->find('list');
		$remittanceTypeIds = $this->SchedulingMaster->RemittanceType->find('list');
		
		$packageId 				= $this->data['SchedulingMaster']['packageId'];
		$package 				= $this->SchedulingMaster->Package->findByPackageId($packageId);
		
		foreach ($package['Format'] as $format):
			$formatIds[] = $format['formatId'];
		endforeach;
		
		$this->setOfferTypeDefaultAndDropdown($packageId, $formatIds);
		
		$this->set('package', 					$package);
		$this->set('packageId', 				$packageId);
		$this->set('remainingIterations',       $remainingIterations);
		$this->set('merchandisingFlags',        $merchandisingFlags);
		$this->set('schedulingStatusIds',       $schedulingStatusIds);
		$this->set('schedulingDelayCtrlIds',    $schedulingDelayCtrlIds);
	}

    //TODO: fix the number of iterations after delete
	function delete($id = null) {
	    $this->autoRender = false;
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SchedulingMaster', true));
			$this->set('closeModalbox', true);
		}

		$this->SchedulingMaster->deleteAll(array('SchedulingMaster.startDate > NOW()', 'SchedulingMaster.schedulingMasterId' => $id));
	    $this->SchedulingMaster->SchedulingInstance->deleteAll(array('SchedulingInstance.startDate > NOW()', 'SchedulingInstance.schedulingMasterId' => $id));
	    
		$this->Session->setFlash(__('The scheduling master and/or iterations have been deleted', true), 'default', array(), 'success');	
		echo "<div id='closeModalbox'>abc</div>";
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