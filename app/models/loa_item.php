<?php
class LoaItem extends AppModel {

	var $name = 'LoaItem';
	var $useTable = 'loaItem';
	var $primaryKey = 'loaItemId';
	var $displayField = 'itemName';
	
	var $belongsTo = array('LoaItemType' => array('foreignKey' => 'loaItemTypeId'),
							'Loa' => array('foreignKey' => "loaId"),
							'Currency' => array('foreignKey' => 'currencyId'));

	var $hasOne = array('Fee' => array('foreignKey' => 'loaItemId', 'dependent' => true),
						'PackageLoaItemRel' => array('foreignKey' => 'loaItemId', 'dependent' => true)
					   );
	var $hasMany = array('LoaItemRatePeriod' => array('foreignKey' => 'loaItemId', 'dependent'=> true));
	
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
		elseif(is_int($itemIds)):
			$itemIds = array($itemIds);			//if only an integer was passed, set the id to the integer
		elseif(!is_array($itemIds)):
			return array();						//if no valid data was passed, return an empty array
		endif;
		
		$ratePeriodBoundaries = $this->getRatePeriodBoundaries($itemIds, $startDate, $endDate);

		$one_day = 24 * 60 * 60;
		$itemList = $this->find('list', array('conditions' => array('loaItemId' => $itemIds)));
		$currencyList = $this->find('list', array('fields' => array('currencyId'), 'conditions' => array('loaItemId' => $itemIds)));

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
						if( strtotime($loaItemRatePeriod['startDate']) <= $rangeStart && strtotime($loaItemRatePeriod['endDate']) >= $rangeEnd):
							$ratePeriodPrice = $loaItemRatePeriod['price'];
							break;
						else:
							$ratePeriodPrice = $this->loaItems[$itemId]['LoaItem']['itemBasePrice'];
						endif;
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
		
		$ratePeriodBoundaries = array();
		//loop through all of the items
		foreach($itemIds as $itemId):
			$this->id = $itemId;
			$this->read(null, $this->id);
			$this->loaItems[$this->id] = $this->data;
			
 			foreach($this->data['LoaItemRatePeriod'] as $loaItemRatePeriod):
				if(null != $startDate && strtotime($loaItemRatePeriod['startDate']) > strtotime($startDate) && strtotime($loaItemRatePeriod['startDate']) < strtotime($endDate)):
					$ratePeriodBoundaries[] = $loaItemRatePeriod['startDate'];
				endif;
			
				if(null != $endDate && strtotime($loaItemRatePeriod['endDate']) < strtotime($endDate) && strtotime($loaItemRatePeriod['endDate']) > strtotime($startDate)):
					$ratePeriodBoundaries[] = $loaItemRatePeriod['endDate'];
				endif;
			endforeach;
		endforeach;
		
		sort($ratePeriodBoundaries);			//sort boundaries
		
		if($startDate !== null):				//append start date if entered
			array_unshift($ratePeriodBoundaries, $startDate);
		endif;
			
		if($endDate !== null):					//append end date if entered
			array_push($ratePeriodBoundaries, $endDate);
		endif;
		
		$ratePeriodBoundaries = array_unique($ratePeriodBoundaries);
		$ratePeriodBoundaries = array_merge($ratePeriodBoundaries, array());	//fix the fact that array_unique does not re-number keys

		return $ratePeriodBoundaries;
	}
}
?>