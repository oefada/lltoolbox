<?php
class PackageOfferTypeDefFieldRel extends AppModel {

	var $name = 'PackageOfferTypeDefFieldRel';
	var $useTable = 'packageOfferTypeDefFieldRel';
	var $primaryKey = 'packageOfferTypeDefFieldRelId';
	var $belongsTo = array('OfferTypeDefField' => array('foreignKey' => 'offerTypeDefFieldId'));
}
?>