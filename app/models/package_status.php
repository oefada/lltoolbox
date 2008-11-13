<?php
class PackageStatus extends AppModel {

	var $name = 'PackageStatus';
	var $useTable = 'packageStatus';
	var $primaryKey = 'packageStatusId';
	var $displayField = 'packageStatusName';
	
	var $hasMany = array('Package' => array('foreignKey' => 'packageStatusId'));
}
?>