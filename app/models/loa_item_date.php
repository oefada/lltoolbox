<?php
class LoaItemDate extends AppModel {

	var $name = 'LoaItemDate';
	var $useTable = 'loaItemDate';
	var $primaryKey = 'loaItemDateId';
	
	var $belongsTo = array('LoaItemRatePeriod' => array('foreignKey' => 'loaItemRatePeriodId'));
}
?>
