<?php
class Package extends AppModel {

	var $name = 'Package';
	var $useTable = 'package';
	var $primaryKey = 'packageId';
	
	var $belongsTo = array('Currency' => array('foreignKey' => 'currencyId'),
						   'PackageStatus' => array('foreignKey' => 'packageStatusId')
						);
	
	var $hasOne = array('PackagePerformance' => array('foreignKey' => 'packageId'));
	
	var $hasMany = array('PackageValidityPeriod' => array('foreignKey' => 'packageId'),
						 'PackageOfferTypeDefField' => array('foreignKey' => 'packageId'),
						 'PackageLoaItemRel' => array('foreignKey' => 'packageId'),
						 'ClientLoaPackageRel' => array('foreignKey' => 'packageId', 'dependent' => true),
						 'PackageRatePeriod' => array('foreignKey' => 'packageId'),
						 'SchedulingMaster' => array('foreignKey' => 'packageId'),
						 'ClientTracking' => array('foreignKey' => 'packageId'),
						 'PackageAgeRange' => array('foreignKey' => 'packageId')
						);
						
	var $validate = array('packageName' => VALID_NOT_EMPTY,
						'numConcurrentOffers' => array('rule' => 'numeric', 'message' => 'Must be a number'),
						'maxNumSales' => array('rule' => 'numeric', 'message' => 'Must  be a number', 'allowEmpty' => true),
						'numGuests' => array('rule' => 'numeric', 'message' => 'Must be a number'),
						'numNights' => array('numeric' => array('rule' => 'numeric', 'message' => 'Must be a number'),
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
	        if ($item['loaItemTypeId'] == 12) {
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
        }
	    return false;
	}
	function cloneData($data)
	{
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
        if ( $this->data['Package']['repopulateInclusions'] ) {
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
	    //dynamically set the client approved date
	    if ($created != true) {
    	    $orig = $this->find('Package.packageId = '.$this->data['Package']['packageId'], array('packageStatusId'));

    	    if (@$orig['Package']['packageStatusId'] != 3 && $this->data['Package']['packageStatusId'] == 3) {
    	        $this->data['Package']['dateClientApproved'] = date('Y-m-d H:i:s');
    	    }
	    }

	   //set packageStatusId to Setup if new package
	   if (!isset($this->data['Package']['packageStatusId'])) {
		$this->data['Package']['packageStatusId'] = 1;
	   }

	    return true;
	}
	
	function afterSave($created) {
		// get validity disclaimer by joining it with validityLeadInLine
		$validity_disclaimer = ($this->data['Package']['validityLeadInLine']) ? '<p><strong>' . Sanitize::escape($this->data['Package']['validityLeadInLine']) . '</strong></p>' : ' ';
		$validity_disclaimer .= Sanitize::escape($this->data['Package']['validityDisclaimer']);

	   //delete from packageAgeRange if numAdults and numChildren isn't set
	   if (empty($this->data['Package']['numAdults']) && empty($this->data['Package']['numChildren'])) {
		$age_ranges = $this->PackageAgeRange->findByPackageId($this->data['Package']['packageId']);
		if (!empty($age_ranges)) {
		   $this->PackageAgeRange->deleteAll(array('packageId' => $this->data['Package']['packageId']), false);
		}
	   }
	    $this->query("UPDATE offerLive SET validityStart = '{$this->data['Package']['validityStartDate']}', validityEnd = '{$this->data['Package']['validityEndDate']}', validityDisclaimer = '$validity_disclaimer' WHERE packageId = $this->id AND isAuction = 0 AND now() < endDate");
	    
	    // update offer details in offerLive for hotel offers type (7)
	    if (!empty($this->data['Package']['externalOfferUrl'])) {
		$this->query("
			    UPDATE offerLive
			    SET validityStart = '{$this->data['Package']['validityStartDate']}', validityEnd = '{$this->data['Package']['validityEndDate']}',
				    offerName = '{$this->data['Package']['packageTitle']}', shortBlurb = '{$this->data['Package']['shortBlurb']}', additionalDescription = '{$this->data['Package']['additionalDescription']}',
				    offerIncludes = '{$this->data['Package']['packageIncludes']}', externalOfferUrl = '{$this->data['Package']['externalOfferUrl']}' 
			    WHERE packageId = $this->id AND offerTypeId = 7 AND now() < endDate
		    ");
	    }
	}
}
?>
