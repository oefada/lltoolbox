<?php
class RevenueModelLoaRel extends AppModel {

	var $name = 'RevenueModelLoaRel';
	var $useTable = 'revenueModelLoaRel';
	var $primaryKey = 'revenueModelLoaRelId';
	
	var $belongsTo = array('ExpirationCriterium' => array('foreignKey' => 'expirationCriteriaId'),
						   'RevenueModel' => array('foreignKey' => 'revenueModelId')
						  );
						  
	var $hasMany = array('RevenueModelLoaRelDetail' => array('foreignKey' => 'revenueModelLoaRelId'));
}
?>