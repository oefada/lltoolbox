<?php
class SchedulingMaster extends AppModel {

	var $name = 'SchedulingMaster';
	var $useTable = 'schedulingMaster';
	var $primaryKey = 'schedulingMasterId';

	var $belongsTo = array('SchedulingStatus' => array('foreignKey' => 'schedulingStatusId'),
						   'SchedulingDelayCtrl' => array('foreignKey' => 'schedulingDelayCtrlId'),
						   'RemittanceType' => array('foreignKey' => 'remittanceTypeId'),
						   'Package' => array('foreignKey' => 'packageId'),
						   'OfferType' => array('foreignKey' => 'offerTypeId'),
                           'PricePoint' => array('foreignKey' => 'pricePointId')
						  );
	var $actsAs = array('Logable');

	var $hasOne = array('SchedulingMasterPerformance' => array('foreignKey' => 'schedulingMasterId'));

	var $hasMany = array('SchedulingInstance' => array('foreignKey' => 'schedulingMasterId'));

	var $hasAndBelongsToMany = array(
								'MerchandisingFlag' =>
									array('className' => 'MerchandisingFlag',
										  'joinTable' => 'schedulingMasterMerchFlagRel',
										  'foreignKey' => 'schedulingMasterId',
										  'associationForeignKey' => 'merchandisingFlagId'
									),
								'Track' =>
								    array('className' => 'Track',
								          'joinTable' => 'schedulingMasterTrackRel',
								          'with' => 'SchedulingMasterTrackRel',
								          'foreignKey' => 'schedulingMasterId',
								          'associationForeignKey' => 'trackId')
								);

	var $validate = array('numDaysToRun' => array('rule' =>
												array('comparison', '>=', 1),
												'message' => 'Must be greater than or equal to 1'
												),
						'iterations' => array('rule' =>
													array('validateIterations'),
													'message' => 'Iterations must be greater than or equal to 1'
												),
						'startDate' => array('rule' =>
													array('validateDateRanges'),
													'message' => 'Date must be greater than today and time must be atleast 1 hour from now'
												),
						'endDate' => array(
											'validLoaEndDate' => array('rule' => array('validateLoaEndDate'), 'message' => 'End Date cannot be greater than the Loa End Date'),
											'validDateRange' => array('rule' =>
													array('validateDateRanges'),
													'message' => 'Must be greater than today and greater than the start date')
												),
/*						'openingBid' => array('rule' =>
                        							array('validateOpeningBid'),
                        							'message' => 'Opening bid cannot be $0.00. Adjust the package and then return to schedule it.'
                        							),
                        'buyNowPrice' => array('rule' =>
                                                    array('validatebuyNowPrice'),
                                                    'message' => 'Buy Now Price cannot be $0.00. Adjust the package and then return to schedule it.'
                                                    ),*/
                        'offerTypeId' => array('rule' => VALID_NOT_EMPTY,
                                                    'message' => 'Offer type is a required field.'
                                                    )
						);

	// mbyrnes
	// ticket 1628
	function hasRow($pricePointId,$offerTypeId){

		$q="SELECT * FROM schedulingMaster ";
		$q.="WHERE pricePointId='$pricePointId' AND offerTypeId='$offerTypeId' ";
		$q.="AND endDate>NOW() ";
		$row=$this->query($q);
		return count($row);

	}
	function validateDateRanges($data) {
		$packageStartDate = $this->data['SchedulingMaster']['startDate'];
		$packageEndDate = $this->data['SchedulingMaster']['endDate'];

		if(isset($data['startDate']) && strtotime($data['startDate'].' -1 hours') < time()) 	return false;
		if(isset($data['endDate']) && $this->data['SchedulingMaster']['iterationSchedulingOption'] && ($packageStartDate >= $packageEndDate))	return false;

		return true;
	}

	function validateLoaEndDate($data) {
		$packageId = $this->data['SchedulingMaster']['packageId'];

		$rows = $this->query("SELECT MIN(Loa.endDate) as minEndDate FROM loa AS Loa INNER JOIN clientLoaPackageRel USING(loaId) WHERE packageId = $packageId");

		$this->data['SchedulingMaster']['endDate'] = substr($this->data['SchedulingMaster']['endDate'], 0, 10);
		$rows[0][0]['minEndDate'] = substr($rows[0][0]['minEndDate'], 0, 10);

		if (strtotime($this->data['SchedulingMaster']['endDate']) > strtotime($rows[0][0]['minEndDate']) && $this->data['SchedulingMaster']['iterationSchedulingOption']) {
			return false;
		}

		return true;
	}

	function validateIterations() {

	    // 06/13/2011 jwoods - hotel offers don't need iteration check
	    if ($this->data['SchedulingMaster']['offerTypeId'] == 7) {
	        return true;
	    }

	    if (1 == $this->data['SchedulingMaster']['iterationSchedulingOption']) {
	        return ($this->data['SchedulingMaster']['iterations'] >= 1);
	    }

	    return true;
	}

	function validateOpeningBid() {
	    $auctionTypes = array(1,2,6);

	    if (in_array($this->data['SchedulingMaster']['offerTypeId'], $auctionTypes)) {
	        if(!isset($this->data['SchedulingMaster']['openingBid']) || $this->data['SchedulingMaster']['openingBid'] <= 0) {
	            return false;
	        }
	    }

	    return true;
	}

	function validatebuyNowPrice() {
	    $buyNowTypes = array(3,4);

	    if (in_array($this->data['SchedulingMaster']['offerTypeId'], $buyNowTypes)) {
	        if(!isset($this->data['SchedulingMaster']['buyNowPrice']) || $this->data['SchedulingMaster']['buyNowPrice'] <= 0) {
	            return false;
	        }
	    }

	    return true;
	}

	function beforeValidate() {
	    foreach ($this->data['Track']['Track'] as $track) {
	        if (!empty($track)) {
	            return true;
	        }
	    }

	    // 06/13/2011 jwoods - hotel offers don't need track check
	    if ($this->data['SchedulingMaster']['offerTypeId'] == 7) {
	        return true;
	    }

	    $this->validationErrors['Track']['Track'] = 'Please select a track';
	}

	function getOfferIdFromInstance($sid) {
		if ($r = $this->query("SELECT offerId FROM offer WHERE schedulingInstanceId = {$sid}")) {
            return $r[0]['offer']['offerId'];
        }
	}

	function end() {
	    $instnance = new SchedulingInstance;
	    $test = $instnance->find('first');
	    debug($test);
	}

	function endTrack() {

	}

	function endPackage() {

	}

	function endFixedPrice() {

	}

	function endAuction() {

	}
}
?>
