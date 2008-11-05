<?php
class PackageValidityPeriod extends AppModel {

	var $name = 'PackageValidityPeriod';
	var $useTable = 'packageValidityPeriod';
	var $primaryKey = 'packageValidityPeriodId';
	
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'));
}
?>