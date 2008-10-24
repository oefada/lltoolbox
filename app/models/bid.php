<?php
class Bid extends AppModel {

	var $name = 'Bid';
	var $useTable = 'bid';
	var $primaryKey = 'bidId';
	
	var $belongsTo = array('User' => array('foreignKey' => 'userId'),
						   'Offer' => array('foreignKey' => 'offerId')
						   );
	
	function beforeSave() {
		$bidData = $this->data['Bid'];
		unset($this->data['Bid']);
		
		$this->data['Bid']['bidId'] = $bidData['bidId'];
		$this->data['Bid']['bidInactive'] = $bidData['bidInactive'];
		
		return true;
	}
	
	function beforeDelete() {
		return false;
	}
}
?>
