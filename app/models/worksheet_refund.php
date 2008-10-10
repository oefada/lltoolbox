<?php
class WorksheetRefund extends AppModel {

	var $name = 'WorksheetRefund';
	var $useTable = 'worksheetRefund';
	var $primaryKey = 'worksheetRefundId';
	
	var $hasOne = array('Worksheet' => array('foreignKey' => 'worksheetId'));
	
	var $belongsTo = array('RefundReason' => array('foreignKey' => 'refundReasonId'));

}
?>
