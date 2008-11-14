<?php
class SchedulingMaster extends AppModel {

	var $name = 'SchedulingMaster';
	var $useTable = 'schedulingMaster';
	var $primaryKey = 'schedulingMasterId';
	
	var $belongsTo = array('SchedulingStatus' => array('foreignKey' => 'schedulingStatusId'),
						   'SchedulingDelayCtrl' => array('foreignKey' => 'schedulingDelayCtrlId'),
						   'RemittanceType' => array('foreignKey' => 'remittanceTypeId'),
						   'Package' => array('foreignKey' => 'packageId')
						  );
						  
	var $hasMany = array('SchedulingInstance' => array('foreignKey' => 'schedulingMasterId'));

	var $hasAndBelongsToMany = array(
								'MerchandisingFlag' => 
									array('className' => 'merchandisingFlag',
										  'joinTable' => 'schedulingMasterMerchFlagRel',
										  'foreignKey' => 'schedulingMasterId',
										  'associationForeignKey' => 'merchandisingFlagId'
									)
								);
	
}
?>