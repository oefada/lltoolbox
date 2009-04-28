<?php
class Track extends AppModel {

	var $name = 'Track';
	var $useTable = 'track';
	var $primaryKey = 'trackId';
	var $displayField = 'trackName';
	
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
		} elseif (isset($data['x']) && ($revModelId == 2 || $revModelId == 3)) {
			$value = $data['x'];
			if (!$value || !is_numeric($value) || floor($value) != $value) {
				$validPresets = false;						
			}
		} elseif (isset($data['y']) && ($revModelId == 2 || $revModelId == 3)) {
			$value = $data['y'];
			if (!$value || !is_numeric($value) || floor($value) != $value) {
				$validPresets = false;						
			}			
		}
		return $validPresets;
	}
}
?>