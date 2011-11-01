<?php
class State extends AppModel {

	var $name           = 'State';
	var $useTable       = 'stateNew';
	var $primaryKey     = 'stateId';
	var $displayField   = 'stateName';
	var $order          = 'stateName';
	
	var $belongsTo = array('Country' => array('foreignKey' => 'countryId'));
	var $hasMany = array('City' => array('foreignKey' => 'stateId'));
	
	var $validate = array(
		'stateName' => array(
			VALID_NOT_EMPTY,
		),
		'stateCode' => array(
			VALID_NOT_EMPTY,
		),
		'countryId' => array(
			VALID_NOT_EMPTY,
		)
	);
	
	/**
	 * Gets OUR auto increment state ID in relation to geonames state ID/Code stored in DB. Fix this later.
	 */
	public function getStateCode($id) {
		$this->recursive = -1;
		
		$result = $this->find('first',array('conditions' => array('id' => $id)));
		return array($result['State']['stateId'],$result['State']['countryId']);
	}
}
?>