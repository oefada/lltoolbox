<?php
class PackageRatePeriod extends AppModel {

	var $name = 'PackageRatePeriod';
	var $useTable = 'packageRatePeriod';
	var $primaryKey = 'packageRatePeriodId';
	
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'),
							'LoaItem' => array('foreignKey' => 'loaItemId'));
	
}
?>