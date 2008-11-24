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
	
	var $hasOne = array('SchedulingMasterPerformance' => array('foreignKey' => 'schedulingMasterId'));
						  
	var $hasMany = array('SchedulingInstance' => array('foreignKey' => 'schedulingMasterId'));

	var $hasAndBelongsToMany = array(
								'MerchandisingFlag' => 
									array('className' => 'merchandisingFlag',
										  'joinTable' => 'schedulingMasterMerchFlagRel',
										  'foreignKey' => 'schedulingMasterId',
										  'associationForeignKey' => 'merchandisingFlagId'
									)
								);
	
	var $validate = array('numDaysToRun' => array('rule' => 
												array('comparison', '>=', 1),
												'message' => 'Must be greater than or equal to 1'
												),
						'iterations' => array('rule' => 
													array('comparison', '>=', 1),
													'message' => 'Must be greater than or equal to 1',
													'allowEmpty' => true
												),
						'startDate' => array('rule' => 
													array('validateDateRanges'),
													'message' => 'Date must be greater than today and time must be atleast 1 hour from now'
												),
						'endDate' => array('rule' => 
													array('validateDateRanges'),
													'message' => 'Must be greater than today and greater than the start date'
												)
						);
	
	function validateDateRanges($data) {
		$packageStartDate = $this->data['SchedulingMaster']['startDate'];
		$packageEndDate = $this->data['SchedulingMaster']['endDate'];
		
		if(isset($data['startDate']) && strtotime($data['startDate'].' -1 hours') < time()) 	return false;
		if(isset($data['endDate']) && $this->data['SchedulingMaster']['iterationSchedulingOption'] && ($packageStartDate >= $packageEndDate))	return false;
		
		return true;
	}
}
?>