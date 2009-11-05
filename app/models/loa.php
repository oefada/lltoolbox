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
	      return;
		if (isset($this->data['Loa']['clientId'])) {
			// if any update to LOA, update the client record also so it gets update on live site for clients
			/*$client = new Client();
			$client->recursive = -1;
			$clientData = $client->read(null, $this->data['Loa']['clientId']);

			if ($clientData['Client']['currentLoaId'] == $this->data['Loa']['loaId'] && !empty($this->data['Loa']['sites'])) {
				$clientData['Client']['sites'] = $this->data['Loa']['sites'];
			} else {
				$clientData['Client']['sites'] = null;
			}

			$clientData['Client']['modified'] = date('Y-m-d H:i:s', strtotime('now'));
			$client->save($clientData['Client']);*/
			$client_id = $this->data['Loa']['clientId'];
			$loa_id = $this->data['Loa']['loaId'];
			$sites = $this->data['Loa']['sites'];
			
			$current_loa = $this->get_current_loa($client_id);
			$this->Client->recursive = -1;
			$data = $this->Client->read(null, $client_id);
		      if ($current_loa == $loa_id && !empty($sites)) {
				$data['Client']['modified'] = date('Y-m-d H:i:s', strtotime('now'));
				$data['Client']['sites'] = $sites;
				//These if statements are necessary to prevent Cake from inserting blank Client records to the toolbox db
				if (empty($data['ParentClient']['clientId'])) {
					  unset($data['ParentClient']);
					  $this->Client->unbindModel(array('belongsTo' => array('ParentClient')), $reset=false);
				}
				if (empty($data['ChildClient']['clientId'])) {
					  unset($data['ChildClient']);
					  $this->Client->unbindModel(array('hasMany' => array('ChildClient')), $reset=false);
				}
				//$this->Client->save($data,array('validate' => false));
			}
		}
	}
	
	function get_current_loa($client_id) {
	  return $this->field('loaId', array('Loa.clientId = '.$client_id.' AND now() BETWEEN Loa.startDate AND Loa.endDate'));
	}
}
?>
