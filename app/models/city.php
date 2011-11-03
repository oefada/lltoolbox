<?php
class City extends AppModel {

	var $name           = 'City';
	var $useTable       = 'cityNew';
	var $primaryKey     = 'cityId';
	var $displayField   = 'cityName';
	var $order          = 'cityName';
	var $actsAs = array('Containable');
	
	var $belongsTo = array('State' => array('foreignKey' => false, 'conditions' => 'City.stateId = State.stateId AND City.countryId = State.countryId'),
				'Country' => array('foreignKey' => 'countryId'));
	
	var $validate = array(
		'cityName' => array(
			VALID_NOT_EMPTY,
		),
		'latitude' => array(
			VALID_NOT_EMPTY,
		),
		'longitude' => array(
			VALID_NOT_EMPTY,
		),
		'countryId' => array(
			VALID_NOT_EMPTY,
		)
	);

	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
	    return $this->find('count',array('fields' => 'cityId'));
	}

}
?>