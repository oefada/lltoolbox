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
}
?>