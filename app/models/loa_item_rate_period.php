<?php
class LoaItemRatePeriod extends AppModel {

	var $name = 'LoaItemRatePeriod';
	var $useTable = 'loaItemRatePeriod';
	var $primaryKey = 'loaItemRatePeriodId';
	
	var $belongsTo = array('LoaItem' => array('foreignKey' => 'loaItemId'));
	
	var $validate = array('startDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date'),
							'endDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date')
							);

	function validateEndStartDate()
	{
		$startDate = $this->data[$this->name]['startDate'];
		$endDate = $this->data[$this->name]['endDate'];
		
		if($startDate >= $endDate) {
			return false;
		}
		return true;
	}
}
?>