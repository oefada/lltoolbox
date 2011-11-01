<?php
class Country extends AppModel {

	var $name = 'Country';
	var $useTable = 'countryNew';
	var $primaryKey = 'countryId';
	var $displayField = 'countryName';
	
	var $hasMany = array('State' => array('foreignKey' => 'countryId'),
						'City' => array('foreignKey' => 'countryId'),
						);
						
						
	public function getCountryCode($id) {
		$this->recursive = -1;
		
		$result = $this->find('first',array('conditions' => array('id' => $id)));
		return $result['Country']['countryId'];
	}
}

