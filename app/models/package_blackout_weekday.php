<?php
class PackageBlackoutWeekday extends AppModel {

	var $name = 'PackageBlackoutWeekday';
	var $useTable = 'packageBlackoutWeekday';
	var $primaryKey = 'packageBlackoutWeekdayId';
	
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'));
	
}
?>
