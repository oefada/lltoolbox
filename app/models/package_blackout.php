<?php
class PackageBlackout extends AppModel {

	var $name = 'PackageBlackout';
	var $useTable = 'packageBlackout';
	var $primaryKey = 'packageBlackoutId';
	
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'));
	
}
?>
