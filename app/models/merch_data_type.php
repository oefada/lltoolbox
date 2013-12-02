<?php
class MerchDataType extends AppModel
{
	
	var $name = 'MerchDataType';
	var $useTable = 'merchDataType';
	var $primaryKey = 'id';
	var $hasMany = Array('MerchDataEntries' => Array('foreignKey' => 'merchDataTypeId'));

}
?>
