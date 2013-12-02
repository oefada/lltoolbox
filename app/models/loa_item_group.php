<?php
class LoaItemGroup extends AppModel {

	var $name = 'LoaItemGroup';
	var $useTable = 'loaItemGroup';
	var $primaryKey = 'loaItemGroupId';

	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId'));
	
	function getLoaItemIds($packageId) {
		$group = array();
		$query = "SELECT groupItemId FROM loaItemGroup LoaItemGroup
			  INNER JOIN packageLoaItemRel PackageLoaItemRel ON LoaItemGroup.groupItemId = PackageLoaItemRel.loaItemId
			  WHERE PackageLoaItemRel.packageId = {$packageId}";
		if ($items = $this->query($query)) {
			foreach ($items as $item) {
				$group[] = $item['LoaItemGroup']['groupItemId'];
			}
		}
		return $group;
	}
	
	function getGroup($packageId, $groupItemId) {
		$query = "SELECT * FROM loaItemGroup LoaItemGroup
			  INNER JOIN packageLoaItemRel PackageLoaItemRel ON LoaItemGroup.groupItemId = PackageLoaItemRel.loaItemId
			  INNER JOIN loaItem LoaItem ON LoaItemGroup.loaItemId = LoaItem.loaItemId
			  WHERE PackageLoaItemRel.packageId = {$packageId} AND LoaItem.loaItemTypeId = 21";
		return $this->query($query);
	}
    
    function deleteLoaItemGroup($loaItemGroupId) {
        return $this->delete($loaItemGroupId);
    }
}
?>
