<?php
class ClientAmenityRel extends AppModel {

	var $name = 'ClientAmenityRel';
	var $useTable = 'clientAmenityRel';
	var $primaryKey = 'clientAmenityRelId';
	
	var $belongsTo = array('Amenity' => array('className' => 'Amenity', 'foreignKey' => 'amenityId'),
				     'Client' => array('className' => 'Client', 'foreignKey' => 'clientId'));
	
	var $actsAs = array('Multisite');

	  function save_amenity($data, $sites) {
		    $this->create();
		    $this->data['ClientAmenityRel'] = $data;
		    $this->data['ClientAmenityRel']['sites'] = $sites;
		    $this->set($this->data);
		    $this->save();
	  }
	
}
?>