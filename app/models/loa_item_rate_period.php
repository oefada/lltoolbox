<?php
class LoaItemRatePeriod extends AppModel {

	var $name = 'LoaItemRatePeriod';
	var $useTable = 'loaItemRatePeriod';
	var $primaryKey = 'loaItemRatePeriodId';
	
	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId'));
	
	var $hasMany = array('LoaItemRate' => array('foreignKey' => 'loaItemRatePeriodId', 'dependent' => true),
						 'LoaItemDate' => array('foreignKey' => 'loaItemRatePeriodId', 'dependent' => true)
						 );
	
	/*
	DISABLED:  now using LoaItemDate

	var $validate = array('startDate' => array('validateEndStartDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date'),
	                                            'validateOverlap' => array('rule' => array('validateOverlap'), 'message' => 'Dates over lap with another rate period')),
							'endDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date')
							);
	*/

	function validateEndStartDate()
	{
		$startDate = $this->data[$this->name]['startDate'];
		$endDate = $this->data[$this->name]['endDate'];
		
		if($startDate >= $endDate) {
			return false;
		}
		return true;
	}
	
	function validateOverlap() {
	    $itemId = $this->data['LoaItemRatePeriod']['loaItemId'];
	    $start = $this->data['LoaItemRatePeriod']['startDate'];
        $end = $this->data['LoaItemRatePeriod']['endDate'];

	    if (empty($this->data['LoaItemRatePeriod']['loaItemRatePeriodId'])) {
	        $condition = sprintf("loaItemId = %u and ('%s' between startDate and endDate or '%s' between startDate and endDate  or ('%s' <= startDate AND  '%s' >= endDate))", $itemId, $start, $end, $start, $end);
	    } else {
	        $id = $this->data['LoaItemRatePeriod']['loaItemRatePeriodId'];
	        $condition = sprintf("loaItemRatePeriodId != %u and loaItemId = %u and ('%s' between startDate and endDate or '%s' between startDate and endDate  or ('%s' <= startDate AND  '%s' >= endDate))", $id, $itemId, $start, $end, $start, $end);
	    }
	    
	    $sql = 'SELECT startDate, endDate FROM loaItemRatePeriod AS LoaItemRatePeriod WHERE '.$condition.' ORDER BY startDate ASC';
	    
	    $results = $this->query($sql);

	    if (!empty($results)) {
	        $overlapping_dates = array();
	        foreach ($results as $result) {
	            $startDate = $result['LoaItemRatePeriod']['startDate'];
                $endDate = $result['LoaItemRatePeriod']['endDate'];
                
	            $overlapping_dates[] = $startDate.' to '.$endDate;
	        }
	        
    	    $this->invalidate('startDate', 'Dates over lap with the following rate period(s): '.implode(', ', $overlapping_dates));
	    }
	    
	    return true;
	}
    
    /**
     * Package revamp functions
     **/
    function getRatePeriods($roomNightId, $packageId=null) {
        $query = "SELECT LoaItemRatePeriod.* FROM loaItemRatePeriod LoaItemRatePeriod
                  WHERE loaItemId = {$roomNightId}
                  ORDER BY LoaItemRatePeriod.loaItemRatePeriodId";
        if ($ratePeriods = $this->query($query)) {
            foreach ($ratePeriods as $i => &$ratePeriod) {
                $query = "SELECT * FROM loaItemRate LoaItemRate ";
                if ($packageId) {
                    $query .= "LEFT JOIN loaItemRatePackageRel LoaItemRatePackageRel ON LoaItemRatePackageRel.loaItemRateId = LoaItemRate.loaItemRateId AND LoaItemRatePackageRel.packageId = {$packageId} ";
                }
                $query .= "WHERE LoaItemRate.loaItemRatePeriodId = {$ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']}";
                if ($rates = $this->query($query)) {
                    $ratePeriod['LoaItemRate'] = $rates;
                }
                $query = "SELECT * FROM loaItemDate LoaItemDate
                          WHERE LoaItemDate.loaItemRatePeriodId = {$ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']}
                          ORDER BY LoaItemDate.startDate";
                if ($validity = $this->query($query)) {
                    $ratePeriod['Validity'] = $validity;
                }
                else {
                    $ratePeriod['Validity'] = array();
                }
            }
            return $ratePeriods;
        }
    }
    
    function createFromPackage($loaItemId) {
        $ratePeriod = array('loaItemId' => $loaItemId);
        $this->create();
        $this->save($ratePeriod);
        return $this->getLastInsertID();
    }
    
    function updateFromPackage($data, $packageId, $loaItemRatePeriodId) {
        if ($this->save($data)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    function deleteFromPackage($data) {
        foreach ($data as $ratePeriod) {
            if (!empty($date['loaItemRatePeriodId'])) {
                $this->delete($date['loaItemRatePeriodId']);
            }
        }
    }
    
    
}
?>
