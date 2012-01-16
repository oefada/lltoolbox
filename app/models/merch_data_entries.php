<?php
class MerchDataEntries extends AppModel
{
	
	var $name = 'MerchDataEntries';
	var $useTable = 'merchDataEntries';
	var $primaryKey = 'id';
	var $belongsTo = Array('MerchDataType' => Array('foreignKey' => 'merchDataTypeId'));

	function afterFind($results)
	{
		foreach ($results AS &$r) {
			if (isset($r['MerchDataEntries']['merchDataJSON']) 
				&& ($merchDataArr = json_decode($r['MerchDataEntries']['merchDataJSON'], true)) != null)
			{
				$r['MerchDataEntries']['merchDataArr'] = $merchDataArr;
			}
		}
		
		return $results;
	}
	
}
