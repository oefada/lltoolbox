<?php
function printR($arr){
 echo "<pre>";
 print_r($arr);
 echo "</pre>";
}
class SchedulingMastersController extends AppController {

	var $name = 'SchedulingMasters';
	var $helpers = array('Html', 'Form');
	var $uses = array(
		'SchedulingMaster', 
		'OfferLuxuryLink', 
		'Loa', 
		'OfferFamily',
		'Ticket',
		'Bid',
	);

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
        $packageId 		= $this->params['named']['packageId'];
		$package 		= $this->SchedulingMaster->Package->findByPackageId($packageId);
		$clientId       = $this->params['named']['clientId'];

		if (isset($this->data) && !empty($this->data)) {
			$err_msg = '';
			if ($this->params['data']['isAuction'] || $this->params['data']['isOptimized']){
				foreach ($this->params['form']['auction_arr'] as $key=>$val){
					$val=(int)$val;
					if ($val==0){
						$err_msg.="You've chosen to schedule an auction, but the Auction Retail value is 0.<br>";
					}
				}

			}
			if ($this->params['data']['isBuyNow'] || $this->params['data']['isOptimized']){
				foreach ($this->params['form']['buynow_arr'] as $key=>$val){
					$val=(int)$val;
					if ($val==0){
						$err_msg.="You've chosen to schedule a buynow, but buynow retail value is 0.<br>";
					}
				}
			}

			// enabling checkboxes and multiple submissions with different pricepoints but the same data
			$ppid_arr = array();
			foreach($this->data['SchedulingMaster']['pricePointId'] as $key=>$ppid){
				$ppid_arr[] = $ppid;
			}

        	// initialize Discountable
        	$this->data['SchedulingMaster']['isDiscountedOffer'] = intval($this->data['SchedulingMaster']['isDiscountedOffer']);
        	
        	if ($this->params['data']['isOptimized']) {
        		$this->data['buyNowOfferTypeId'] = 8;
        	}

			if ($err_msg != "") {
				$this->Session->setFlash(__($err_msg, true), 'default', array(), 'error');
				
			} elseif (!$this->data['isAuction'] && !$this->data['isBuyNow'] && !$this->data['isHotelOffer'] && !$this->data['isOptimized']) {
				$this->Session->setFlash(__('You must schedule at least an auction or a buy now.', true), 'default', array(), 'error');
				
			} elseif (sizeof($ppid_arr) == 0) {
				$this->Session->setFlash(__('You must choose at least one Price Point.', true), 'default', array(), 'error');
				
			} elseif (!$this->data['Track']['Track'][0]) {
				$this->Session->setFlash(__('Please select an LOA Track.', true), 'default', array(), 'error');

			} elseif ($this->data['SchedulingMaster']['isDiscountedOffer'] == '1' && intval($this->data['SchedulingMaster']['percentDiscount']) == 0){
				$this->Session->setFlash(__('Apply Discount selected, but no discount % found.', true), 'default', array(), 'error');
				
			} elseif (intval($this->data['SchedulingMaster']['isDiscountedOffer']) == 0 && intval($this->data['SchedulingMaster']['percentDiscount']) > 0){
				$this->Session->setFlash(__('Apply Discount not selected, but discount % found.', true), 'default', array(), 'error');
				
			} else {
			
				// validate price points for optimized offers
				$flexCheck = false;
				if ($this->data['isOptimized'] && $package['Package']['isFlexPackage'] == 1) {
					$flexCheck = $this->SchedulingMaster->query('SELECT pricePointId FROM pricePoint WHERE pricePointId IN (' . implode(',', $ppid_arr) . ') AND (pricePerExtraNight IS NULL OR pricePerExtraNight < 1 OR pricePerExtraNightDNG IS NULL OR pricePerExtraNightDNG < 1)'); 
				}
				
				if ($flexCheck) {
					$this->Session->setFlash(__('Please ensure flex night prices are set for selected Price Points.', true), 'default', array(), 'error');

				} else {

					// date check for ticket 1852 fix below
					$loaDateCheckClient = $package['ClientLoaPackageRel'][0];
					$loaDateCheckLoa = $this->Loa->findByloaId($loaDateCheckClient['loaId']);

					foreach($ppid_arr as $key=>$ppid){
						$this->data['SchedulingMaster']['pricePointId'] = $ppid;

						// get validity start/end
						$pricePointObj = new PricePoint();
						$pricePointsValidities = $pricePointObj->getPricePointStartEnd($ppid);

						$this->SchedulingMaster->create();
						$package = $this->SchedulingMaster->Package->findByPackageId($this->data['SchedulingMaster']['packageId']);
						$this->data['SchedulingMaster']['validityStartDate'] = $pricePointsValidities['startDate'];
						$this->data['SchedulingMaster']['validityEndDate'] = $pricePointsValidities['endDate'];
						$this->data['SchedulingMaster']['retailValue'] = $package['Package']['approvedRetailPrice'];
						$this->data['SchedulingMaster']['siteId'] = $package['Package']['siteId'];

						// startDate
						$datePickerDate = explode('-', $this->data['SchedulingMaster']['startDatePicker']);
						$this->data['SchedulingMaster']['startDate'] = "$datePickerDate[2]-$datePickerDate[0]-$datePickerDate[1] " . $this->data['SchedulingMaster']['startDateTime'];

						// endDate
						// ticket 1852 - end date is master end date minus 7 days when there are multiple price points
						if (sizeof($ppid_arr) > 1) {
							$endDateFromValidity = date('Y-m-d', strtotime($this->data['SchedulingMaster']['validityEndDate'] . ' -7 days'));
							if (in_array($this->data['buyNowOfferTypeId'], array(3,4,8))) {
								$this->data['SchedulingMaster']['endDate'] = "$endDateFromValidity 01:00:00";
							} else {
								$this->data['SchedulingMaster']['endDate'] = "$endDateFromValidity 16:00:00";
							}

							// end date can not be greater than loa end date
							if (strtotime($this->data['SchedulingMaster']['endDate']) > strtotime($loaDateCheckLoa['Loa']['endDate'])) {
								$this->data['SchedulingMaster']['endDate'] = $loaDateCheckLoa['Loa']['endDate'];
							}

						// end date prior to ticket 1852
						} else {
							$datePickerDate2 = explode('-', $this->data['SchedulingMaster']['endDatePicker2']);
							if (in_array($this->data['buyNowOfferTypeId'], array(3,4,8))) {
								$this->data['SchedulingMaster']['endDate'] = "$datePickerDate2[2]-$datePickerDate2[0]-$datePickerDate2[1] 01:00:00";
							} else {
								$this->data['SchedulingMaster']['endDate'] = "$datePickerDate2[2]-$datePickerDate2[0]-$datePickerDate2[1] 16:00:00";
							}
						}

						//if this is a mystery auction we override some fields
						if ($this->data['MerchandisingFlag']['MerchandisingFlag'] == 3) {
							$this->data['SchedulingMaster']['openingBid']   = $this->data['Mystery']['openingBid'];
							$this->data['SchedulingMaster']['bidIncrement'] = $this->data['Mystery']['bidIncrement'];
							$this->data['SchedulingMaster']['packageName']  = $this->data['Mystery']['packageName'];
							$this->data['SchedulingMaster']['subtitle']     = $this->data['Mystery']['subtitle'];
							$this->data['SchedulingMaster']['shortBlurb']   = $this->data['Mystery']['shortBlurb'];
						}

						// price point fields
						$pricePointObj->recursive = -1;
						if ($pp = $pricePointObj->find('first', array('conditions' => array('PricePoint.pricePointId' => $ppid), 'fields' => array('retailValue', 'percentRetailAuc', 'percentRetailBuyNow')))) {
							$this->data['SchedulingMaster']['pricePointRetailValue'] = $pp['PricePoint']['retailValue'];
						}

						// create an optimized
						if ($this->data['isOptimized']) {
							$this->data['SchedulingMaster']['offerTypeId'] = $this->data['buyNowOfferTypeId'];
							$success = $this->addSave($this->data);
						} else {
							// create an auction
							if ($this->data['isAuction']) {
								$this->data['SchedulingMaster']['offerTypeId'] = 1;
								if ($pp) {
									$this->data['SchedulingMaster']['pricePointPercentRetailAuc'] = $pp['PricePoint']['percentRetailAuc'];
								}

								// jwoods - workaround so that discounts are not set for auctions
								$tempIsDiscountedOffer = $this->data['SchedulingMaster']['isDiscountedOffer'];
								$tempPercentDiscount = $this->data['SchedulingMaster']['percentDiscount'];
								$this->data['SchedulingMaster']['isDiscountedOffer'] = 0;
								$this->data['SchedulingMaster']['percentDiscount'] = '';

								$success = $this->addSave($this->data);

								// second part of discount workaround - reset discount values
								$this->data['SchedulingMaster']['isDiscountedOffer'] = $tempIsDiscountedOffer;
								$this->data['SchedulingMaster']['percentDiscount'] = $tempPercentDiscount;

							}

							// create a buy now
							if ($this->data['isBuyNow']) {
								$this->data['SchedulingMaster']['offerTypeId'] = $this->data['buyNowOfferTypeId'];
								if ($pp) {
									if (isset($this->data['SchedulingMaster']['pricePointPercentRetailAuc'])) {
										unset($this->data['SchedulingMaster']['pricePointPercentRetailAuc']);
									}
									$this->data['SchedulingMaster']['pricePointPercentRetailBuyNow'] = $pp['PricePoint']['percentRetailBuyNow'];
								}
								$success = $this->addSave($this->data);
							}
						}

						// create a hotel offer
						if ($this->data['isHotelOffer']) {
							$success = $this->addSave($this->data);
						}

						if ($success) {
							$this->Session->setFlash(__('The Schedule has been saved', true), 'default', array(), 'success');
							$this->set('closeModalbox', true);
						}

						// for save failure
						$this->set('data', $this->data);

					}
				}
			}
		}

		$packageEndDate = explode('-', $package['Package']['endDate']);
		$day=(isset($packageEndDate[2]))?$packageEndDate[2]:0;
		$month=(isset($packageEndDate[1]))?$packageEndDate[1]:0;
		$year=(isset($packageEndDate[0]))?$packageEndDate[0]:0;

		$packageEndDate = array('year' => $packageEndDate[0], 'month' => $month, 'day' => $day);
		$this->set('packageEndDate', $packageEndDate);

		$formatIds=array();
		foreach ($package['Format'] as $format):
			$formatIds[] = $format['formatId'];
		endforeach;

		if (count($package['ClientLoaPackageRel']) > 1) {
            $this->set('singleClientPackage', false);
            foreach ($package['ClientLoaPackageRel'] as &$packageClient) {
                $packageClient['clientName'] = $this->SchedulingMaster->Package->ClientLoaPackageRel->Client->field('name', array('Client.clientId' => $packageClient['clientId']));
                $packageClient['trackIds'] = $this->SchedulingMaster->Package->ClientLoaPackageRel->Loa->Track->find('list', array('conditions' => array('loaId' => $packageClient['loaId'])));
                if ($packageClient['clientId'] == $clientId) {
                    $masterClient = $packageClient;
                }
            }
		} else {
            $this->set('singleClientPackage', true);
            $masterClient = $package['ClientLoaPackageRel'][0];
            $conditions = array('Track.loaId = ' . $package['ClientLoaPackageRel'][0]['loaId']);
            //filter out 'Barter - Set Number of Packages' track type for flex packs
            if ($package['Package']['isFlexPackage']) {
                $conditions[] = 'Track.expirationCriteriaId NOT IN (4)';
            }
            $trackIds = $this->SchedulingMaster->Package->ClientLoaPackageRel->Loa->Track->find('list', array('conditions' => $conditions));
            $this->set('trackIds', $trackIds);
		}

		//if package is not approved, do not allow scheduling
		if ($package['Package']['packageStatusId'] != 4) {
			echo '<h3>This package cannot be scheduled because it is not approved.</h3>';
			die();
		}

		// price points
		$pricePointObj = new PricePoint();
		$pricePoints = $pricePointObj->getPricePoint($packageId);
		$pricePointsValidities = $pricePointObj->getPricePointValidities($packageId);
		$this->set('pricePoints', $pricePoints);

		//get loa
		$loa = $this->Loa->findByloaId($masterClient['loaId']);
		$this->set('loa', $loa);
		
		// get default endDate from loa
		$loaEndDate = date("m-d-Y", strtotime($loa['Loa']['endDate']));		

		// get pricePoint endDates (7 days prior) if earlier than loa endDate
		$pricePointDefaultEndDates = array();
		foreach ($pricePointsValidities as $pricePointValidity) {
			$pricePointEndDate = strtotime("-7 days", strtotime($pricePointValidity['LoaItemDate']['endDate']));
			if ($pricePointEndDate < strtotime($loa['Loa']['endDate'])) {
				$pricePointDefaultEndDates[$pricePointValidity['PricePoint']['pricePointId']]['endDate'] =  date("m-d-Y", $pricePointEndDate);
			} else {
				$pricePointDefaultEndDates[$pricePointValidity['PricePoint']['pricePointId']]['endDate'] = $loaEndDate;
			}
		}
		$this->set('pricePointDefaultEndDates', $pricePointDefaultEndDates);
		
        // defaults
		if (empty($this->data) && isset($this->params['named']['date'])) {
			$date = explode('-', $this->params['named']['date']);
			// default startDate
			$this->data['SchedulingMaster']['startDatePicker'] = date("m-d-Y", strtotime($this->params['named']['date']));
			$this->data['SchedulingMaster']['startDateTime'] = date("H:00:00", strtotime("+2 hour"));
			$this->data['SchedulingMaster']['endDatePicker2'] = $loaEndDate;

			// default numDaysToRun
			$this->data['SchedulingMaster']['numDaysToRun'] = 2;

			// default iteration
			$this->data['SchedulingMaster']['iterationSchedulingOption'] = 1;

			$this->data['SchedulingMaster']['packageName']       = trim($package['Package']['packageName']);
			$this->data['SchedulingMaster']['mysteryIncludes']   = trim($package['Package']['packageIncludes']);
			$this->data['SchedulingMaster']['retailValue']       = $package['Package']['approvedRetailPrice'];
		}
		
        //set to true to hide price points if hotel offer
        if (empty($package['Package']['externalOfferUrl'])) {
            $this->set('isHotelOffer', false);
        }else{
            $pricePoint = $this->SchedulingMaster->Package->PricePoint->getHotelOfferPricePoint($packageId);
            if ($pricePoint) {
                $this->set('pricePointId', $pricePoint[0]['PricePoint']['pricePointId']);
                $this->set('isHotelOffer', true);
            }
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

    function addSave($data) {
        $this->data = $data;
    	if ($this->SchedulingMaster->saveAll($this->data)) {
    		$this->createInstances();

    		$numInstances = $this->SchedulingMaster->query("SELECT COUNT(*) as numInstances FROM schedulingInstance WHERE schedulingMasterId = ".$this->SchedulingMaster->id);

    		// if no instances were created, delete this scheduling master
    		if ($numInstances[0][0]['numInstances'] == 0) {
    			$this->SchedulingMaster->delete($this->SchedulingMaster->id);
    			$this->Session->setFlash(__('The Schedule could not be saved. The number of iterations could not fit within the Loa End Date', true), 'default', array(), 'error');
    		} else if ($this->RequestHandler->isAjax()) {
    			return true;
    		}
    	} else {
    		$this->Session->setFlash(__('The Schedule could not be saved. Please correct the errors below.<br />'.implode('<br />', $this->SchedulingMaster->validationErrors), true), 'default', array(), 'error');
    	}

        return false;
    }

	function setOfferTypeDefaultAndDropdown($packageId, $formatIds, $schedulingMaster = false) {

	    /* Get all Offer Types available for this package based on Format */
		$this->SchedulingMaster->Package->Format->Behaviors->attach('Containable');
		$formats = $this->SchedulingMaster->Package->Format->find('all', array('conditions' => array('formatId' => $formatIds), 'contain' => array('OfferType')));

		$offerTypeIds=array();
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
		if (!isset($firstOfferId))$firstOfferId=0;

		$offerTypeId = (isset($this->data['SchedulingMaster']['offerTypeId'])) ? $this->data['SchedulingMaster']['offerTypeId'] : $firstOfferId;
		$this->SchedulingMaster->Package->PackageOfferTypeDefField->recursive = -1;
		if ($schedulingMaster) {
			
			$defaults['PackageOfferTypeDefField'] = $schedulingMaster['SchedulingMaster'];
			$defaults['PackageOfferTypeDefField']['buyNowPrice']=$schedulingMaster['SchedulingMaster']['buyNowPrice'];
			$defaults['PackageOfferTypeDefField']['openingBid'] = $schedulingMaster['SchedulingMaster']['openingBid'];

		} else {

			$arr=array(
				'conditions' => array(
					'PackageOfferTypeDefField.packageId' => $packageId, 
					'PackageOfferTypeDefField.offerTypeId' => $offerTypeId
				),
			);

		  $defaults = $this->SchedulingMaster->Package->PackageOfferTypeDefField->find('first', $arr);

		}

		$defaults['PackageOfferTypeDefField']['numWinners'] = $this->Ticket->getNumAuctionWinners($packageId);

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

			case 8:
				$this->set('defaultFile', 'offer_type_defaults_8');
			    break;

			case 7:
				$this->set('defaultFile', 'offer_type_defaults_3');
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

		$packageId = $this->data['SchedulingMaster']['packageId'];

		$rows = $this->SchedulingMaster->query("SELECT MIN(Loa.endDate) as minEndDate FROM loa AS Loa INNER JOIN clientLoaPackageRel USING(loaId) WHERE packageId = $packageId");

		$loaEndDate = $rows[0][0]['minEndDate'];

		$instanceData['SchedulingInstance']['schedulingMasterId'] 	= $masterData['SchedulingMaster']['schedulingMasterId'];
		$instanceData['SchedulingInstance']['startDate'] 			= $masterData['SchedulingMaster']['startDate'];

		/*
		 * For fixed price offers, we only grab the start and end dates. Don't care about number of iterations, or delays.
		 */
		if (in_array($masterData['SchedulingMaster']['offerTypeId'], array(3,4,7,8))) {
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

        if (!empty($this->data['SchedulingMaster']['endDatePicker'])) {
            //we have to format the date and time for the DB... fun stuff
            $endDate = explode('-', $this->data['SchedulingMaster']['endDatePicker']);

			$this->data['SchedulingMaster']['firstIterationEndDate']['month']     =   $endDate[0];
			$this->data['SchedulingMaster']['firstIterationEndDate']['day']   =   $endDate[1];
			$this->data['SchedulingMaster']['firstIterationEndDate']['year']    =   $endDate[2];

			$this->data['SchedulingMaster']['firstIterationEndDate'] = $this->data['SchedulingMaster']['firstIterationEndDate']['year'].'-'.
			                                                            $this->data['SchedulingMaster']['firstIterationEndDate']['month'].'-'.
			                                                            $this->data['SchedulingMaster']['firstIterationEndDate']['day'].' ';
//			$this->data['SchedulingMaster']['firstIterationEndDateTime'] = $this->data['SchedulingMaster']['firstIterationEndDateTime']['hour'].':'.
//			        $this->data['SchedulingMaster']['firstIterationEndDateTime']['min'].':00 '.
//			        $this->data['SchedulingMaster']['firstIterationEndDateTime']['meridian'];

			$dateTime = $this->data['SchedulingMaster']['firstIterationEndDate'].' '.$this->data['SchedulingMaster']['firstIterationEndDateTime'];
			$firstIterationDateTime = date('Y-m-d H:i:s', strtotime($dateTime));

			$firstIterationEndFixed = true;
        }

		for ($i = 0; $i < $iterations; $i++) {

		    //for the first iteration, if an end date is picked, we use that date/time and bypass all holiday checks
		    //this only applies for the first iteration of a master
		    if (true === $firstIterationEndFixed && 0 == $i):
		        $endDate = strtotime($firstIterationDateTime);

		        $instanceData['SchedulingInstance']['endDate'] = $firstIterationDateTime;
		    else:
			    $endDate = strtotime($instanceData['SchedulingInstance']['startDate'] . ' +' . $masterData['SchedulingMaster']['numDaysToRun'] . ' days');

			    /*while ($this->_isHoliday($endDate)) {
			        $endDate = strtotime('+1 day', $endDate);
			    }*/

			    if (($endDate > $masterEndDate && $iterationSchedulingOption == 1) || $endDate > strtotime($loaEndDate)) {
			        break;
			    }

			    $instanceData['SchedulingInstance']['endDate'] = date('Y-m-d H:i:s', $endDate);

			endif;

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
			if (date('G', $startDate) >= 18) {
			    $startDate = strtotime('+1 day', $startDate);   //push to next day
			    $startDate = date('Y-m-d', $startDate);         //conver to just Y-m-d
			    $startDate = $startDate.' 08:00:00';            //set time to 8am
			    $startDate = strtotime($startDate);             //save new date as timestamp
			}

			$instanceData['SchedulingInstance']['startDate'] = date('Y-m-d H:i:s', $startDate);
		}

		// cleanup master data for iterations and endDate
		$instance_stats = $this->SchedulingMaster->query("SELECT COUNT(*) AS totalInstances, MAX(endDate) AS lastEndDate FROM schedulingInstance WHERE schedulingMasterId = " . $masterData['SchedulingMaster']['schedulingMasterId']);
		$new_master['SchedulingMaster']['schedulingMasterId'] = $masterData['SchedulingMaster']['schedulingMasterId'];
		$new_master['SchedulingMaster']['iterations'] = $instance_stats[0][0]['totalInstances'];
		$new_master['SchedulingMaster']['endDate'] = $instance_stats[0][0]['lastEndDate'];
		$this->SchedulingMaster->save($new_master, false);

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
		return false;	//not honoring holidays anymore
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
			foreach ($originalData['SchedulingInstance'] as $k => $instance){
				if (strtotime($instance['startDate']) > time()) {
					$remainingIterations++;
				}
			}

			/* If no iterations have gone live yet, we can do whatever we want to this */
			if ($remainingIterations == count($originalData['SchedulingInstance'])) {

				$arr=array('SchedulingInstance.schedulingMasterId' => $id);
				$this->SchedulingMaster->SchedulingInstance->deleteAll($arr);

				//if this is a mystery auction we override some fields
				if (in_array(3, $this->data['MerchandisingFlag']['MerchandisingFlag'])) {
					$this->data['SchedulingMaster']['openingBid']   = $this->data['Mystery']['openingBid'];
					$this->data['SchedulingMaster']['bidIncrement'] = $this->data['Mystery']['bidIncrement'];
					$this->data['SchedulingMaster']['packageName']  = $this->data['Mystery']['packageName'];
					$this->data['SchedulingMaster']['subtitle']     = $this->data['Mystery']['subtitle'];
					$this->data['SchedulingMaster']['shortBlurb']   = $this->data['Mystery']['shortBlurb'];
				}

				// startDate
	      $datePickerDate = explode('-', $this->data['SchedulingMaster']['startDatePicker']);
	      $this->data['SchedulingMaster']['startDate'] = "$datePickerDate[2]-$datePickerDate[0]-$datePickerDate[1] " . $this->data['SchedulingMaster']['startDateTime'];

	      // endDate
				$datePickerDate2 = explode('-', $this->data['SchedulingMaster']['endDatePicker2']);
	      $this->data['SchedulingMaster']['endDate'] = "$datePickerDate2[2]-$datePickerDate2[0]-$datePickerDate2[1] 16:00:00";

    		if ($this->SchedulingMaster->save($this->data)) {

    			$this->createInstances();
    			if ($this->RequestHandler->isAjax()) {
    				$this->Session->setFlash(__('The Schedule has been saved', true), 'default', array(), 'success');
    				$this->set('closeModalbox', true);
    			}

    		} else {

					$this->Session->setFlash(__('The Schedule could not be saved. Please correct the errors below.', true), 'default', array(), 'error');

    		}

			// save after offer is live: for fixedprices and hotel offers
 			} elseif (in_array($originalData['SchedulingMaster']['offerTypeId'], array(3, 4, 7, 8))) {

				 // endDate
				$datePickerDate2 = explode('-', $this->data['SchedulingMaster']['endDatePicker2']);
	            $this->data['SchedulingMaster']['endDate'] = "$datePickerDate2[2]-$datePickerDate2[0]-$datePickerDate2[1] 01:00:00";

				// set endDate for instance and offerLive
			    $offerLiveResults = $this->OfferLuxuryLink->query("SELECT OfferLuxuryLink.* FROM offerLuxuryLink as OfferLuxuryLink INNER JOIN  offer AS Offer USING(offerId) WHERE Offer.schedulingInstanceId = " . $originalData['SchedulingInstance'][0]['schedulingInstanceId']);
			    $schedulingInstance['SchedulingInstance']   			= $originalData['SchedulingInstance'][0];
			    $schedulingInstance['SchedulingInstance']['endDate']    = $this->data['SchedulingMaster']['endDate'];
				$offerLive['OfferLuxuryLink']									= $offerLiveResults[0]['OfferLuxuryLink'];
			    $offerLive['OfferLuxuryLink']['endDate']                      = $this->data['SchedulingMaster']['endDate'];

			    if (count($offerLiveResults)) {
			    	$this->OfferLuxuryLink->save($offerLive, false);
			    }

            	if ($this->SchedulingMaster->save($this->data, false) && $this->SchedulingMaster->SchedulingInstance->save($schedulingInstance, false)) {
	            	if ($this->RequestHandler->isAjax()) {
		                $this->Session->setFlash(__('The Schedule has been saved', true), 'default', array(), 'success');
		        		$this->set('closeModalbox', true);
	        		}
	            } else {
	                $this->Session->setFlash(__('The Schedule could not be saved. Please correct the errors below.', true), 'default', array(), 'error');
	            }

			// save after offer is live: for auctions
    		} else {

		    	// delete future instances
				$this->SchedulingMaster->SchedulingInstance->deleteAll(array('SchedulingInstance.startDate > NOW() + INTERVAL 1 HOUR', 'SchedulingInstance.schedulingMasterId' => $id));

		  		// endDate
				$datePickerDate2 = explode('-', $this->data['SchedulingMaster']['endDatePicker2']);
	            $this->data['SchedulingMaster']['endDate'] = "$datePickerDate2[2]-$datePickerDate2[0]-$datePickerDate2[1] 16:00:00";

    			if ($this->SchedulingMaster->save($this->data, false)) {

					// get necessary data for re-doing instance creation
					$instance_count = $this->SchedulingMaster->query("SELECT COUNT(*) AS totalInstances FROM schedulingInstance WHERE schedulingMasterId = $id");
					$instance_count = $instance_count[0][0]['totalInstances'];
					$scheduling_master = $originalData;
					$scheduling_master['SchedulingMaster']['startDate'] = date("Y-m-d 08:00:00", strtotime('+ 1 day'));
					$scheduling_master['SchedulingMaster']['endDate'] = $this->data['SchedulingMaster']['endDate'];
					$scheduling_master['SchedulingMaster']['iterations'] = $this->data['SchedulingMaster']['iterations'] - $instance_count;
					$scheduling_master['SchedulingMaster']['iterationSchedulingOption'] = $this->data['SchedulingMaster']['iterationSchedulingOption'];

    				$this->createInstances($scheduling_master);

    				if ($this->RequestHandler->isAjax()) {
    					$this->Session->setFlash(__('The Schedule has been saved', true), 'default', array(), 'success');
    					$this->set('closeModalbox', true);
    				}
    			} else {
    				$this->Session->setFlash(__('The Schedule could not be saved. Please correct the errors below.', true), 'default', array(), 'error');
    			}

    		}

		}else if (empty($this->data)) {

			$this->data = $this->SchedulingMaster->read(null, $id);

			$searchStartDate = $this->data['SchedulingMaster']['startDate'];
			$searchEndDate = $this->data['SchedulingMaster']['endDate'];

			$date       = explode(' ', $this->data['SchedulingMaster']['startDate']);
			$date2       = explode(' ', $this->data['SchedulingMaster']['endDate']);
			$datePicker = explode('-', $date[0]);
			$datePicker2 = explode('-', $date2[0]);

			$this->data['SchedulingMaster']['startDatePicker']     =   date('m-d-Y', strtotime($date[0]));
			$this->data['SchedulingMaster']['startDateTime']     =   date('H:i:s', strtotime($date[1]));
			$this->data['SchedulingMaster']['endDatePicker2']     =   date('m-d-Y', strtotime($date2[0]));

		}

		/* Check if there are any iterations left. If there are none, then we can't edit this */
		$this->SchedulingMaster->SchedulingInstance->recursive = 2;
		$remainingIterations = 0;
		$old_offer_id = false;
		foreach ($this->data['SchedulingInstance'] as $k => $instance){
			$this->data['SchedulingInstance'][$k]['offerId'] = $this->SchedulingMaster->getOfferIdFromInstance($instance['schedulingInstanceId']);
			if (!$old_offer_id && $this->data['SchedulingInstance'][$k]['offerId']) {
				$old_offer_id = $this->data['SchedulingInstance'][$k]['offerId'];
			}
		  if (strtotime($instance['startDate']) > time()) {
				$remainingIterations++;
      }
		}

		$this->set('old_offer_id', $old_offer_id);

		$merchandisingFlags = $this->SchedulingMaster->MerchandisingFlag->find('list');
		$schedulingStatusIds = $this->SchedulingMaster->SchedulingStatus->find('list');
		$schedulingDelayCtrlIds = $this->SchedulingMaster->SchedulingDelayCtrl->find('list');
		$remittanceTypeIds = $this->SchedulingMaster->RemittanceType->find('list');

		$packageId 				= $this->data['SchedulingMaster']['packageId'];
		$package 				= $this->SchedulingMaster->Package->findByPackageId($packageId);
		$package['packageIsMystery']=$this->SchedulingMaster->isMystery($packageId);

		foreach ($package['Format'] as $format){
			$formatIds[] = $format['formatId'];
		}

		$this->setOfferTypeDefaultAndDropdown($packageId, $formatIds, $this->data);

		//the state of the master is 0 if it hasn't gone live yet, or 1 if at least one iteration has gone live
		if (strtotime($this->data['SchedulingMaster']['startDate']) >= time() && $this->data['SchedulingMaster']['offerTypeId'] != 8) {
		    $masterState = 0;
		} else {
		    $masterState = 1;
		}

		if (count($package['ClientLoaPackageRel']) > 1) {
		    $this->set('singleClientPackage', false);
		} else {
		    $this->set('singleClientPackage', true);

		    $trackIds = $this->SchedulingMaster->Package->ClientLoaPackageRel->Loa->Track->find('list', array('conditions' => array('loaId' => $package['ClientLoaPackageRel'][0]['loaId'])));
		    $this->set('trackIds', $trackIds);
		}

		//set to true to hide price points if hotel offer
		if (empty($package['Package']['externalOfferUrl'])) {
			$isHotelOffer = false;
			$this->set('isHotelOffer', false);
			// price points
			$pricePoint = new PricePoint();
			$this->set('pricePoints', $pricePoint->getPricePoint($packageId));
		}else {
			$isHotelOffer = true;
			$pricePoint = $this->SchedulingMaster->Package->PricePoint->getHotelOfferPricePoint($packageId);
			if ($pricePoint) {
				$this->set('pricePointId', $pricePoint[0]['PricePoint']['pricePointId']);
				$this->set('isHotelOffer', true);
			}
		}

    if (isset($this->passedArgs['instanceId'])) {       //is auction or hotel offer
      $this->SchedulingMaster->SchedulingInstance->recursive = -1;
      if ($instance = $this->SchedulingMaster->SchedulingInstance->findBySchedulingInstanceId($this->passedArgs['instanceId'])) {
        if ($instanceOfferId=$this->SchedulingMaster->getOfferIdFromInstance($this->passedArgs['instanceId'])) {
							if (strtotime($instance['SchedulingInstance']['startDate']) > time()) {
									$offerId = $this->data['PricePoint']['pricePointId'];
									$previewType = 'pricepoint';
							}
							else {
									$offerId = $instanceOfferId;
									$previewType = 'old_offer';
							}
						}
						else {
								$offerId = $this->data['PricePoint']['pricePointId'];
								$previewType = 'pricepoint';
						}
					}
        }
        else {          //is buy now
            if ($offerId = $this->SchedulingMaster->SchedulingInstance->Offer->findOfferBySchedulingMasterId($id)) {
                $previewType = 'old_offer';
            }
            else {
                $offerId = $this->data['PricePoint']['pricePointId'];
                $previewType = 'pricepoint';
            }
        }

        if (isset($_SERVER['ENV']) && $_SERVER['ENV'] == 'development') {
            $subdomain = 'dev-';
            if ($this->data['SchedulingMaster']['siteId'] == 1) {
                $subdomain .= 'luxurylink';
            }
            elseif ($this->data['SchedulingMaster']['siteId'] == 2) {
                $subdomain .= 'familygetaway';
            }
        }
        elseif (isset($_SERVER['ENV']) && $_SERVER['ENV'] == 'staging') {
            $subdomain = 'stage-';
            if ($this->data['SchedulingMaster']['siteId'] == 1) {
                $subdomain .= 'luxurylink';
            }
            elseif ($this->data['SchedulingMaster']['siteId'] == 2) {
                $subdomain .= 'family';
            }
        }
        else {
            $subdomain = 'www';
        }

        switch($this->data['SchedulingMaster']['siteId']) {
            case 2:
                if (isset($_SERVER['ENV']) && in_array($_SERVER['ENV'], array('development', 'staging'))) {
                    $previewUrl = $subdomain.'.luxurylink.com';
                }
                else {
                    $previewUrl = $subdomain.'.familygetaway.com';
                }
                break;
            case 1:
            default:
                $previewUrl = $subdomain.'.luxurylink.com';
        }

				// get the number of bids against an auction packageId regardless if successful 
				$numBids=$this->Bid->getBidStatsForPackageId($packageId);

				// get the number of buynow requests against buynow packageId regardless if successful 
				$numRequests=$this->Ticket->getNumBuyNowRequests($packageId);

				$this->set("numBids", $numBids);
				$this->set("numRequests", $numRequests);
        $this->set('offerId',           $offerId);
        $this->set('previewType',       $previewType);
				$this->set('masterState',				$masterState);
				$this->set('package', 					$package);
				$this->set('packageId', 				$packageId);
				$this->set('remainingIterations',       $remainingIterations);
				$this->set('merchandisingFlags',        $merchandisingFlags);
				$this->set('schedulingStatusIds',       $schedulingStatusIds);
				$this->set('schedulingDelayCtrlIds',    $schedulingDelayCtrlIds);
        $this->set('schedulingInstance',        $instance);
        $this->set('previewUrl',                $previewUrl);

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
		// NOTE:  there is a variation of this function inside web_service_tickets_controller to automatically stop offers if it hits the number of max num sales
	    if (!$id) {
			$this->Session->setFlash(__('Invalid id for SchedulingMaster', true));
			$this->set('closeModalbox', true);
			die();
		}
		$this->data = $this->SchedulingMaster->read(null, $id);
		if($this->data['SchedulingMaster']['offerTypeId'] != 3 && $this->data['SchedulingMaster']['offerTypeId'] != 4 && $this->data['SchedulingMaster']['offerTypeId'] != 7 && $this->data['SchedulingMaster']['offerTypeId'] != 8) {
		    $this->Session->setFlash(__('Cannot stop a non-fixed priced offer', true), 'default', array(), 'error');
			$this->set('closeModalbox', true);
		    die();
		}

		if(strtotime($this->data['SchedulingMaster']['startDate']) < time()) {
            switch($this->data['Package']['siteId']) {
                case 2:             //family
                    $offerSiteField = 'OfferFamily';
                    $offerSiteTable = 'offerFamily';
                    break;
                case 1:             //luxurylink
                default:
                    $offerSiteField = 'OfferLuxuryLink';
                    $offerSiteTable = 'offerLuxuryLink';
            }

		    $newEndDate = date('Y-m-d H:i:s');

            $schedulingInstanceId = $this->data['SchedulingInstance'][0]['schedulingInstanceId'];
		    $offerLiveResults = $this->$offerSiteField->query("SELECT {$offerSiteField}.* FROM {$offerSiteTable} as {$offerSiteField} INNER JOIN  offer AS Offer USING(offerId) WHERE Offer.schedulingInstanceId = ".$schedulingInstanceId);

            $schedulingMaster['SchedulingMaster']       = $this->data['SchedulingMaster'];
		    $schedulingInstance['SchedulingInstance']   = $this->data['SchedulingInstance'][0];
		    $offerLive[$offerSiteField]                     = $offerLiveResults[0][$offerSiteField];

		    $schedulingMaster['SchedulingMaster']['endDate']        = $newEndDate;
		    $schedulingInstance['SchedulingInstance']['endDate']    = $newEndDate;
		    $offerLive[$offerSiteField]['endDate']                      = $newEndDate;

            if ($this->SchedulingMaster->save($schedulingMaster, false) &&
                $this->SchedulingMaster->SchedulingInstance->save($schedulingInstance, false) &&
                $this->$offerSiteField->save($offerLive, false) )
            {

                $this->Session->setFlash(__('The offer has been stopped', true), 'default', array(), 'success');
        		$this->set('closeModalbox', true);

            } else { // will issue this error if there's no offerLive

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

		$package = $this->SchedulingMaster->Package->findByPackageId($packageId);
		$defaults['PackageOfferTypeDefField']['retailValue'] = $package['Package']['approvedRetailPrice'];
		$this->set(compact('defaults'));

		switch ($offerTypeId):
			case 1:
			case 2:
			case 6:
				$this->render('offer_type_defaults_1');
			break;
			case 3:
			case 4:
			case 8:
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
