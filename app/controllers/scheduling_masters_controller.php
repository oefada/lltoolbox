<?php
class SchedulingMastersController extends AppController {

	var $name = 'SchedulingMasters';
	var $helpers = array('Html', 'Form');
	var $uses       = array('SchedulingMaster', 'OfferLive');

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

            $datePickerDate = explode('-', $this->data['SchedulingMaster']['startDatePicker']);
            $dateTime = $datePickerDate[2].'-'.$datePickerDate[0].'-'.$datePickerDate[1];
            $dateTime .= ' ';
            $dateTime .= $this->data['SchedulingMaster']['startDateTime']['hour'].':00:00 '.$this->data['SchedulingMaster']['startDateTime']['meridian'];
            
            if (date('G', strtotime($dateTime)) >= 16) {
			    $startDate = strtotime('+1 day',  strtotime($dateTime));   //push to next day
			    $startDate = date('Y-m-d', $startDate);         //convert to just Y-m-d
			    $date = explode('-', $startDate);
			    
			    $this->data['SchedulingMaster']['startDateTime']['hour'] = '08';
			    $this->data['SchedulingMaster']['startDateTime']['min'] = '00';
			    $this->data['SchedulingMaster']['startDateTime']['meridian'] = 'am';
			} else {
			    $date[0] = $datePickerDate[2];
			    $date[1] = $datePickerDate[0];
			    $date[2] = $datePickerDate[1];
			}

            $this->data['SchedulingMaster']['startDate']['year']    =   $date[0];
			$this->data['SchedulingMaster']['startDate']['month']     =   $date[1];
			$this->data['SchedulingMaster']['startDate']['day']   =   $date[2];
			
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
	
	function setOfferTypeDefaultAndDropdown($packageId, $formatIds, $schedulingMaster = false) {
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
		
		asort($offerTypeIds);    //Sort offer types by name
		
		$offerTypeId = (isset($this->data['SchedulingMaster']['offerTypeId'])) ? $this->data['SchedulingMaster']['offerTypeId'] : $firstOfferId;
		$this->SchedulingMaster->Package->PackageOfferTypeDefField->recursive = -1;
		if ($schedulingMaster) {
		    $defaults['PackageOfferTypeDefField']['buyNowPrice'] = $schedulingMaster['SchedulingMaster']['buyNowPrice'];
		    $defaults['PackageOfferTypeDefField']['openingBid'] = $schedulingMaster['SchedulingMaster']['openingBid'];
		} else {
		    $defaults = $this->SchedulingMaster->Package->PackageOfferTypeDefField->find('first', array('conditions' => array('PackageOfferTypeDefField.packageId' => $packageId, 'PackageOfferTypeDefField.offerTypeId' => $offerTypeId)));        
		}
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
	 * Method creates instances for a master.
	 * @param array optional array to be used as a fake master to override how the method works (@see fix_instances)
	 */
	function createInstances($masterData = array(), &$out = null, $skipDb = false, $file = null) {
	    
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
		    
		    if (false == $skipDb) {
		        $this->SchedulingMaster->id = $masterData['SchedulingMaster']['schedulingMasterId'];
    		    $this->SchedulingMaster->saveField('iterations', $iterations);
		    }
		}
		
		if ($iterations <= 0) {
		    return;
		}
        $out['iterations'] = 0;
        //TODO: Put this logic in the model where it belongs
		for ($i = 0; $i < $iterations; $i++) {
			$endDate = strtotime($instanceData['SchedulingInstance']['startDate'] . ' +' . $masterData['SchedulingMaster']['numDaysToRun'] . ' days');
			
			while ($this->_isHoliday($endDate)) {
			    $endDate = strtotime('+1 day', $endDate);
			}
			
			if ($endDate > $masterEndDate) {
			    break;
			}
			
			$instanceData['SchedulingInstance']['endDate'] = date('Y-m-d H:i:s', $endDate);
				
            $out['endDate'] = $instanceData['SchedulingInstance']['endDate'];
            
            if (false == $skipDb) {
        		$this->SchedulingMaster->SchedulingInstance->create();
        		$this->SchedulingMaster->SchedulingInstance->save($instanceData);
    		} else {
    		    $out['iterations'] += 1;
                fwrite($file, $instanceData['SchedulingInstance']['schedulingMasterId'].",".$instanceData['SchedulingInstance']['startDate'].",".$instanceData['SchedulingInstance']['endDate']."\n");
    		}
			$startDate = strtotime($instanceData['SchedulingInstance']['endDate'] . ' +' . $masterData['SchedulingDelayCtrl']['schedulingDelayCtrlDesc']);
			
			//check if the start date is greater than 4pm, 16 in 24-hour format
			if (date('G', $startDate) >= 17) {
			    $startDate = strtotime('+1 day', $startDate);   //push to next day
			    $startDate = date('Y-m-d', $startDate);         //conver to just Y-m-d
			    $startDate = $startDate.' 07:00:00';            //set time to 8am
			    $startDate = strtotime($startDate);             //save new date as timestamp
			}

			$instanceData['SchedulingInstance']['startDate'] = date('Y-m-d H:i:s', $startDate);	
		}
		
		if (!empty($instancesToSave) && $skipDb == true) {
		    return $instancesToSave;
	    }

	}
	
	/**
	 * Method takes a timestamp and determins if it falls on a predeterined holiday
	 * @param $timestamp the timestamp to check
	 * @return boolean true if timestamp falls on a holiday, false if not
	 */
	function _isHoliday($timestamp) {
	    $dateToCheck = date('n/j', $timestamp);
	    
	    if('1/1' == $dateToCheck) return true;
	    if('7/4' == $dateToCheck) return true;
	    if('12/24' == $dateToCheck) return true;
	    if('12/25' == $dateToCheck) return true;
	    
	    //memorial day, labor day
	    $year = date('Y', $timestamp);
        if(!isset($this->_tmpHolidays[$year])) {
            //thanksgiving, friday after thanksgiving
    	    $thanksgiving = strtotime("third thursday",mktime(0,0,0,11,1,$year));
    	    $this->_tmpHolidays[$year][] = date('n/j', $thanksgiving);
    	    $this->_tmpHolidays[$year][] = date('n/j', strtotime('+1 day', $thanksgiving));

    	    //memorial day
    	    $lastMondayInMay = strtotime("fourth monday",mktime(0,0,0,5,1,$year));
    	    $nextMonday =  strtotime("fifth monday",mktime(0,0,0,5,1,$year));

    	    if(date('n', $nextMonday) == '5') {
    	        $lastMondayInMay = $nextMonday;
    	    }
    	    $this->_tmpHolidays[$year][] = date('n/j', $lastMondayInMay);

    	    //labor day
    	    $this->_tmpHolidays[$year][] = date('n/j', strtotime('monday', mktime(0,0,0,9,1,$year)));
        }
	    
	    //check all of the days in the holidays array
	    if (in_array($dateToCheck, $this->_tmpHolidays[$year])) {
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
		
		$this->setOfferTypeDefaultAndDropdown($packageId, $formatIds, $this->data);
		
		//the state of the master is 0 if it hasn't gone live yet, or 1 if atleast one iteration has gone live
		if (strtotime($this->data['SchedulingMaster']['startDate']) >= time()) {
		    $masterState = 0;
		} else {
		    $masterState = 1;
		}
		
		$this->set('masterState',               $masterState);
		$this->set('package', 					$package);
		$this->set('packageId', 				$packageId);
		$this->set('remainingIterations',       $remainingIterations);
		$this->set('merchandisingFlags',        $merchandisingFlags);
		$this->set('schedulingStatusIds',       $schedulingStatusIds);
		$this->set('schedulingDelayCtrlIds',    $schedulingDelayCtrlIds);
	}

	function delete($id = null) {
	    $this->autoRender = false;
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SchedulingMaster', true));
			$this->set('closeModalbox', true);
		}

	    $this->SchedulingMaster->deleteAll(array('SchedulingMaster.startDate > NOW()', 'SchedulingMaster.schedulingMasterId' => $id));
	    $this->SchedulingMaster->SchedulingInstance->deleteAll(array('SchedulingInstance.startDate > NOW()', 'SchedulingInstance.schedulingMasterId' => $id));
	 
	    $this->SchedulingMaster->id = $id;
	    $masterData = $this->SchedulingMaster->read();
	    
	    if(!empty($masterData)) {
    	    $numInstances = count($masterData['SchedulingInstance']);

    	    $this->SchedulingMaster->saveField('iterations', $numInstances);
	    }


		$this->Session->setFlash(__('The scheduling master and/or iterations have been deleted', true), 'default', array(), 'success');	
		echo "<div id='closeModalbox'>abc</div>";
	}
	
	function closeFixedPriceOffer($id = null) {
	    if (!$id) {
			$this->Session->setFlash(__('Invalid id for SchedulingMaster', true));
			$this->set('closeModalbox', true);
			die();
		}
		
		$this->data = $this->SchedulingMaster->read(null, $id);
		if($this->data['SchedulingMaster']['offerTypeId'] != 3 && $this->data['SchedulingMaster']['offerTypeId'] != 4) {
		    $this->Session->setFlash(__('Cannot stop a non-fixed priced offer', true), 'default', array(), 'error');
			$this->set('closeModalbox', true);
		    die();
		}

		if(strtotime($this->data['SchedulingMaster']['startDate']) < time()) {
		    $newEndDate = date('Y-m-d H:i:s');
            
            $schedulingInstanceId = $this->data['SchedulingInstance'][0]['schedulingInstanceId'];
		    $offerLiveResults = $this->OfferLive->query("SELECT OfferLive.* FROM offerLive as OfferLive INNER JOIN  offer AS Offer USING(offerId) WHERE Offer.schedulingInstanceId = ".$schedulingInstanceId);
		    
            $schedulingMaster['SchedulingMaster']       = $this->data['SchedulingMaster'];
		    $schedulingInstance['SchedulingInstance']   = $this->data['SchedulingInstance'][0];
		    $offerLive['OfferLive']                     = $offerLiveResults[0]['OfferLive'];
		    
		    $schedulingMaster['SchedulingMaster']['endDate']        = $newEndDate;
		    $schedulingInstance['SchedulingInstance']['endDate']    = $newEndDate;
		    $offerLive['OfferLive']['endDate']                      = $newEndDate;

            if ($this->SchedulingMaster->save($schedulingMaster) &&
                $this->SchedulingMaster->SchedulingInstance->save($schedulingInstance) &&
                $this->OfferLive->save($offerLive) )
            {
                    
                $this->Session->setFlash(__('The offer has been stopped', true), 'default', array(), 'success');
        		$this->set('closeModalbox', true);
            
            } else {
            
                $this->Session->setFlash(__('The offer could not been stopped, please contact tech support and report this issue', true), 'default', array(), 'error');
        		$this->set('closeModalbox', true);
            
            }
		} else {
		    $this->delete($id);
		    $this->Session->setFlash(__('The offer has been stopped', true), 'default', array(), 'success');
    		$this->set('closeModalbox', true);
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