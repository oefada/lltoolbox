<?php
class PackageValidityPeriod extends AppModel {

	var $name = 'PackageValidityPeriod';
	var $useTable = 'packageValidityPeriod';
	var $primaryKey = 'packageValidityPeriodId';
	
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'));
	
	function afterFind($results, $primary = false) {
	    if (!$primary):
	        return $this->stripRecurringDays($results);
	    endif;
	}
	
	/**
	 * Method strips all of the recurring day blackouts so they are not displayed
	 * @param array $results the result array passed in from the @link(afterFind()) method.
	 * @return the modified result set
	 */
	function stripRecurringDays($results)
	{
	    foreach ($results as $k => $result):
	        if (isset($result[$this->name])):
	            foreach ($result[$this->name] as $k2 => $validityPeriod):
	            if ($k2 == 'isWeekDayRepeat' && $validityPeriod == 1) {
	                unset($results[$k][$this->name]);
	            } elseif ($k2 == $this->name && $validityPeriod['isWeekDayRepeat'] == 1) {
	                unset($results[$k][$this->name][$k2]);
	            }
	            endforeach;
	        endif;
	    endforeach;
	    
	    return $results;
	}
}
?>