<?php
class LoaItemType extends AppModel {

	var $name           = 'LoaItemType';
	var $useTable       = 'loaItemType';
	var $primaryKey     = 'loaItemTypeId';
	var $displayField   = 'loaItemTypeName';
	var $order          = 'loaItemTypeName';
    
    function getItemTypes() {
        $query = "SELECT * FROM loaItemType LoaItemType
                  WHERE sortOrder IS NOT NULL
                  ORDER BY sortOrder";
        return $this->query($query);
    }
    
}
?>