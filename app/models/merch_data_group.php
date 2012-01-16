<?php
class MerchDataGroup extends AppModel
{
	var $name = 'MerchDataGroup';
	var $useTable = 'merchDataGroup';
	var $primaryKey = 'id';
	var $hasMany = Array('MerchDataType' => Array('foreignKey' => 'merchDataGroupId'));

}
?>
