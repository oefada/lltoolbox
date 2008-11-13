<?php
class PackageOfferTypeDefField extends AppModel {

	var $name = 'PackageOfferTypeDefField';
	var $useTable = 'packageOfferTypeDefField';
	var $primaryKey = 'packageOfferTypeDefFieldId';
	var $belongsTo = array('Package' => array('foreignKey' => 'packageId'));
}
?>