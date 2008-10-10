<?php
class OfferType extends AppModel {

	var $name = 'OfferType';
	var $useTable = 'offerType';
	var $primaryKey = 'offerTypeId';
	var $displayField = 'offerTypeName';
	var $hasAndBelongsToMany = array(
							'Format' => 
								array('className' => 'Format',
									  'joinTable' => 'formatOfferTypeRel',
									  'foreignKey' => 'offerTypeId',
									  'associationForeignKey' => 'formatId'
								),
							'OfferTypeDefField' => 
									array('className' => 'OfferTypeDefField',
										  'joinTable' => 'offerTypeOfferTypeDefFieldRel',
										  'foreignKey' => 'offerTypeId',
										  'associationForeignKey' => 'offerTypeDefFieldId'
								)
							);
}
?>