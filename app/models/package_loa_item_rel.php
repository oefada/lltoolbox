<?php
class PackageLoaItemRel extends AppModel {

	var $name = 'PackageLoaItemRel';
	var $useTable = 'packageLoaItemRel';
	var $primaryKey = 'packageLoaItemRelId';
	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId'),
						   'Package' => array('foreignKey' => 'packageId')
					);
	var $orderBy = array('LoaItemType.loaItemTypeName');
		
	var $validate = array('quantity' => array('rule' => 'numeric', 'message' => 'Must be a number'));
	
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
