<?php
class CountryBilling extends AppModel {

	var $name = 'CountryBilling';
	var $useTable = 'country';
	var $primaryKey = 'countryId';
	var $displayField = 'countryName';
	var $order      = 'countryName';
					
	public function getCountryCode($id) {
		$this->recursive = -1;
		
		$result = $this->find('first',array('conditions' => array('id' => $id)));
		return $result['Country']['countryCode'];
	}

	public function getList() {
		$result = $this->find('all');
		$list = array('US'=>'United States');
		foreach ($result as $r) {
			if ($r['CountryBilling']['countryCode'] != '--') {
				$list[$r['CountryBilling']['countryCode']] = $r['CountryBilling']['countryName'];
			}
		}
		return $list;
	}

}

