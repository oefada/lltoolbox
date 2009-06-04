<?php
class CreditTracking extends AppModel {

	var $name = 'CreditTracking';
	var $useTable = 'creditTracking';
	var $primaryKey = 'creditTrackingId';

	var $belongsTo = array(
		'CreditTrackingType' => array('foreignKey' => 'creditTrackingTypeId'), 'User' => array('foreignKey' => 'userId'),
		'UserSiteExtended' => array('foreignKey' => 'userId'),
		'CreditTrackingOfferRel' => array('foreignKey' => 'creditTrackingId'),
		'CreditTrackingTicketRel' => array('foreignKey' => 'creditTrackingId')
	);
	
	function beforeSave() {
		// get number of trackings for userId and the balance
		$results = $this->query("SELECT balance FROM creditTracking WHERE userId = " . $this->data['CreditTracking']['userId'] . " ORDER BY creditTrackingId DESC LIMIT 1");
		$balance = $results[0]['creditTracking']['balance'];

		// balance
		if (!empty($results)) {
			$this->data['CreditTracking']['balance'] = $balance + $this->data['CreditTracking']['amount'];
		} else {
			$this->data['CreditTracking']['balance'] = $this->data['CreditTracking']['amount'];
		}
		
		// datetime
		$this->data['CreditTracking']['datetime'] = date("Y-m-d H:i:s", time());
		
		return true;
	}
}
?>