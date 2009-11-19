<?php
class Amenity extends AppModel {

	var $name = 'Amenity';
	var $useTable = 'amenity';
	var $primaryKey = 'amenityId';
	var $displayField = 'amenityName';
	//
	//var $hasMany = array(
	//   'ClientAmenityRel' => array('className' => 'ClientAmenityRel', 'foreignKey' => 'amenityId')
	//   );
	
	var $hasAndBelongsToMany = array('Client' => array('foreignKey' => 'amenityId',
									   'associationForeignKey' => 'clientId',
									   'with' => 'ClientAmenityRel'
									   ));
	
	var $actsAs = array('Multisite');

}
?>
