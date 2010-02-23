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
    
    var $actsAs = array('Containable');
	
    var $multisite = true;

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
		if (isset($this->data['Loa']['loaId'])) {
		    $orig = $this->find('Loa.loaId = '.$this->data['Loa']['loaId'], array('customerApprovalStatusId'));
		    
		    if (@$orig['Loa']['customerApprovalStatusId'] != 2 && @$this->data['Loa']['customerApprovalStatusId'] == 2) {
		        $this->data['Loa']['customerApprovalDate'] = date('Y-m-d H:i:s');
		    }			
		}
        AppModel::beforeSave();
	    return true;
	}
	
	function afterSave() {
          if ($this->id == $this->get_current_loa($this->data['Loa']['clientId'])) {
                $this->Client->set_sites($this->data['Loa']['clientId'], $this->data['Loa']['sites']);
          }
	      return;
	}
    
    //override AppModel::afterDelete()
    function afterDelete() {
        return;
    }
	
    function get_current_loa($client_id) {
        $this->Loa->recursive = -1;
         $currentLoaId = $this->field('loaId', array('Loa.clientId = '.$client_id.' AND now() BETWEEN Loa.startDate AND Loa.endDate'));
         if (empty($currentLoaId)) {
           $this->Client->recursive = -1;
           $client = $this->Client->findByClientId($client_id);
           if (empty($client['Client']['parentClientId'])) {
               $currentLoaId = $this->field('loaId', array('Loa.clientId = '.$client_id.' AND Loa.loaLevelId = 0 AND now() < Loa.startDate'));
           }
           else {
               $currentLoaId = $this->field('loaId', array('Loa.clientId ='.$client['Client']['parentClientId'].' AND now() BETWEEN Loa.startDate AND Loa.endDate'));
           }
           if (empty($currentLoaId)) {
               $currentLoaId = 0;
           }
         }
         return $currentLoaId;
    } 
}
?>
