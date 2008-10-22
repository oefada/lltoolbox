<?php
class Package extends AppModel {

	var $name = 'Package';
	var $useTable = 'package';
	var $primaryKey = 'packageId';
	
	var $belongsTo = array('Currency' => array('foreignKey' => 'currencyId'),
						   'PackageStatus' => array('foreignKey' => 'packageStatusId')
						);
	
	var $hasMany = array('PackageValidityPeriod' => array('foreignKey' => 'packageId'),
						 'PackageOfferTypeDefFieldRel' => array('foreignKey' => 'packageId'),
						 'PackageLoaItemRel' => array('foreignKey' => 'packageId'),
						 'PackagePromo' => array('foreignKey' => 'packageId')
						);
	
	var $hasAndBelongsToMany = array(
								'Format' => 
									array('className' => 'Format',
										  'joinTable' => 'packageFormatRel',
										  'foreignKey' => 'packageId',
										  'associationForeignKey' => 'formatId'
									)
								);

}
?>