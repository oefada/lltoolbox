<?php
class PackageOfferTypeDefFieldsRel extends AppModel {

	var $name = 'PackageOfferTypeDefFieldsRel';
	var $useTable = 'packageOfferTypeDefFieldsRel';
	var $primaryKey = 'packageOfferTypeDefFieldsRelId';
	var $belongsTo = array('OfferTypeDefField' => array('foreignKey' => 'offerTypeDefFieldId'));
}
?>