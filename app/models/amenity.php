<?php
class Amenity extends AppModel {

	var $name = 'Amenity';
	var $useTable = 'amenity';
	var $primaryKey = 'amenityId';
	var $displayField = 'amenityName';

	var $belongsTo = array('AmenityType' => array('foreignKey' => 'amenityTypeId'));
    
	var $hasAndBelongsToMany = array(
								'Client' =>
	                               array('className'    => 'Client',
	                                     'joinTable'    => 'clientAmenityRel',
	                                     'foreignKey'   => 'amenityId',
	                                     'associationForeignKey'=> 'clientId'
	                               )
								);
}
?>
