<?php

App::import("Vendor","DateHelper",array('file' => "appshared".DS."helpers".DS."DateHelper.php"));

class PackageBlackoutWeekday extends AppModel {

	var $name = 'PackageBlackoutWeekday';
	var $useTable = 'packageBlackoutWeekday';
	var $primaryKey = 'packageBlackoutWeekdayId';
	
	var $belongsTo = array(
		'Package' => array('foreignKey' => 'packageId'),
		'pricePoint' => array('foreignKey' => 'packageId')	
		);
    
  var $actsAs = array('Logable');

	/**
	// ticket3208
	 * Thought this method would do more, but oh well, guess not
	 * 
	 * @param array (int packageId, str weekday)
	 * 
	 * @return null
	 */
	public function saveBlackoutWeekdayStr($data, $siteId){

		// data = packageId=>n, weekday=>str
		// save string (Mon, Tues, etc) to table
		$this->save($data);

	}
	

}
