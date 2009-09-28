<?php
class LoaItemRate extends AppModel {

	var $name = 'LoaItemRate';
	var $useTable = 'loaItemRate';
	var $primaryKey = 'loaItemRateId';

	var $belongsTo = array('LoaItemRatePeriod' => array('foreignKey' => 'loaItemRatePeriodId'));
}
?>
