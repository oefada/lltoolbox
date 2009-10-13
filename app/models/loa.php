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
    
    var $actsAs = array('Containable', 'Logable');

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
		    
		    if (@$orig['Loa']['customerApprovalStatusId'] != 2 && $this->data['Loa']['customerApprovalStatusId'] == 2) {
		        $this->data['Loa']['customerApprovalDate'] = date('Y-m-d H:i:s');
		    }			
		}
	    return true;
	}
	
	function afterSave() {
		if (isset($this->data['Loa']['clientId'])) {
			// if any update to LOA, update the client record also so it gets update on live site for clients
			$client = new Client();
			$clientData = $client->read(null, $this->data['Loa']['clientId']);
			$clientData['Client']['modified'] = date('Y-m-d H:i:s', strtotime('now'));
			$client->save($clientData['Client']);	
		}
	}
}
?>
