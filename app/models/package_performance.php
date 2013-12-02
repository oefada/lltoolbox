<?php
class PackagePerformance extends AppModel {

	var $name = 'PackagePerformance';
	var $useTable = 'packagePerformance';
	var $primaryKey = 'packageId';
	var $displayField = 'packageId';
	
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'));
}
?>