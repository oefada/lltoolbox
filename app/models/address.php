<?php
class Address extends AppModel {

	var $name = 'Address';
	var $useTable = 'address';
	var $primaryKey = 'addressId';

	var $belongsTo = array('AddressType' => array('foreignKey' => 'addressTypeId'),
						   'User' => array('foreignKey' => 'userId'),
						   'Country' => array('foreignKey' => 'countryId'),
						   'State' => array('foreignKey' => 'stateId'),
						   'City' => array('foreignKey' => 'cityId')
						  );

	
}
?>
