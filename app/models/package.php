<?php
class Package extends AppModel {

	var $name = 'Package';
	var $useTable = 'package';
	var $primaryKey = 'packageId';
	
	var $belongsTo = array('Currency' => array('foreignKey' => 'currencyId'),
						   'PackageStatus' => array('foreignKey' => 'packageStatusId')
						);
	
	var $hasOne = array('PackagePerformance' => array('foreignKey' => 'packageId'));
	
	var $hasMany = array('PackageValidityPeriod' => array('foreignKey' => 'packageId'),
						 'PackageOfferTypeDefField' => array('foreignKey' => 'packageId'),
						 'PackageLoaItemRel' => array('foreignKey' => 'packageId'),
						 'PackagePromo' => array('foreignKey' => 'packageId'),
						 'ClientLoaPackageRel' => array('foreignKey' => 'packageId', 'dependent' => true),
						 'PackageRatePeriod' => array('foreignKey' => 'packageId'),
						 'SchedulingMaster' => array('foreignKey' => 'packageId')
						);
						
	var $validate = array('packageName' => VALID_NOT_EMPTY,
						'numConcurrentOffers' => array('rule' => 'numeric', 'message' => 'Must be a number'),
						'maxNumSales' => array('rule' => 'numeric', 'message' => 'Must  be a number', 'allowEmpty' => true),
						'numGuests' => array('rule' => 'numeric', 'message' => 'Must be a number'),
						'numNights' => array('rule' => 'numeric', 'message' => 'Must be a number'),
						'endDate' => array('rule' => array('validateDateRanges'), 'message' => 'End Date must be greater than Start Date'),
						'validityEndDate' => array('rule' => array('validateDateRanges'), 'message' => 'End Date must be greater than Start Date'));
		
	var $hasAndBelongsToMany = array(
								'Format' => 
									array('className' => 'Format',
										  'joinTable' => 'packageFormatRel',
										  'foreignKey' => 'packageId',
										  'associationForeignKey' => 'formatId'
									)
								);

	function validateDateRanges($data) {
		$packageStartDate = $this->data['Package']['startDate'];
		$packageEndDate = $this->data['Package']['endDate'];
		
		$validityStartDate = $this->data['Package']['validityStartDate'];
		$validityEndDate = $this->data['Package']['validityEndDate'];
		
		if(isset($data['validityEndDate']) && $validityStartDate >= $validityEndDate)	return false;
		if(isset($data['endDate']) && $packageStartDate >= $packageEndDate)	return false;

		return true;
	}
	
	function cloneData($data)
	{
		$data['Package']['copiedFromPackageId'] = $data['Package']['packageId'];
	
		foreach ($data['ClientLoaPackageRel'] as &$packageRel):
			unset($packageRel['clientLoaPackageRelId']);
		endforeach;
	
		unset($data['Package']['packageId']);
		
		return $data;
	}
}
?>