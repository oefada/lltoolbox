<?php
class AmenitiesController extends AppController {

	var $name = 'Amenities';
	var $scaffold;
	
	function auto_complete() {
		$amenities = $this->Amenity->find('all', array(
   		'conditions' => array(
   			'Amenity.amenityName LIKE' => $this->data['Client']['amenity_select'].'%',
   			),
			'limit' => 10,
   			'fields' => array('amenityId', 'amenityName')
   			));
   		$this->set('amenities', $amenities);
   		$this->layout = 'ajax';
  	}


	function view_complete_list_compact() {
		$amenities = $this->Amenity->find('list');
		
		$this->set(compact('amenities'));
   		$this->layout = 'ajax';
  	}
   
}
?>