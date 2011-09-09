<?php
class City extends AppModel {

	var $name           = 'City';
	var $useTable       = 'cityNew';
	var $primaryKey     = 'cityId';
	var $displayField   = 'cityName';
	var $order          = 'cityName';
	
	var $belongsTo = array('State' => array('foreignKey' => 'stateId'),
						   'Country' => array('foreignKey' => 'countryId')
						  );

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

}
?>