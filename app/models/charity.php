<?php
class Charity extends AppModel {

	var $name = 'Charity';
	var $useTable = 'charity';
	var $primaryKey = 'charityId';
	var $displayField = 'charityName';
	
	var $hasMany = array('CharityBalance' => array('foreignKey' => 'charityId'));
	
}
?>
