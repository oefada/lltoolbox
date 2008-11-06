<?php
class Bid extends AppModel {

	var $name = 'Bid';
	var $useTable = 'bid';
	var $primaryKey = 'bidId';
	var $skipBeforeSaveFilter = false;
	
	var $belongsTo = array('User' => array('foreignKey' => 'userId'),
						   'Offer' => array('foreignKey' => 'offerId')
						   );
	
	function beforeSave() {
		if($this->skipBeforeSaveFilter == false):
		$bidData = $this->data['Bid'];
		unset($this->data['Bid']);
		
		$this->data['Bid']['bidId'] = $bidData['bidId'];
		$this->data['Bid']['inactive'] = $bidData['inactive'];

		endif;
		return true;
	}
	
	function beforeDelete() {
		return false;
	}
}
?>
