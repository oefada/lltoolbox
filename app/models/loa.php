<?php
class Loa extends AppModel {

	var $name = 'Loa';
	var $useTable = 'loa';
	var $primaryKey = 'loaId';

	var $belongsTo = array('LoaCustomerApprovalStatus' => array('foreignKey' => 'customerApprovalStatusId'),
						   'Client' => array('foreignKey' => 'clientId'),
						   'Currency' => array('foreignKey' => 'currencyId'),
						   'LoaLevel' => array('foreignKey' => 'loaLevelId'),
						   'LoaMembershipType' => array('foreignKey' => 'loaMembershipTypeId')
						  );
	var $validate = array('startDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date'),
							'endDate' => array('rule' => array('validateEndStartDate'), 'message' => 'Start date must be less than end date')
							);
							
	var $order = array("Loa.startDate DESC");

	var $hasMany = array('LoaItem' => array('foreignKey' => 'loaId'), 
						 'ClientLoaPackageRel' => array('foreignKey' => 'loaId'),
						 'Track' => array('foreignKey' => 'loaId')
						);
    
    var $actsAs = array('Containable', 'Multisite' => array('disableWrite' => true));
	  

	function validateEndStartDate()
	{
		$startDate = $this->data[$this->name]['startDate'];
		$endDate = $this->data[$this->name]['endDate'];
		
		if($startDate >= $endDate) {
			return false;
		}
		return true;
	}
	
	function beforeSave($options) {
		if ($this->data['Loa']['loaId']) {
		    $orig = $this->find('Loa.loaId = '.$this->data['Loa']['loaId'], array('customerApprovalStatusId'));
		    
		    if (@$orig['Loa']['customerApprovalStatusId'] != 2 && @$this->data['Loa']['customerApprovalStatusId'] == 2) {
		        $this->data['Loa']['customerApprovalDate'] = date('Y-m-d H:i:s');
		    }			
		}
	    return true;
	}
	
	function afterSave() {
	      $this->Client->set_sites($this->data['Loa']['clientId'], implode(',', $this->data['Loa']['sites']));
	      return;
	}
	
	function get_current_loa($client_id) {
	  return $this->field('loaId', array('Loa.clientId = '.$client_id.' AND now() BETWEEN Loa.startDate AND Loa.endDate'));
	}
}
?>
