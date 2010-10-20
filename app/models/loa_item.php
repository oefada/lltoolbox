<?php
class LoaItem extends AppModel {

	var $name = 'LoaItem';
	var $useTable = 'loaItem';
	var $primaryKey = 'loaItemId';
	var $displayField = 'itemName';
	var $actsAs = array('Logable');
	
	/*
	var $validate = array(
				'itemName' => array(
					'rule' => VALID_NOT_EMPTY,
					'message' => 'The Item Name cannot be empty.'
				)
			);
	*/
	
	var $belongsTo = array('LoaItemType' => array('foreignKey' => 'loaItemTypeId'),
							'Loa' => array('foreignKey' => "loaId"),
							'RoomGrade' => array('foreignKey' => 'roomGradeId'),
                            'Currency' => array('foreignKey' => 'currencyId')
							);

	//var $hasOne = array('PackageLoaItemRel' => array('foreignKey' => 'loaItemId', 'dependent' => true));

	var $hasMany = array('LoaItemRatePeriod' => array('foreignKey' => 'loaItemId', 'dependent'=> true),
						 'Fee' => array('foreignKey' => 'loaItemId', 'dependent' => true),
						 'LoaItemGroup' => array('foreignKey' => 'loaItemId', 'dependent' => true),
                         'PackageLoaItemRel' => array('foreignKey' => 'loaItemId')
						);
	
	var $loaItems;	//used as storage for carving rate periods, stores the item details for all the items being carved
	
	
	/**
	 * Method carves the rate periods and returns a very informative array with all of the rate periods,
	 * the item information, and all info needed to build a table with items and their rate periods.
	 *
	 * @see getRatePeriodBoundaries()
	 * @author Victor Garcia
	 * @param mixed $itemIds accepts either a single item id as an int, an array of integers, or just uses the current id
	 * @param array $quantities the quantities that are used to multiply the rate period or base price and get the full price
	 * @param string $startDate the left boundary for dates
	 * @param string $endDate the right boundary for dates
	 * @return array that contains the rate period boundaries for all items
	 */
	function carveRatePeriods($itemIds = array(), $quantities = array(), $startDate = null, $endDate = null) {
		if(empty($itemIds)):
			$itemIds = array($this->id);		//if no itemIds were passed, set the id to $this->id
		elseif(is_numeric($itemIds)):
			$itemIds = array($itemIds);			//if only an integer was passed, set the id to the integer
		elseif(!is_array($itemIds)):
			return array();						//if no valid data was passed, return an empty array
		endif;
		
		$ratePeriodBoundaries = $this->getRatePeriodBoundaries($itemIds, $startDate, $endDate);
		
		$itemList = $this->find('list', array('conditions' => array('loaItemId' => $itemIds)));
		//$currencyList = $this->find('list', array('fields' => array('currencyId'), 'conditions' => array('loaItemId' => $itemIds)));
		$carvedBoundaries = array();
		$periodPrices = array();
		$items = array();
		$i = 0;
		foreach ($ratePeriodBoundaries as $start => $end) :
			$rangeStart = strtotime($start);
			$rangeEnd = strtotime($end);
			$carvedBoundaries[$i] = array('rangeStart' =>  date('Y-m-d', $rangeStart), 'rangeEnd' => date('Y-m-d', $rangeEnd));
			foreach($itemIds as $itemId):
				if (!isset($periodPrices[$i])) {
				   $periodPrices[] = 0;
				}
				$items[$itemId]['itemName'] = $itemList[$itemId];
				//$items[$itemId]['currencyId'] = $currencyList[$itemId];			
				
				if (empty($this->loaItems[$itemId]['LoaItemRatePeriod'])):									//if item has no rate periods, we always use the item base price
					$ratePeriodPrice = $this->loaItems[$itemId]['LoaItem']['itemBasePrice'];
				else:																						//if item has atleast one rate period, loop through them and set the prices
					foreach($this->loaItems[$itemId]['LoaItemRatePeriod'] as $loaItemRatePeriod):
						foreach ($loaItemRatePeriod['LoaItemDate'] as $loaItemDate):

							if ($loaItemDate['startDate'] == $start && $loaItemDate['endDate'] == $end):
								$ratePeriodPrice = $loaItemRatePeriod['LoaItemRate'][0]['price'];
								break 2;
							else:
								$ratePeriodPrice = $this->loaItems[$itemId]['LoaItem']['itemBasePrice'];
							endif;

						endforeach;
					endforeach;
				endif; //end rate period test
					
				$data = array(
					'loaItemId' => $itemId,
					'startDate' => date('Y-m-d', $rangeStart),
					'endDate' => date('Y-m-d', $rangeEnd),
					'ratePeriodPrice' => $ratePeriodPrice,
					'quantity' => (isset($quantities[$itemId]) ? $quantities[$itemId]['quantity'] : 0),
					//'currencyId' => $items[$itemId]['currencyId']
				);
				
				$periodPrices[$i] += ($data['ratePeriodPrice'] * $data['quantity']);
				
				$items[$itemId]['feePercentDisplay'] = '';
				$items[$itemId]['ratePeriodPriceDisplay'] = ($data['ratePeriodPrice'] * $data['quantity']);

				if (!empty($this->loaItems[$itemId]['Fee'])) {
					foreach ($this->loaItems[$itemId]['Fee'] as $fee) {
						if ($fee['feeTypeId'] == 1) {
							$feeAdjust = ($data['ratePeriodPrice'] * ($fee['feePercent'] / 100));
							$periodPrices[$i] += ($feeAdjust * $data['quantity']);
							$items[$itemId]['ratePeriodPriceDisplay'] += ($feeAdjust * $data['quantity']);
							$items[$itemId]['feePercentDisplay'] .= '+ ' . $data['quantity'] . '@' . $fee['feePercent'] . '% ';
						}
						if ($fee['feeTypeId'] == 2) {
							$periodPrices[$i] += ($fee['feePercent'] * $data['quantity']);
							$items[$itemId]['ratePeriodPriceDisplay'] += ($fee['feePercent'] * $data['quantity']);
							$items[$itemId]['feePercentDisplay'] .= '+ ' . $data['quantity'] . '@' . $fee['feePercent'] . ' ';
						}
					}
				} 
				
				$data['feePercentDisplay'] = $items[$itemId]['feePercentDisplay'];
				$data['ratePeriodPriceDisplay'] = $items[$itemId]['ratePeriodPriceDisplay'];
				
				if (!isset($periodPrices[$i])) $periodPrices[$i] = 0;
				
				$packageRatePeriods['PackageRatePeriod'][] = $data;
				$items[$itemId]['PackageRatePeriod'][] = $data;
				
				if (!isset($items[$itemId]['OverallPrice'])) $items[$itemId]['OverallPrice'] = 0;
				$items[$itemId]['OverallPrice'] += $data['ratePeriodPrice'];
			endforeach;
			
			$carvedBoundaries[$i]['rangeSum'] = $periodPrices[$i];
			$i++;
		endforeach;
		
		$packageRatePeriods['Boundaries'] = $carvedBoundaries;
		$packageRatePeriods['IncludedItems'] = $items;

    	return $packageRatePeriods;

		// legacy
		/*
		for($i = 0; $i < count($ratePeriodBoundaries)-1; $i++):
			$rangeStart = strtotime($ratePeriodBoundaries[$i]);
			$rangeEnd = strtotime($ratePeriodBoundaries[($i + 1)])-$one_day;
			
			$carvedBoundaries[$i] = array('rangeStart' =>  date('Y-m-d', $rangeStart), 'rangeEnd' => date('Y-m-d', $rangeEnd));

			foreach($itemIds as $itemId):
				$items[$itemId]['itemName'] = $itemList[$itemId];
				$fee = $this->Fee->find('first', array('conditions' => array('Fee.loaItemId' => $itemId)));;
				$items[$itemId]['feePercent'] = $fee['Fee']['feePercent'];
				$items[$itemId]['currencyId'] = $currencyList[$itemId];
				
				if (empty($this->loaItems[$itemId]['LoaItemRatePeriod'])):									//if item has no rate periods, we always use the item base price
					$ratePeriodPrice = $this->loaItems[$itemId]['LoaItem']['itemBasePrice'];
				else:																						//if item has atleast one rate period, loop through them and set the prices
					foreach($this->loaItems[$itemId]['LoaItemRatePeriod'] as $loaItemRatePeriod):
						foreach ($loaItemRatePeriod['LoaItemDate'] as $loaItemDate):

							if( strtotime($loaItemDate['startDate']) <= $rangeStart && strtotime($loaItemDate['endDate']) >= $rangeEnd):
								$ratePeriodPrice = $loaItemRatePeriod['LoaItemRate'][0]['price'];
								break;
							else:
								$ratePeriodPrice = $this->loaItems[$itemId]['LoaItem']['itemBasePrice'];
							endif;

						endforeach;
					endforeach;
				endif; //end rate period test
				
				$data = array(
				'loaItemId' => $itemId,
				'startDate' => date('Y-m-d', $rangeStart),
				'endDate' => date('Y-m-d', $rangeEnd),
				'ratePeriodPrice' => $ratePeriodPrice,
				'quantity' => (isset($quantities[$itemId]) ? $quantities[$itemId]['quantity'] : 0),
				'feePercent' => $items[$itemId]['feePercent'],
				'currencyId' => $items[$itemId]['currencyId']
				);
				
				if (!isset($periodPrices[$i])) $periodPrices[$i] = 0;
				
				$periodPrices[$i] += $data['ratePeriodPrice']*$data['quantity']+$data['ratePeriodPrice']*$fee['Fee']['feePercent']/100;
				$packageRatePeriods['PackageRatePeriod'][] = $data;
				$items[$itemId]['PackageRatePeriod'][] = $data;
				
				if (!isset($items[$itemId]['OverallPrice'])) $items[$itemId]['OverallPrice'] = 0;
				$items[$itemId]['OverallPrice'] += $data['ratePeriodPrice'];
			endforeach;
			
			$carvedBoundaries[$i]['rangeSum'] = $periodPrices[$i];
		endfor;
		
		$packageRatePeriods['Boundaries'] = $carvedBoundaries;
		$packageRatePeriods['IncludedItems'] = $items;

		return $packageRatePeriods;
		*/
	}
	
	
	/**
	 * Method gets all of the rate period boundaries for an item or an array of items.
	 * If a start date or end date are entered, the dates are bounded by them
	 *
	 * @see carveRatePeriods()
	 * @author Victor Garcia
	 * @param array $itemIds the array of item ids to look up for the carving
	 * @param string $startDate the left boundary for dates
	 * @param string $endDate the right boundary for dates
	 * @return array that contains the rate period boundaries for all items
	 */
	function getRatePeriodBoundaries($itemIds = array(), $startDate = null, $endDate = null) {
		$endDate = strtotime('+1 day', strtotime($endDate));
		$endDate = date('Y-m-d', $endDate);

		$ratePeriodBoundaries = array();
		//loop through all of the items
		$this->recursive = 2;
		foreach($itemIds as $itemId):
			$this->id = $itemId;
			$this->read(null, $this->id);
			$this->loaItems[$this->id] = $this->data;

 			foreach($this->data['LoaItemRatePeriod'] as $loaItemRatePeriod):
				foreach($loaItemRatePeriod['LoaItemDate'] as $loaItemDate):

					$ratePeriodBoundaries[$loaItemDate['startDate']] = $loaItemDate['endDate'];

					/*
					if (!in_array($loaItemDate['startDate'], $ratePeriodBoundaries)) {
						$ratePeriodBoundaries[] = $loaItemDate['startDate'];
					}
					if (!in_array($loaItemDate['endDate'], $ratePeriodBoundaries)) {
						$ratePeriodBoundaries[] = $loaItemDate['endDate'];
					}
					*/
				/*
 			    $loaItemDate['endDate'] = date('Y-m-d', strtotime('+1 day', strtotime($loaItemDate['endDate'])));
 			    
				if(null != $startDate && strtotime($loaItemDate['startDate']) > strtotime($startDate) && strtotime($loaItemDate['startDate']) < strtotime($endDate)):
					$ratePeriodBoundaries[] = $loaItemDate['startDate'];
				endif;
			
				if(null != $endDate && strtotime($loaItemDate['endDate']) < strtotime($endDate) && strtotime($loaItemDate['endDate']) > strtotime($startDate)):
					$ratePeriodBoundaries[] = $loaItemDate['endDate'];
				endif;i
				*/

				endforeach;
			endforeach;

		endforeach;
		
		ksort($ratePeriodBoundaries);			//sort boundaries
		
		/*
		if($startDate !== null):				//append start date if entered
			array_unshift($ratePeriodBoundaries, $startDate);
		endif;
			
		if($endDate !== null):					//append end date if entered
			array_push($ratePeriodBoundaries, $endDate);
		endif;
		*/

		//$ratePeriodBoundaries = array_unique($ratePeriodBoundaries);
		//$ratePeriodBoundaries = array_merge($ratePeriodBoundaries, array());	//fix the fact that array_unique does not re-number keys

		return $ratePeriodBoundaries;
	}
    
    /**
     * Package revamp functions
     **/
    
    function getRoomTypesByPackage($packageId) {
        $query = "SELECT * FROM loaItem LoaItem
                  INNER JOIN packageLoaItemRel PackageLoaItemRel ON LoaItem.loaItemId = PackageLoaItemRel.loaItemId AND PackageLoaItemRel.packageId = {$packageId}
                  WHERE LoaItem.loaItemTypeId = 21";
        if ($group = $this->query($query)) {
            $query = "SELECT * FROM loaItemGroup LoaItemGroup
                      WHERE loaItemId = {$group[0]['LoaItem']['loaItemId']}";
            $rooms = array();
            if ($itemsInGroup = $this->query($query)) {
                foreach($itemsInGroup as $groupItem) {
                    $query = "SELECT * FROM loaItem LoaItem
                              WHERE LoaItem.loaItemId = {$groupItem['LoaItemGroup']['groupItemId']}";
                    if ($room = $this->query($query)) {
                        $rooms[] = $room[0];
                    }
                }
            }
            return $rooms;
        }
        else {
            $query = "SELECT * FROM loaItem LoaItem
                      INNER JOIN packageLoaItemRel PackageLoaItemRel ON LoaItem.loaItemId = PackageLoaItemRel.loaItemId AND PackageLoaItemRel.packageId = {$packageId}
                      WHERE LoaItem.loaItemTypeId IN (1, 12)";
            if ($room = $this->query($query)) {
                if ($room[0]['LoaItem']['loaItemTypeId'] == 12) {
                    $query = "SELECT LoaItem.itemName FROM loaItem LoaItem
                              INNER JOIN loaItemGroup LoaItemGroup ON LoaItemGroup.groupItemId = LoaItem.loaItemId
                              WHERE LoaItemGroup.loaItemId = {$room[0]['LoaItem']['loaItemId']} AND LoaItem.loaItemTypeId = 1";
                    if ($loaItem = $this->query($query)) {
                        $room[0]['LoaItem']['itemName'] = $loaItem[0]['LoaItem']['itemName'];
                        return $room;
                    }
                }
                else {
                    return $room;
                }
            }
        }
    }
    
    function getRoomTypesByLoa($packageId) {
        if ($loaId = $this->getLoaId($packageId)) {
            $query = "SELECT * FROM loaItem LoaItem
		      INNER JOIN loaItemType LoaItemType USING (loaItemTypeId)
                      LEFT JOIN packageLoaItemRel PackageLoaItemRel ON LoaItem.loaItemId = PackageLoaItemRel.loaItemId AND PackageLoaItemRel.packageId = {$packageId}
                      WHERE LoaItem.loaId = {$loaId} AND LoaItem.loaItemTypeId IN (1, 12, 21) AND TRIM(LoaItem.itemName) <> 'Migrated Item'
                      ORDER BY LoaItem.loaItemTypeId DESC";
            if ($loaItems = $this->query($query)) {
                $groupLoaItems = array();
                foreach($loaItems as $i => &$loaItem) {
                    if ($loaItem['LoaItem']['loaItemTypeId'] == 21) {
                        if (!empty($loaItem['PackageLoaItemRel']['packageLoaItemRelId'])) {
                            if ($groupItems = $this->query("SELECT * FROM loaItemGroup LoaItemGroup WHERE LoaItemGroup.loaItemId = {$loaItem['LoaItem']['loaItemId']}")) {
                                foreach ($groupItems as $item) {
                                    $groupLoaItems[$item['LoaItemGroup']['groupItemId']] = true;
                                }
                            }
                        }
                        unset($loaItems[$i]);
                    }
                    else {
                        if (!empty($loaItem['PackageLoaItemRel']['packageLoaItemRelId'])) {
                            $loaItem['LoaItem']['inPackage'] = 'true';
                        }
                        elseif (in_array($loaItem['LoaItem']['loaItemId'], array_keys($groupLoaItems))) {
                            if ($groupLoaItems[$loaItem['LoaItem']['loaItemId']] === true) {
                                $loaItem['LoaItem']['inPackage'] = 'true';
                            }
                        }
                    }
                }
            }
        }
        else {
            $loaItems = array();
        }
        return $loaItems;
    }
    
    function getPackageInclusions($packageId) {
        $query = "SELECT * FROM loaItem LoaItem
                  INNER JOIN packageLoaItemRel PackageLoaItemRel ON LoaItem.loaItemId = PackageLoaItemRel.loaItemId AND PackageLoaItemRel.packageId = {$packageId}
                  INNER JOIN loaItemType LoaItemType USING (loaItemTypeId)
                  WHERE LoaItem.loaItemTypeId NOT IN (1, 21)
                  ORDER BY PackageLoaItemRel.weight";
        $inclusions = $this->query($query);
        foreach ($inclusions as &$inclusion) {
            if (in_array($inclusion['LoaItem']['loaItemTypeId'], array(12,13,14))) {
                $query = "SELECT * FROM loaItemGroup LoaItemGroup
                          INNER JOIN loaItem LoaItem ON LoaItemGroup.groupItemId = LoaItem.loaItemId
                          INNER JOIN loaItemType LoaItemType USING (loaItemTypeId)
                          WHERE LoaItemGroup.loaItemId = {$inclusion['LoaItem']['loaItemId']}";
                if ($packagedLoaItems = $this->query($query)) {
                    foreach($packagedLoaItems as $i => $item) {
                        $inclusion['LoaItem']['PackagedItems'][$i] = $item;
                    }
                }
            }
            $query = "SELECT * FROM fee Fee
                      WHERE Fee.loaItemId = {$inclusion['LoaItem']['loaItemId']}";
            if ($fees = $this->query($query)) {
                foreach ($fees as $fee) {
                    $taxes = 0;
                    if ($fee['Fee']['feeTypeId'] == 1) {            //percentage
                        $taxes += ($fee['Fee']['feePercent']/100)*$inclusion['LoaItem']['itemBasePrice'];
                    }
                    elseif ($fee['Fee']['feeTypeId'] == 2) {         //dollar amount
                        $taxes += $fee['Fee']['feePercent'];
                    }
                }
                $inclusion['LoaItem']['totalPrice'] = ($inclusion['LoaItem']['loaItemTypeId'] == 12) ? 0 : $inclusion['LoaItem']['itemBasePrice'] + $taxes;
            }
            else {
                $inclusion['LoaItem']['totalPrice'] = $inclusion['LoaItem']['itemBasePrice'];
            }
        }
        return $inclusions;
    }
    
    function getAvailableInclusions($loaId, $packageId) {
        $query = "SELECT * FROM loaItem LoaItem
                  INNER JOIN loaItemType LoaItemType USING (loaItemTypeId)
                  WHERE LoaItem.loaItemTypeId NOT IN (1, 12, 21) AND
                        LoaItem.loaId = {$loaId} AND
                        LoaItem.loaItemId NOT IN (
                            SELECT loaItemId FROM loaItem
                            INNER JOIN packageLoaItemRel USING (loaItemId)
                            WHERE packageId = {$packageId} AND loaItemTypeId NOT IN (1, 12, 21)
                        )
                  ORDER BY LoaItem.merchandisingDescription";
        if ($inclusions = $this->query($query)) {
            foreach ($inclusions as &$inclusion) {
                if (in_array($inclusion['LoaItem']['loaItemTypeId'], array(13,14))) {
                    $query = "SELECT * FROM loaItemGroup LoaItemGroup
                              INNER JOIN loaItem LoaItem ON LoaItemGroup.groupItemId = LoaItem.loaItemId
                              INNER JOIN loaItemType LoaItemType USING (loaItemTypeId)
                              WHERE LoaItemGroup.loaItemId = {$inclusion['LoaItem']['loaItemId']}";
                    if ($packagedLoaItems = $this->query($query)) {
                        foreach($packagedLoaItems as $i => $item) {
                            $inclusion['LoaItem']['PackagedItems'][$i] = $item;
                        }
                    }
                }
                $query = "SELECT * FROM fee Fee
                          WHERE Fee.loaItemId = {$inclusion['LoaItem']['loaItemId']}";
                if ($fees = $this->query($query)) {
                    foreach ($fees as $fee) {
                        $taxes = 0;
                        if ($fee['Fee']['feeTypeId'] == 1) {            //percentage
                            $taxes += ($fee['Fee']['feePercent']/100)*$inclusion['LoaItem']['itemBasePrice'];
                        }
                        elseif ($fee['Fee']['feeTypeId'] == 2) {         //dollar amount
                            $taxes += $fee['Fee']['feePercent'];
                        }
                    }
                    $inclusion['LoaItem']['totalPrice'] = $inclusion['LoaItem']['itemBasePrice'] + $taxes;
                }
                else {
                    $inclusion['LoaItem']['totalPrice'] = $inclusion['LoaItem']['itemBasePrice'];
                }
            }
        }
        else {
            $inclusions = array();
        }
        return $inclusions;
    }
    
    function getLoaId($packageId) {
        $query = "SELECT loaId FROM clientLoaPackageRel ClientLoaPackageRel
                  WHERE packageId = {$packageId}";
        if ($loaId = $this->query($query)) {
            return $loaId[0]['ClientLoaPackageRel']['loaId'];
        }
    }
    
    function getLoaItem($loaItemId) {
        return $this->find('first', array('conditions' => array('LoaItem.loaItemId' => $loaItemId)));
    }
    

    function getRatePeriodId($loaItemId, $packageId, $origValidity) {
        if ($ratePeriods = $this->LoaItemRatePeriod->getRatePeriods($loaItemId, $packageId)) {
            foreach ($ratePeriods as $ratePeriod) {
                if ($ratePeriod['Validity'][0]['LoaItemDate']['startDate'] == $origValidity[0]['LoaItemDate']['startDate'] &&
                    $ratePeriod['Validity'][0]['LoaItemDate']['endDate'] == $origValidity[0]['LoaItemDate']['endDate']) {
                    return $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'];
                }
            }
        }
    }
    
    function getRoomNights($packageId) {
        if ($roomLoaItems = $this->getRoomTypesByPackage($packageId)) {
            $this->bindModel(array('hasOne' =>  array('Package')));
            $totalNights = $this->Package->field('numNights', array('packageId' => $packageId));
            $this->unbindModel(array('hasOne' =>  array('Package')));

            if ($rates = $this->LoaItemRatePeriod->getRatePeriods($roomLoaItems[0]['LoaItem']['loaItemId'], $packageId)) {
                foreach($rates as &$rate) {
                    foreach ($roomLoaItems as $i => &$loaItem) {
                        $ratePeriodId = ($i == 0) ? $rate['LoaItemRatePeriod']['loaItemRatePeriodId'] : $this->getRatePeriodId($loaItem['LoaItem']['loaItemId'], $packageId, $rate['Validity']);
                        if ($loaItemRates = $this->LoaItemRatePeriod->LoaItemRate->getRoomRates($loaItem['LoaItem']['loaItemId'], $packageId, $ratePeriodId)) {
                            $loaItem['LoaItemRate'] = $loaItemRates;
                        }
                    }
                    $thisRate = array();
                    $thisRate['Validity'] = $rate['Validity'];
                    unset($rate['Validity']);
                    foreach ($roomLoaItems as $room) {
                        $thisRate['LoaItems'][] = array_merge($rate, $room);
                    }
                    $fees = $this->Fee->getFeesForRoomType($roomLoaItems[0]['LoaItem']['loaItemId']);
                    if (!empty($fees)) {
                        $thisRate['Fees'] = $fees;
                    }
                    else {
                        $thisRate['Fees'] = array();
                    }
                    $thisRate['Totals']['totalAccommodations'] = $this->calcTotalAccommodations($roomLoaItems, $fees, $totalNights);
                    $ratePeriods[] = $thisRate;
                }
                
            }
            //debug($ratePeriods);
            //die();
            return $ratePeriods;
        }
    }
    
    function array_search_key($key, $array, $value) {
        foreach($array as $a) {
            if (isset($a[$key])) {
                if ($a[$key] === $value) {
                    return true;
                }
            }
            else {
                return $this->array_search_key($key, $a, $value);
            }
        }
    }
    
    function calcTotalAccommodations($rate, $fees, $totalNights) {
        $rates = array();
        $i = 0;
        foreach ($rate as $roomRate) {
            foreach ($roomRate['LoaItemRate'] as $price) {
                $taxes = 0;
                if (!empty($fees)) {
                    foreach ($fees as $fee) {
                        if ($fee['Fee']['feeTypeId'] == 1) {            //percentage
                            $taxes += ($fee['Fee']['feePercent']/100)*$price['LoaItemRate']['price'];
                        }
                        elseif ($fee['Fee']['feeTypeId'] == 2) {       //dollar amount
                            $resortFee = ($roomRate['LoaItem']['loaItemTypeId'] == 12) ? $fee['Fee']['feePercent'] * $totalNights : $fee['Fee']['feePercent'];
                            $taxes += $resortFee;
                        }
                    }
                }
                if (isset($price['LoaItemRatePackageRel']['numNights'])) {
                    $totalNights = $price['LoaItemRatePackageRel']['numNights'];
                }
                if ($roomRate['LoaItem']['loaItemTypeId'] == 12) {
                    $rates[$i] = $price['LoaItemRate']['price'] + $taxes;
                }
                else {
                    $rates[$i] = ($price['LoaItemRate']['price'] + $taxes) * ($totalNights);
                }
                $i++;
            }
        }
        $total = 0;
        foreach ($rates as $price) {
            $total += $price;
        }
        return $total;
    }
    
    function createRoomGroup($loaItemIds, $loaId, $packageId) {
        $loaItems = array();
        foreach ($loaItemIds as $loaItemId) {
            $loaItems[] = $loaItemId['loaItemId'];
        }
        $ids = implode(',', $loaItems);
        
        $query = "SELECT * FROM loaItemGroup LoaItemGroup
                  INNER JOIN packageLoaItemRel PackageLoaItemRel ON LoaItemGroup.groupItemId = PackageLoaItemRel.loaItemId
                  WHERE groupItemId IN ({$ids}) AND packageId = {$packageId}";
        if ($group = $this->query($query)) {
            //if (count($group) != count($ids)) {
            //    $groupIds = array();
            //    foreach($group as $g) {
            //        if (!in_array($g['LoaItemGroup']['groupItemId'], $loaItemIds)) {
            //            $this->LoaItemGroup->delete($g['LoaItemGroup']['loaItemGroupId']);
            //        }
            //        else {
            //            $groupIds[] = $g['LoaItemGroup']['groupItemId'];
            //        }
            //    }
            //    foreach($loaItemIds as $id) {
            //        if (!in_array($id, $groupIds)) {
            //            $data = array('groupItemId' => $id,
            //                          'loaItemId' => $g['LoaItemGroup']['loaItemId'],
            //                          'quantity' => 1
            //            );
            //            $this->LoaItemGroup->create();
            //            $this->LoaItemGroup->save($data);
            //        }
            //    }
            //}
            return $group[0]['LoaItemGroup']['loaItemId'];
        }
        else {
            $groupLoaItem = array('loaItemTypeId' => 21,
                                  'loaId' => $loaId,
                                  'itemName' => 'Multiple Room Package: '.$packageId
                          );
            $this->create();
            $this->save($groupLoaItem);
            $loaItemId = $this->getLastInsertID();
            foreach($loaItemIds as $room) {
                $data = array('groupItemId' => $room['loaItemId'],
                              'loaItemId' => $loaItemId,
                              'quantity' => 1
                      );
                $this->LoaItemGroup->create();
                $this->LoaItemGroup->save($data);
            }
            return $loaItemId;
        }
    }
    
    function deleteLoaItem($loaItemId) {
        return $this->delete($loaItemId);
    }
    
}
?>
