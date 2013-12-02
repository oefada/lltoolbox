<?php
class Format extends AppModel {

	var $name = 'Format';
	var $useTable = 'format';
	var $primaryKey = 'formatId';
	var $displayField = 'formatName';
	
	var $hasAndBelongsToMany = array(
								'Package' => 
									array('className' => 'Package',
										  'joinTable' => 'packageFormatRel',
										  'foreignKey' => 'formatId',
										  'associationForeignKey' => 'packageId'
									),
								'OfferType' => 
									array('className' => 'OfferType',
										  'joinTable' => 'formatOfferTypeRel',
										  'foreignKey' => 'formatId',
										  'associationForeignKey' => 'offerTypeId'
									)
								);
}
?>