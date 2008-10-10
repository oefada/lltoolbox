<?php
class SchedulingInstance extends AppModel {

	var $name = 'SchedulingInstance';
	var $useTable = 'schedulingInstance';
	var $primaryKey = 'schedulingInstanceId';
	
	//var $belongsTo = array('SchedulingMaster' => array('foreignKey' => 'schedulingMasterId'));
	var $hasOne = array('Offer' => array('foreignKey' => 'schedulingInstanceId'));
}
?>