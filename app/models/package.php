<?php
class Package extends AppModel {

	var $name = 'Package';
	var $useTable = 'package';
	var $primaryKey = 'packageId';
	
	var $belongsTo = array('Currency' => array('foreignKey' => 'currencyId'),
						   'PackageStatus' => array('foreignKey' => 'packageStatusId')
						);
	
	var $hasOne = array('PackagePerformance' => array('foreignKey' => 'packageId'),
                        'PackageAgeRange' => array('foreignKey' => 'packageId'));
	
	var $hasMany = array('PackageBlackout' => array('foreignKey' => 'packageId'),
						 'PackageBlackoutWeekday' => array('foreignKey' => 'packageId'),
						 'PackageOfferTypeDefField' => array('foreignKey' => 'packageId'),
						 'PackageLoaItemRel' => array('foreignKey' => 'packageId'),
						 'ClientLoaPackageRel' => array('foreignKey' => 'packageId', 'dependent' => true),
						 'SchedulingMaster' => array('foreignKey' => 'packageId'),
						 'ClientTracking' => array('foreignKey' => 'packageId'),
						 'LoaItemRatePackageRel' => array('foreignKey' => 'packageId'),
                         'PricePoint' => array('foreignKey' => 'packageId')                                                  
						);
						
	var $validate = array('packageName' => VALID_NOT_EMPTY,
						'numConcurrentOffers' => array('rule' => 'numeric', 'message' => 'Number of concurrent offers must be a number'),
						'maxNumSales' => array('rule' => 'numeric', 'message' => 'Maximum number of sales must be a number', 'allowEmpty' => true),
						'numGuests' => array('rule' => 'numeric', 'message' => 'Number of guests must be a number'),
						'minGuests' => array('minGuestsRule1' => array('rule' => 'numeric', 'message' => 'Minimum number of guests must be a number'),
											 'minGuestsRule2' => array('rule' => 'validateGuests', 'message' => 'Minimum # of guests must be less than or equal to the maximum # of guests')
											),
						'maxAdults' => array('maxAdultsRule1' => array('rule' => 'numeric', 'message' => 'Maximum number of adults must be a number'),
											 'maxAdultsRule2' => array('rule' => 'validateGuests', 'message' => 'Maximum # of adults must be less than or equal to the maximum # of guests')
											 ),
						'numNights' => array('numeric' => array('rule' => 'numeric', 'message' => 'Number of nights must be a number'),
						                    'validateNumNightsAddsUp' => array('rule' => 'validateNumNightsAddsUp', 'message' => 'Must match with the number of nights entered for each room item below.')),
						'endDate' => array('rule' => array('validateDateRanges'), 'message' => 'End Date must be greater than Start Date'),
						'validityEndDate' => array('rule' => array('validateDateRanges'), 'message' => 'End Date must be greater than Start Date'));
		
	var $hasAndBelongsToMany = array(
								'Format' => 
									array('className' => 'Format',
										  'joinTable' => 'packageFormatRel',
										  'foreignKey' => 'packageId',
										  'associationForeignKey' => 'formatId'
									),
								'PackagePromo' =>
								    array('joinTable' => 'packagePromoRel',
								          'foreignKey' => 'packageId',
								          'associationForeignKey' => 'packagePromoId'),
								'FamilyAmenity' => 
									array('className' => 'FamilyAmenity',
										  'foreignKey' => 'packageId',
										  'joinTable' => 'packageFamilyAmenityRel',
										  'associationForeignKey' => 'familyAmenityId'
								   )
								);
    var $actsAs = array('Logable');
    
	function validateGuests($data) {
	  $numGuests = $this->data['Package']['numGuests'];
	  $guestsValue = array_values($data);
	  if ($numGuests < $guestsValue[0]) {
		 return false;
	  }
	  return true;
	}
	
	function validateDateRanges($data) {
		$packageStartDate = $this->data['Package']['startDate'];
		$packageEndDate = $this->data['Package']['endDate'];
		
		$validityStartDate = $this->data['Package']['validityStartDate'];
		$validityEndDate = $this->data['Package']['validityEndDate'];
		
		if(isset($data['validityEndDate']) && $validityStartDate >= $validityEndDate)	return false;
		if(isset($data['endDate']) && $packageStartDate >= $packageEndDate)	return false;

		return true;
	}

	function validateNumNightsAddsUp($data) {
	    $numNights = 0;
	    
	    if(isset($this->data['PackageLoaItemRel']) && is_array($this->data['PackageLoaItemRel'])) {
            foreach ($this->data['PackageLoaItemRel'] as $item) {
                
                //If type Pre-packaged is included, skip validation and just return true
                if (in_array($item['loaItemTypeId'], array(12,20))) {
                    return true;
                }
                
                //If type room night is included, keep a running count of the room nights
                if ($item['loaItemTypeId'] == 1) {
                    $numNights += $item['quantity'];
                }
            }
            
            if ($numNights == $data['numNights']) {
                return true;
            }
            else {
                return false;
            }
        }
	    return true;
	}

	function cloneData($data)
	{
		// LEGACY PRE-PKGR -- no use
		$data['Package']['copiedFromPackageId'] = $data['Package']['packageId'];
	    $data['Package']['packageStatusId'] = 1;
		foreach ($data['ClientLoaPackageRel'] as &$packageRel):
			unset($packageRel['clientLoaPackageRelId']);
		endforeach;
	
		unset($data['Package']['packageId']);
		
		return $data;
	}
    
	function beforeSave($created) {
        //get all descriptions for the inclusions and populate a text area field to store all of this on the database.

		/* 
		ALEE PKGR - NO NEED FOR THIS AUG 13 -2010

        if (isset($this->data['Package']['repopulateInclusions'])) {
            //only do this if the package loa item rel array is there because we need the weights
            if (isset($this->data['PackageLoaItemRel'])):
                if (!empty($this->data['Package']['CheckedLoaItems'])) {
                    $itemDescriptions = $this->query("SELECT LoaItemGroup.*, LoaItem.loaItemId, LoaItem.merchandisingDescription FROM loaItem AS LoaItem 
                                                      LEFT JOIN loaItemGroup AS LoaItemGroup USING (loaItemId) 
                                                      WHERE LoaItem.loaItemId IN(".implode(',', $this->data['Package']['CheckedLoaItems']).") GROUP BY LoaItem.loaItemId");
                }
                foreach ($itemDescriptions as $v) {
                    $itemId = $v['LoaItem']['loaItemId'];
                    $weight = $this->data['PackageLoaItemRel'][$itemId]['weight'];
                    if (!isset($descriptions[$weight])) {
                        $descriptions[$weight] = $v['LoaItem']['merchandisingDescription'];
                    } else {
                        $descriptions[] = $v['LoaItem']['merchandisingDescription'];
                    }
                    
                    if ($v['LoaItemGroup']['loaItemGroupId']) {
                        $groupDescriptions = $this->query("SELECT LoaItem.loaItemId, LoaItem.merchandisingDescription FROM loaItemGroup AS LoaItemGroup 
                                                           INNER JOIN loaItem AS LoaItem ON LoaItemGroup.groupItemId = LoaItem.loaItemId WHERE LoaItemGroup.loaItemId = $itemId");
                        $gdCount = 1;
                        foreach ($groupDescriptions as $gd) {
                            if ($weight) {
                                $descriptions["$weight.$gdCount"] = $gd['LoaItem']['merchandisingDescription'];
                                $gdCount++;
                            } else {
                                $descriptions[] = $gd['LoaItem']['merchandisingDescription'];
                            }
                        }
                    } 
                }
                
                //sort the array by the weights so the implode works
                ksort($descriptions);
                $this->data['Package']['packageIncludes'] = implode("\r\n", $descriptions);
            endif;
        }
	    */

		//dynamically set the client approved date
	    if ($created != true) {
    	    $orig = $this->find('Package.packageId = '.$this->data['Package']['packageId'], array('packageStatusId'));

    	    if (@$orig['Package']['packageStatusId'] != 3 && $this->data['Package']['packageStatusId'] == 3) {
    	        $this->data['Package']['dateClientApproved'] = date('Y-m-d H:i:s');
    	    }
	    }

	   //set packageStatusId to Setup if new package
	   if (!isset($this->data['Package']['packageStatusId']) && empty($this->data['Package']['packageId'])) {
		$this->data['Package']['packageStatusId'] = 1;
	   }
       
       $sites = (empty($this->data['Package']['sites']) && !empty($this->data['Package']['packageId'])) ? $this->field('sites', array('Package.packageId' => $this->data['Package']['packageId'])) : $this->data['Package']['sites'];

       $this->data['Package']['sites'] = (is_array($sites)) ? implode(',', $sites) : $sites;
       
	   return true;
	}
	
	function afterSave($created) {
		 $packageId = (empty($this->data['Package']['packageId'])) ? $this->getLastInsertID() : $this->data['Package']['packageId'];
	  
	  	/* 
		DISABLED BY ALEE SINCE PKGR [AUG 12]
		===========================================================================
		// get validity disclaimer by joining it with validityLeadInLine
		$validity_disclaimer = (isset($this->data['Package']['validityLeadInLine'])) ? '<p><strong>' . Sanitize::escape($this->data['Package']['validityLeadInLine']) . '</strong></p>' : ' ';
		$validity_disclaimer .= Sanitize::escape(isset($this->data['Package']['validityDisclaimer']));
		*/

	   //delete from packageAgeRange if this package isn't associated with Family
	   if (isset($this->data['Package']['siteId']) && !in_array($this->data['Package']['siteId'], array(2)) ||
           (isset($this->data['Package']['sites']) && !stristr('family', $this->data['Package']['sites']))) {
            $age_ranges = $this->PackageAgeRange->findByPackageId($packageId);
            if (!empty($age_ranges)) {
               $this->PackageAgeRange->deleteAll(array('PackageAgeRange.packageId' => $packageId), false);
            }
	   }

        $sites = (isset($this->data['Package']['sites'])) ? explode(',', $this->data['Package']['sites']) : array($this->data['Package']['siteId']);
        $updateFields = array('validityStart' => 'validityStartDate',
                                  'validityEnd' => 'validityEndDate',
                                  'numGuests' => 'numGuests',
                                  'minGuests' => 'minGuests',
                                  'maxAdults' => 'maxAdults');
        $setFields = array();

        foreach ($updateFields as $column => $dataField) {
            if (!empty($this->data['Package'][$dataField])) {
                $setFields[] = "{$column} = '{$this->data['Package'][$dataField]}'";
            }
        }

		/*
		DISABLED BY ALEE SINCE PKGR [AUG 12]
		===========================================================================
        if (!empty($validity_disclaimer)) {
            $setFields[] = "validityDisclaimer = '{$validity_disclaimer}'";
        }
		*/

        foreach ($sites as $site) {
            switch ($site) {
               case 1:		//Luxury Link
               case 'luxurylink':
                  $table = 'offerLuxuryLink';
                  break;
               case 2:		//Family
               case 'family':
                  $table = 'offerFamily';
                  break;
               default:		//default to Luxury Link
                  $table = 'offerLuxuryLink';
            }
            
            if (!empty($setFields)) {
                $query = "UPDATE {$table}
                          SET " . implode(', ', $setFields) .
                          "WHERE packageId = {$this->id} AND isAuction = 0 AND now() < endDate";
                $this->query($query);
            }
        }
	    
	    // update offer details in offer for hotel offers type (7)
	    if (!empty($this->data['Package']['externalOfferUrl'])) {
	    	$package_title = Sanitize::escape($this->data['Package']['packageTitle']);
	    	$short_blurb = Sanitize::escape($this->data['Package']['shortBlurb']);
	    	$additionalDescription = Sanitize::escape($this->data['Package']['additionalDescription']);
	    	$package_includes = Sanitize::escape($this->data['Package']['packageIncludes']);
	    	
			$this->query("
			    UPDATE {$table}
			    SET validityStart = '{$this->data['Package']['validityStartDate']}',
				    validityEnd = '{$this->data['Package']['validityEndDate']}',
				    offerName = '$package_title',
					shortBlurb = '$short_blurb',
					additionalDescription = '$additionalDescription',
				    offerIncludes = '$package_includes',
					externalOfferUrl = '{$this->data['Package']['externalOfferUrl']}' 
			    WHERE packageId = $this->id AND offerTypeId = 7 AND now() < endDate
		    ");
	    }
	}
    
    /**
     * Package revamp functions
     **/
    
    function getPackage($packageId) {
        $query = "SELECT * FROM package Package
                    INNER JOIN clientLoaPackageRel ClientLoaPackageRel USING (packageId)
                    LEFT JOIN packageAgeRange PackageAgeRange USING (packageId)
                    INNER JOIN packageStatus PackageStatus USING (packageStatusId)
                    INNER JOIN loa Loa ON ClientLoaPackageRel.loaId = Loa.loaId
                    INNER JOIN currency Currency ON Package.currencyId  = Currency.currencyId
                    WHERE Package.packageId = {$packageId}";
        if ($package = $this->query($query)) {
            $package[0]['Package']['sites'] = explode(',', $package[0]['Package']['sites']);
            $package[0]['Loa']['sites'] = explode(',', $package[0]['Loa']['sites']);
            return $package[0];
        }
        else {
            return false;
        }
    }
    
    function getHistory($packageId) {
        $query = "SELECT History.description, History.action, History.samaccountname, History.change, History.created
                  FROM logs History
                  WHERE model ='Package' AND model_id = {$packageId}
                  ORDER BY History.id DESC, History.created DESC";
        $historyDesc = array();
        if ($history = $this->query($query)) {
            foreach($history as $update) {
                $historyDate = date('M j Y g:i A', strtotime($update['History']['created']));
                switch ($update['History']['action']) {
                    case 'add':
                        $historyStr = "{$historyDate} - {$update['History']['samaccountname']} -- Created";
                        break;
                    case 'edit':
                        if (preg_match('/packageStatusId \([0-9]*\) => \(([0-9]+)\)/', $update['History']['change'], $matches)) {
                            $statusId = end($matches);
                            if ($statusId) {
                                $statusName = $this->PackageStatus->field('packageStatusName', array('packageStatusId' => $statusId));
                                $updateStr = "STATUS: {$statusName}";
                            }
                        }
                        else {
                            $updateStr = 'Updated';
                        }
                        $historyStr = "{$historyDate} - {$update['History']['samaccountname']} -- {$updateStr}";
                        break;
                    default:
                        $historyStr = '';
                        break;
                }
                $historyDesc[] = $historyStr;
            }
        }
        return $historyDesc;
    }
    
    function getRatePeriods($packageId) {
        $query = "SELECT * FROM loaItemRatePeriod LoaItemRatePeriod
                  INNER JOIN loaItemRate LoaItemRate USING (loaItemRatePeriodId)
                  INNER JOIN loaItemRatePackageRel LoaItemRatePackageRel USING (loaItemRateId) 
                  WHERE LoaItemRatePackageRel.packageId = {$packageId}
                  GROUP BY LoaItemRatePeriod.loaItemRatePeriodId
        ";
        $ratePeriods = $this->query($query);
        if ($ratePeriods = $this->query($query)) {
            return $ratePeriods;
        }
        else {
            return false;
        }
    }
    
    function getRoomRate($loaItemRatePeriodId, $packageId) {
        $loaItemRates = $this->query("
            SELECT * FROM loaItemRate LoaItemRate
            INNER JOIN loaItemRatePackageRel LoaItemRatePackageRel USING(loaItemRateId)
            INNER JOIN loaItemRatePeriod LoaItemRatePeriod USING(loaItemRatePeriodId)
            INNER JOIN loaItem LoaItem USING (loaItemId)
            WHERE loaItemRatePeriodId = $loaItemRatePeriodId AND packageId = $packageId
        ");
        
        $total = 0;
        foreach ($loaItemRates as $loaItemRate) {
            $taxes = $this->getTaxes($loaItemRate['LoaItemRatePeriod']['loaItemId']);
            if ($loaItemRate['LoaItem']['loaItemTypeId'] == 12) {
                $price = $loaItemRate['LoaItemRate']['price'];
                $total += $price + ($price * $taxes['percent'] / 100) + ($taxes['fixed'] * $loaItemRate['LoaItemRatePackageRel']['numNights']);
            }
            else {
                $price = $loaItemRate['LoaItemRate']['price'] * $loaItemRate['LoaItemRatePackageRel']['numNights'];
                $total += $price + ($price * $taxes['percent'] / 100) + ($taxes['fixed'] * $loaItemRate['LoaItemRatePackageRel']['numNights']);
            }
        }
        return $total;
    }
    
    function getLoaItemDates($loaItemRatePeriodId) {
        return $this->query("SELECT * FROM loaItemDate LoaItemDate WHERE loaItemRatePeriodId = {$loaItemRatePeriodId}");
    }
    
    function deleteDate($loaItemDateId) {
        $query = "DELETE FROM loaItemDate WHERE loaItemDateId = {$loaItemDateId}";
        return $this->query($query);
    }

	
	//  =====================================================================
	//  PKGR - START OF VALIDITY / BLACKOUT METHODS 
	//  =====================================================================

	function updatePackagePricePointValidity($packageId) {
		$this->updateValidityDisclaimer($packageId); 
		$this->recursive = -1;
		$package = $this->read(null, $packageId);
		$rp = $this->getPricePointDateRangeByPackage($packageId);
		foreach ($rp as $r) {
			$validityDisclaimer = $this->getValidityDisclaimerText($packageId, $r[0]['minStartDate'], $r[0]['maxEndDate'], $r[0]['loaItemRatePeriodIds']);
			if ($validityDisclaimer) {
				$r['PricePoint']['validityStart'] = $r[0]['minStartDate'];
				$r['PricePoint']['validityEnd'] = $r[0]['maxEndDate'];
				$r['PricePoint']['validityDisclaimer'] = Sanitize::escape($validityDisclaimer);
				$this->PricePoint->save($r['PricePoint']);
			}
		}
		return;
	}

	function getValidityDisclaimerText($packageId, $startDate, $endDate, $loaItemRatePeriodIds) {

		// RETURNS : (STRING) html of validity disclaimer for a given price point range
		// pulls data from toolbox.packageValidityDisclaimer

		$dates = $this->getPackageValidityDisclaimerByItem($packageId, $loaItemRatePeriodIds, $startDate, $endDate);
		$blackout_week = $this->getBlackoutWeekday($packageId);
		if ($blackout_week) {
			switch ($blackout_week) {
				case 'Fri,Sat,Sun': 
					$valid = "Valid for Travel Monday through Thursday";
					break;
				case 'Fri,Sat':
					$valid = "Valid for Travel Sunday through Thursday";
					break;
				case 'Sat':
					$valid = "Valid for Travel Sunday through Friday";
					break;
				case 'Mon,Tue,Wed,Thu':
					$valid = "Valid for Travel Friday through Sunday";
					break;
				default:
					$valid = '';
					$add_to_blackout = true;
					break;
			}
			$html = "<b>This package is valid for travel $valid:</b><br><br>";
		} else {
			$html = "<b>This package is valid for travel:</b><br><br>";
		}
		
		// populate html with valid ranges
		if (!empty($dates['ValidRanges'])) {
			foreach ($dates['ValidRanges'] as $r) {
				$pvd_ts_start = strtotime($r['pvd']['startDate']);
				$pvd_ts_end = strtotime($r['pvd']['endDate']);
				if ($this->isSameMonth($pvd_ts_start, $pvd_ts_end)) {
					$html.= date('F', $pvd_ts_start) . ' ' . date('j', $pvd_ts_start) . '-' . date('j', $pvd_ts_end) . ', ' . date('Y', $pvd_ts_start)  .'<br>';
				} else {
					$html.= date('F j, Y', $pvd_ts_start) . ' - ' . date('F j, Y', $pvd_ts_end) . "<br>";
				}
			}
		}

		// populate html with blackout ranges
		if (!empty($dates['BlackoutDays'])) {
			$html.= "Reservations are subject to availability at time of booking.<br><br>";
			$html.= "<b>Blackout dates:</b><br><br>";
			foreach ($dates['BlackoutDays'] as $r) {
				$pvd_ts_start = strtotime($r['pvd']['startDate']);
				$pvd_ts_end = strtotime($r['pvd']['endDate']);
				if ($this->isSameMonth($pvd_ts_start, $pvd_ts_end)) {
					$html.= date('F', $pvd_ts_start) . ' ' . date('j', $pvd_ts_start) . '-' . date('j', $pvd_ts_end) . ', ' . date('Y', $pvd_ts_start)  .'<br>';
				} else {
					$html.= date('F j, Y', $pvd_ts_start) . ' - ' . date('F j, Y', $pvd_ts_end) . "<br>";
				}
			}
			if (isset($add_to_blackout)) {
				$html.= $this->pluralize($blackout_week) . "<br>";
			}
			$html.= 'May not be valid during holidays and special event periods.';
		} else {
			$html.= 'Reservations are subject to availability at time of booking.  May not be valid during holidays and special event periods.';
		}
			
		return $html;
	}

	function pluralize($day) {
		$days_plural = array('Mon' => 'Mondays',
							 'Tue' => 'Tuesdays',
							 'Wed' => 'Wednesdays',
							 'Thu' => 'Thurdays',
							 'Fri' => 'Fridays', 
							 'Sat' => 'Saturdays', 
							 'Sun' => 'Sundays'
						 );
		return (isset($days_plural[$day])) ? $days_plural[$day] : false;

	}

	function isSameMonth($date1_ts, $date2_ts) {
		if (!$date1_ts || !$date2_ts) {
			return false;
		}
		if (date('F', $date1_ts) === date('F', $date2_ts)) {
			return true;
		} else {
			return false;
		}
	}

	function getBlackoutWeekday($packageId) {
		$r = $this->query("SELECT weekday FROM packageBlackoutWeekday WHERE packageId = {$packageId}");
		return $r[0]['packageBlackoutWeekday']['weekday'];
	}

	function getBlackout($packageId) {
		$r = $this->query("SELECT startDate,endDate FROM packageBlackout WHERE packageId = {$packageId}");
		return $r;
	}

	function saveBlackouts($packageId, $data) {
		$this->query("DELETE FROM packageBlackout WHERE packageId = {$packageId}");
		$this->query("DELETE FROM packageBlackoutWeekday WHERE packageId = {$packageId}");
		if ($weekdays = implode(',', $data['PackageBlackoutWeekday'])) {
			$this->query("INSERT INTO packageBlackoutWeekday SET packageId = {$packageId}, weekday = '{$weekdays}'");
		}
		if (!empty($data['PackageBlackout'])) {
			foreach ($data['PackageBlackout'] as $k=>$pb) {
				if ($pb['delete'] == 1) {
					continue;
				}
				$start = strtotime($pb['startDate']);
				$end = strtotime($pb['endDate']);
				if ($start && $end) {
					$startDate = date('Y-m-d', $start);
					$endDate = date('Y-m-d', $end);
					$this->query("INSERT INTO packageBlackout SET packageId = {$packageId}, created = NOW(), startDate = '{$startDate}', endDate = '{$endDate}'");
				}
			}
		}
	}

	function getPkgVbDates($packageId) {
		// used in package summary page
		$r = $this->query("SELECT * FROM packageValidityDisclaimer pvd WHERE packageId = {$packageId} ORDER BY startDate");
		$data = array();
		foreach ($r as $m => $arr) {
			if ($arr['pvd']['startDate'] == $arr['pvd']['endDate']) {
				$date = date('F j, Y', strtotime($arr['pvd']['startDate']));
			} else {
				$date = date('F j, Y', strtotime($arr['pvd']['startDate'])) . ' - ' . date('F j, Y', strtotime($arr['pvd']['endDate']));
			}
			if ($arr['pvd']['isBlackout'] == 1) {
				$data['BlackoutDays'][] = $date;
			} else {
				$data['ValidRanges'][] = $date;
			}
		}
		return $data;
	}
	
	function getPackageValidityDisclaimerByItem($packageId, $loaItemRatePeriodIds, $startDate, $endDate) {
		
		//$r = $this->query("SELECT startDate,endDate,isBlackout FROM packageValidityDisclaimer pvd WHERE packageId = {$packageId} AND startDate >= '{$startDate}' AND endDate <= '{$endDate}' ORDER BY startDate");
		$r = $this->query("SELECT pvd.startDate, pvd.endDate, pvd.isBlackout FROM loaItemDate date 
								INNER JOIN packageValidityDisclaimer pvd ON pvd.packageId = {$packageId} AND date.startDate <= pvd.startDate AND pvd.endDate <= date.endDate 
								WHERE date.loaItemRatePeriodId IN ($loaItemRatePeriodIds) ORDER BY pvd.startDate");
		$data = array();
		foreach ($r as $m => $arr) {
			if ($arr['pvd']['isBlackout'] == 1) {
				$data['BlackoutDays'][] = $arr;
			} else {
				$data['ValidRanges'][] = $arr;
			}
		}
		return $data;
	}

	function getPricePointDateRange($packageId, $loaItemRatePeriodIds) {
		if (!$packageId || !$loaItemRatePeriodIds) {
			return false;
		}
		$r = $this->query("SELECT MIN(startDate) AS minStart, MAX(endDate) AS maxEnd FROM loaItemDate where loaItemRatePeriodId IN ({$loaItemRatePeriodIds})");
		if (!empty($r)) {
			return array('minStartDate' => $r[0][0]['minStart'], 'maxEndDate' => $r[0][0]['maxEnd']);
		} else {
			return false;
		}
	}

	function getPricePointDateRangeByPackage($packageId) {
		$r = $this->query("SELECT PricePoint.pricePointId, MIN(item.startDate) AS minStartDate, MAX(item.endDate) AS maxEndDate, CONCAT_WS(',', loaItemRatePeriodId) AS loaItemRatePeriodIds 
							FROM pricePoint PricePoint INNER JOIN pricePointRatePeriodRel pr USING (pricePointId) INNER JOIN loaItemDate item USING (loaItemRatePeriodId) 
							WHERE PricePoint.packageId = {$packageId} GROUP BY pricePointId;");
		return $r;
	}

	function updateValidityDisclaimer($packageId) {
		
		// * this method is called whenever a user defined blackout or room night date range is changed
		// * this clears and updates the table packageValidityDisclaimer
		// * does not auto populate price point validity disclaimer

		$validRanges = array();  // pairs of start/end valid date ranges
		$blackoutRanges = array(); // pairs of start/end blackout date ranges
		$blackoutDates = array(); // pairs of start/end blackout dates (not part of official ranges but for display only)
	
		// "blackout range" is just a NON-VALID period ***

		// [ VALID DATE RANGES ]
		// ==============================================================================
		$rp = $this->query("SELECT dr.startDate, dr.endDate FROM packageLoaItemRel 
								INNER JOIN loaItem USING (loaItemId) 
								INNER JOIN loaItemRatePeriod USING (loaItemId) 
								INNER JOIN loaItemDate dr USING (loaItemRatePeriodId) 
							WHERE packageId = {$packageId}");
		
		foreach ($rp as $r) {
			$validRanges[] = array('s' => $r['dr']['startDate'], 'e' => $r['dr']['endDate']);
		}
		unset($rp);
	
		// [ BLACKOUT DATE RANGES ]
		// ==============================================================================
		$bo = $this->query("SELECT startDate, endDate FROM packageBlackout WHERE packageId = {$packageId}");
		foreach ($bo as $b) {
			$validBlackoutRange = (strtotime($b['packageBlackout']['endDate']) - strtotime($b['packageBlackout']['startDate']) >= 1209600) ? true : false;
			if ($validBlackoutRange) {
				// if two weeks or more, then its considered a blackout RANGE and needs carving
				$blackoutRanges[] = array('s' => $b['packageBlackout']['startDate'], 'e' => $b['packageBlackout']['endDate']);
			} else {
				// other just use to display info as individual records for display
				$blackoutDates[] = array('s' => $b['packageBlackout']['startDate'], 'e' => $b['packageBlackout']['endDate']);
			}
		}
		unset($bo);

		// [ CALCULATE RANGES]
		// ==============================================================================
		$carvedDateRanges = $this->carveDateRanges($validRanges, $blackoutRanges);		
		$data = array();
		$data['BlackoutDays'] = $blackoutDates;

		// only separate data out into groups
		// primary goal is to carve the validity ranges based on non-valid periods (blackout ranges)
		foreach ($carvedDateRanges as $c) {
			if ($c['t'] == 'validity') {
				$data['ValidRanges'][] = $c;
			} 
		}
	
		// return user defined blackout day (or range) + validity ranges
		// do not care about black ranges ('NON-VALID period')
		$this->query("DELETE FROM packageValidityDisclaimer WHERE packageId ={$packageId}");
		foreach ($data['ValidRanges'] as $r) {
			$this->query("INSERT INTO packageValidityDisclaimer SET packageId = {$packageId}, startDate = '{$r['s']}', endDate = '{$r['e']}', isBlackout = 0, created = NOW() ON DUPLICATE KEY UPDATE modified = NOW()");
		}
		foreach ($data['BlackoutDays'] as $r) {
			$this->query("INSERT INTO packageValidityDisclaimer SET packageId = {$packageId}, startDate = '{$r['s']}', endDate = '{$r['e']}', isBlackout = 1, created = NOW() ON DUPLICATE KEY UPDATE modified = NOW()");
		}
	}

	function carveDateRanges($validDates, $blackoutDates) {

		// carve the date ranges for both validity and blackout.
		// build date boundaries and carve out new ranges

		$ranges = array();  // new carved date ranges
		
		// gather all days to use as guide boundaries
		$dates = array();
		foreach (array_merge($validDates, $blackoutDates) as $d) {
			$dates[] = $d['s'];
			$dates[] = $d['e'];
		}
		$dates = array_unique($dates);
		sort($dates);  // sort reorders indexes and sorts by value
		
		$count = count($dates);
		foreach ($dates as $k => $d) {
			$next_index = $k + 1;
			$prev_index = $k - 1;
			if ($next_index >= $count) {
				break;
			}
			$range1 = $d;
			$range2 = $dates[$next_index];
			foreach ($validDates as $vd) {
				if ($range1 >= $vd['s'] && $range2 <= $vd['e']) {
					// label validity or blackout for use in other functions
					$type = 'validity';
					foreach ($blackoutDates as $xd) {
						if ($range1 >= $xd['s'] && $range2 <= $xd['e']) {
							$type = 'blackout';
						}
					}
					$ranges[] = array('s' => $range1, 'e' => $range2, 't' => $type);
				}
			}
		}
		return $this->cleanCarvedDateRanges($ranges);
	}

	function cleanCarvedDateRanges($ranges) {

		// reorganize dates for better structure
		// i.e. collapse two ranges that are one
		// i.e. if blackout or validity, make sure new date ranges are made

		$count = count($ranges);
		for ($i = 0 ; $i < $count; $i++) {
			$prev_index = $i - 1;
			if ($ranges[$i]['s'] == $ranges[$prev_index]['e']) {
				if ($ranges[$i]['t'] == $ranges[$prev_index]['t']) {
					// two date ranges are actually one  -- so combine them and remove old range
					$ranges[$i] = array('s' => $ranges[$prev_index]['s'], 'e' => $ranges[$i]['e'], 't' => $ranges[$i]['t']);
					unset($ranges[$prev_index]);
				} elseif ($ranges[$i]['t'] == 'validity') {
					$ranges[$i]['s'] = date('Y-m-d', strtotime($ranges[$i]['s'] . ' +1 day'));
				} elseif ($ranges[$i]['t'] == 'blackout') {
					$ranges[$prev_index]['e'] = date('Y-m-d', strtotime($ranges[$prev_index]['e'] . ' -1 day'));
				}
			}
		}
		return $ranges;
	}

	//  =====================================================================
	//  END OF VALIDITY / BLACKOUT METHODS 
	//  =====================================================================
    

	function getLoaItems($packageId) {
        return $this->query("
            SELECT * FROM packageLoaItemRel PackageLoaItemRel
            INNER JOIN loaItem LoaItem USING(loaItemId)
            WHERE packageId = $packageId;
        ");
    }
	
	function getInclusions($packageId) {
        return $this->query("
            SELECT LoaItem.merchandisingDescription,PackageLoaItemRel.packageLoaItemRelId FROM packageLoaItemRel PackageLoaItemRel
            INNER JOIN loaItem LoaItem USING(loaItemId) 
			WHERE packageId = $packageId AND loaItemTypeId NOT IN (1,12) ORDER BY weight;
        ");
    }

    function getCurrency($packageId) {
        $currency = $this->query("
            SELECT Currency.*, CurrencyExchangeRate.*
            FROM package
            INNER JOIN currency Currency USING(currencyId)
            LEFT JOIN currencyExchangeRate CurrencyExchangeRate USING(currencyId)
            WHERE packageId = $packageId
            ORDER BY asOfDateTime DESC
            LIMIT 1
        ");
        return $currency[0];
    }
    
    function getTaxes($loaItemId) {
        $fees = $this->query("SELECT * FROM fee Fee WHERE loaItemId = $loaItemId");
        if (count($fees)) {
            // add all percentages and fixed fees
            $percent = $fixed = 0;
            foreach ($fees as $key => $row) {
                $percent += ($row['Fee']['feeTypeId'] == 1) ? $row['Fee']['feePercent'] : 0;
                $fixed += ($row['Fee']['feeTypeId'] == 2) ? $row['Fee']['feePercent'] : 0;
            }
            return array('percent' => $percent, 'fixed' => $fixed);            
        } else {
            return array('percent' => 0, 'fixed' => 0);
        }
    }

	/* 
		====================================================
		PKGR :  clone package 
		====================================================
	*/

	function clonePackage($originalPkgId) {
		$origData = $this->read(null, $originalPkgId);
		
		// create new package row based on original package id
		$data = array();
		$data['Package'] = $origData['Package'];
		$data['Package']['packageStatusId'] = 1;
		$data['Package']['copiedFromPackageId'] = $originalPkgId;
		$data['Package']['created'] = $data['Package']['modified'] = date('Y-m-d H:i:s');
		$data['Package']['sites'] = explode(',', $data['Package']['sites']);
		unset($data['Package']['packageId']);
		unset($data['Package']['notes']);
		
		$this->create();
		$this->save($data);
		$newPkgId = $this->getLastInsertID();

		// no package id  -- could not create row
		if (!$newPkgId) {
			$errors = 'ERRORS:<BR /><BR />' . implode("\n\n<br /><br />", $this->validationErrors);
			return $errors;
		}

		// create new records for the following tables based on original
		$tables = array('packageLoaItemRel', 'loaItemRatePackageRel', 'packageBlackout', 'packageBlackoutWeekday', 'clientLoaPackageRel');
		foreach ($tables as $table) {
			$model = ucfirst($table);
			if (empty($origData[$model])) {
				continue;
			}
			foreach ($origData[$model] as &$modelData) {
				$modelData['packageId'] = $newPkgId;
				unset($modelData[($table . 'Id')]);
			}
			$this->{$model}->saveAll($origData[$model]);
		}
		
		$this->updatePackagePricePointValidity($newPkgId);
		
		return $newPkgId;
	}

}
?>
