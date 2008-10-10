<?php
class LoaItemRatePeriod extends AppModel {

	var $name = 'LoaItemRatePeriod';
	var $useTable = 'loaItemRatePeriod';
	var $primaryKey = 'loaItemRatePeriodId';
	
	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId'));
}
?>