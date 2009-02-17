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
						 'PackagePromo' => array('foreignKey' => 'packageId'),
						 'ClientLoaPackageRel' => array('foreignKey' => 'packageId', 'dependent' => true),
						 'PackageRatePeriod' => array('foreignKey' => 'packageId'),
						 'SchedulingMaster' => array('foreignKey' => 'packageId')
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
                $itemDescriptions = $this->query("SELECT LoaItem.loaItemId, LoaItem.merchandisingDescription FROM loaItem AS LoaItem WHERE LoaItem.loaItemId IN(".implode(',', $this->data['Package']['CheckedLoaItems']).")");
	        }
	        foreach ($itemDescriptions as $v) {
	            $itemId = $v['LoaItem']['loaItemId'];
	            $weight = $this->data['PackageLoaItemRel'][$itemId]['weight'];
	            $descriptions[$weight] = $v['LoaItem']['merchandisingDescription'];
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

	    return true;
	}
}
?>