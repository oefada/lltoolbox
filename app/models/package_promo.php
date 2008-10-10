<?php
class PackagePromo extends AppModel {

	var $name = 'PackagePromo';
	var $useTable = 'packagePromo';
	var $primaryKey = 'packagePromoId';
	
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'));

}
?>