<?php
class Loa extends AppModel {

	var $name = 'Loa';
	var $useTable = 'loa';
	var $primaryKey = 'loaId';

	var $belongsTo = array('LoaCustomerApprovalStatus' => array('foreignKey' => 'customerApprovalStatusId'),
						   'Client' => array('foreignKey' => 'clientId')
						  );
	var $validate = array('startDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date'),
							'endDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date')
							);
							
	var $order = array("Loa.startDate DESC");

	var $hasMany = array('LoaItem' => array('foreignKey' => 'loaId'), 
						 'ClientLoaPackageRel' => array('foreignKey' => 'loaId'),
						 'RevenueModelLoaRel' => array('foreignKey' => 'loaId')
						);

	function validateEndStartDate()
	{
		$startDate = $this->data[$this->name]['startDate'];
		$endDate = $this->data[$this->name]['endDate'];
		
		if($startDate >= $endDate) {
			return false;
		}
		return true;
	}
}
?>
