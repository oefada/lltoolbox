<?php
class PackageValidityDisclaimer extends AppModel {

	var $name = 'PackageValidityDisclaimer';
	var $useTable = 'packageValidityDisclaimer';
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'));
	
}
?>
