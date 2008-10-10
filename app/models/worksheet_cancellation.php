<?php
class WorksheetCancellation extends AppModel {

	var $name = 'WorksheetCancellation';
	var $useTable = 'worksheetCancellation';
	var $primaryKey = 'worksheetCancellationId';
	
	var $hasOne = array('Worksheet' => array('foreignKey' => 'worksheetId'));
	
	var $belongsTo = array('CancellationReason' => array('foreignKey' => 'cancellationReasonId'));

}
?>
