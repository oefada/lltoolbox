<?php
class LoaItemRatePeriod extends AppModel {

	var $name = 'LoaItemRatePeriod';
	var $useTable = 'loaItemRatePeriod';
	var $primaryKey = 'loaItemRatePeriodId';
	
	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId'));
	
	var $validate = array('startDate' => array('validateEndStartDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date'),
	                                            'validateOverlap' => array('rule' => array('validateOverlap'), 'message' => 'Dates over lap with another rate period')),
							'endDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date')
							);

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
	        $condition = sprintf("loaItemId = %u and ('%s' between startDate and endDate or '%s' between startDate and endDate)", $itemId, $start, $end);
	    } else {
	        $id = $this->data['LoaItemRatePeriod']['loaItemRatePeriodId'];
	        $condition = sprintf("loaItemRatePeriodId != %u and loaItemId = %u and ('%s' between startDate and endDate or '%s' between startDate and endDate)", $id, $itemId, $start, $end);
	    }
	    	    
	    $sql = 'SELECT startDate, endDate FROM loaItemRatePeriod AS LoaItemRatePeriod WHERE '.$condition;
	    
	    $results = $this->query($sql);

	    if (!empty($results)) {
	        $overlapping_dates = array();
	        foreach ($results as $result) {
	            $start = $result['LoaItemRatePeriod']['startDate'];
                $end = $result['LoaItemRatePeriod']['endDate'];
                
	            $overlapping_dates[] = $start.' to '.$end;
	        }   
	        
    	    $this->invalidate('startDate', 'Dates over lap with the following rate period(s): '.implode(', ', $overlapping_dates));
	    }
	    
	    return true;
	}
}
?>