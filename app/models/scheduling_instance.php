<?php
class SchedulingInstance extends AppModel {

	var $name = 'SchedulingInstance';
	var $useTable = 'schedulingInstance';
	var $primaryKey = 'schedulingInstanceId';
	
	var $belongsTo = array('SchedulingMaster' => array('foreignKey' => 'schedulingMasterId'));
	var $hasOne = array('Offer' => array('foreignKey' => 'schedulingInstanceId'));

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
    
    function getInstancePricePointId($instance, $packageId) {
        $query = "SELECT pricePointId from pricePoint PricePoint
                  WHERE '{$instance['SchedulingInstance']['startDate']}' BETWEEN PricePoint.validityStart AND PricePoint.validityEnd
                  AND PricePoint.packageId = {$packageId}";
        if ($pricePoint = $this->query($query)) {
            return ($pricePoint[0]['PricePoint']['pricePointId']);
        }
    }
    
    function isScheduled($schedulingMasterId, $endDate) {
        $query = "SELECT 1 FROM schedulingInstance
                  WHERE schedulingMasterId = " . $schedulingMasterId . " AND
                  offerCreated = 0 AND endDate <= '" . $endDate . "'";
        if ($scheduled = $this->query($query)) {
            return true;
        }
        else {
            return false;
        }
    }
}
?>