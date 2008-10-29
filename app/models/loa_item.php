<?php
class LoaItem extends AppModel {

	var $name = 'LoaItem';
	var $useTable = 'loaItem';
	var $primaryKey = 'loaItemId';
	var $displayField = 'itemName';
	
	var $belongsTo = array('LoaItemType' => array('foreignKey' => 'loaItemTypeId'));
	var $hasOne = array('Fee' => array('foreignKey' => 'loaItemId'),
						'PackageLoaItemRel' => array('foreignKey' => 'loaItemId')
					   );
	var $hasMany = array('LoaItemRatePeriod' => array('foreignKey' => 'loaItemId', 'dependent'=> true));
}
?>