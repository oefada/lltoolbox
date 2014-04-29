<?php
class Track extends AppModel {

	var $name = 'Track';
	var $useTable = 'track';
	var $primaryKey = 'trackId';
	var $displayField = 'trackName';
	var $actsAs = array('Logable');
	
	var $belongsTo = array('ExpirationCriterium' => array('foreignKey' => 'expirationCriteriaId'),
						   'RevenueModel' => array('foreignKey' => 'revenueModelId')
						  );
						  
	var $hasMany = array('TrackDetail' => array('foreignKey' => 'trackId'));
	
	var $validate = array(
						'loaId' => array(
								'rule' => 'numeric',
								'message' => 'The Loa Id must be numeric.'
								),
						'keepPercentage' => array(
								'rule' => array('checkPresets'),
								'message' => 'The Keep Percentage field is required for the "Revenue Split" revenue model.  This value must be a number from 0 to 100.'
								),
						'commissionPercentage' => array(
								'rule' => array('checkPresets'),
								'message' => 'The Commission Percentage field is required for the "X for Y w/ Commission" revenue model.  This value must be a number from 0 to 100.'
								),
						'x' => array(
								'rule' => array('checkPresets'),
								'message' => 'The X field is required for any "X for Y" revenue models.  This value must be an integer.'
								),
						'y' => array(
								'rule' => array('checkPresets'),
								'message' => 'The Y field is required for any "X for Y" revenue models.  This value must be an integer'
								), 
						'expMaxOffers' => array(
								'rule' => array('checkExp'),
								'message' => 'This field is required for the expiration criteria you have selected.'
								), 
						'expDate' => array(
								'checkExp' => array(
											'rule' => array('checkExp'),
											'message' => 'This field is required for the expiration criteria you have selected.'),
								'date' => array(
											'rule' => array('date'),
											'message' => 'Must be a valid date.',
											'allowEmpty' => true)
								),
						);
						
					
	function getLoaEndDate($loaId){

		$q="SELECT endDate FROM loa WHERE loaId=$loaId";
		$row=$this->query($q);
		return ($row[0]['loa']['endDate']);


	}

	function checkExp($data) {
		$validPresets = true;
		$expCriteriaId = $this->data['Track']['expirationCriteriaId'];
		if (isset($data['expMaxOffers']) && $expCriteriaId == 2) {
			$value = $data['expMaxOffers'];
			if (!$value || !is_numeric($value) || floor($value) != $value) {
				$validPresets = false;						
			}			
		} elseif (isset($data['expDate']) && $expCriteriaId == 3) {
			$value = $data['expDate'];
			if (!$value) {
				$validPresets = false;						
			}			
		}
		return $validPresets;
	}					
	
	function checkPresets($data) {
		$validPresets = true;
		$revModelId = $this->data['Track']['revenueModelId'];
		if (isset($data['keepPercentage']) && $revModelId == 1) {
			$value = $data['keepPercentage'];
			if (!is_numeric($value) || !($value >= 0 && $value <= 100)) {
				$validPresets = false;	
			}
		} elseif (isset($data['commissionPercentage']) && $revModelId == 4) {
			$value = $data['commissionPercentage'];
			if (!is_numeric($value) || !($value >= 0 && $value <= 100)) {
				$validPresets = false;	
			}
		} elseif (isset($data['x']) && ($revModelId == 2 || $revModelId == 3 || $revModelId == 4 || $revModelId == 5)) {
			$value = $data['x'];
			if (!$value || !is_numeric($value) || floor($value) != $value) {
				$validPresets = false;						
			}
		} elseif (isset($data['y']) && ($revModelId == 2 || $revModelId == 3 || $revModelId == 4 || $revModelId == 5)) {
			$value = $data['y'];
			if (!$value || !is_numeric($value) || floor($value) != $value) {
				$validPresets = false;						
			}			
		}
		return $validPresets;
	}
	
	function beforeSave($created) {
	    if ($this->data['Track']['expirationCriteriaId'] == 1) {
	        $this->data['Track']['applyToMembershipBal'] = 1;
	    } else {
	        $this->data['Track']['applyToMembershipBal'] = 0;
	    }
	    
	    return true;
	}
	
	function inUse($id) {
		$q = 'SELECT COUNT(*) AS nbr FROM schedulingMasterTrackRel WHERE trackId = ?';
		$result = $this->query($q, array($id));
		if ($result && $result[0][0]['nbr'] > 0) { 
			return true;
		} else {
			return false;
		}

	}	
	
}
?>
