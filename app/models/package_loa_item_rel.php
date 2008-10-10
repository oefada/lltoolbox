<?php
class PackageLoaItemRel extends AppModel {

	var $name = 'PackageLoaItemRel';
	var $useTable = 'packageLoaItemRel';
	var $primaryKey = 'packageLoaItemRelId';
	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId'),
						   'Package' => array('foreignKey' => 'packageId'),
						   'LoaItemGroup' => array('foreignKey' => 'loaItemGroupId')
					);
		
	var $hasMany = array('PackageRatePeriodItemRel' => array('foreignKey' => 'packageLoaItemRelId'));	
	
	/*			
	var $hasAndBelongsToMany = array(
								'packageRatePeriod' => 
									array('className' => 'packageRatePeriod',
										  'joinTable' => 'packageRatePeriodItemRel',
										  'foreignKey' => 'packageLoaItemRelId',
										  'associationForeignKey' => 'packageRatePeriodId'
									)
								);*/

}
?>
