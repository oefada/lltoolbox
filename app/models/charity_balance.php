<?php
class CharityBalance extends AppModel {

	var $name = 'CharityBalance';
	var $useTable = 'charityBalance';
	var $primaryKey = 'charityBalanceId';
	
	var $belongsTo = array('Charity' => array('foreignKey' => 'charityId'));

}
?>
