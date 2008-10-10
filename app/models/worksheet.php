<?php
class Worksheet extends AppModel {

	var $name = 'Worksheet';
	var $useTable = 'worksheet';
	var $primaryKey = 'worksheetId';
	
	var $belongsTo = array('WorksheetStatus' => array('foreignKey' => 'worksheetStatusId'));
	
	var $hasMany = array('PaymentDetail' => array('foreignKey' => 'worksheetId'),
						 'PpvNotice' => array('foreignKey' => 'worksheetId')
						);
	
	var $hasOne = array('WorksheetCancellation' => array('foreignKey' => 'worksheetId'),
						'WorksheetRefund' => array('foreignKey' => 'worksheetId'),
						'Reservation' => array('foreignKey' => 'worksheetId')
						);
				   		
}
?>