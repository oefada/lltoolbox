<?php
class SchedulingMasterPerformance extends AppModel {

	var $name = 'SchedulingMasterPerformance';
	var $useTable = 'schedulingMasterPerformance';
	var $primaryKey = 'schedulingMasterId';
	var $displayField = 'schedulingMasterId';
	
	var $belongsTo = array('SchedulingMaster' => array('foreignKey' => 'schedulingMasterId'));
}
?>