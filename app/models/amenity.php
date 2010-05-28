<?php
class Amenity extends AppModel {

	var $name = 'Amenity';
	var $useTable = 'amenity';
	var $primaryKey = 'amenityId';
	var $displayField = 'amenityName';
	
	var $hasMany = array(
	   'ClientAmenityRel' => array('className' => 'ClientAmenityRel', 'foreignKey' => 'amenityId')
	   );
       
   var $belongsTo = array('amenityType' => array('foreignKey' => 'amenityTypeId'));

}
?>
