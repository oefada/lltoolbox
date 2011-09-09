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
    
    function getBidStatsForOffer($offerId) {
        $query = "SELECT COUNT(bidId) AS bidCount, MAX(bidAmount) AS winner
                  FROM bid 
                  WHERE offerId = " . $offerId;
        if ($bidStats = $this->query($query)) {
            return $bidStats;
        }
    }
}
?>
