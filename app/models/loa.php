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
						 'Track' => array('foreignKey' => 'loaId'),
                         'LoaPublishingStatusRel' => array('foreignKey' => 'loaId')
						);
    
    //var $hasAndBelongsToMany = array('PublishingStatus' => array('className' => 'PublishingStatus',
    //                                                             'joinTable' => 'LoaPublishingStatusRel',
    //                                                             'foreignKey' => 'loaId',
    //                                                             'associationForeignKey' => 'publishingStatusId',
    //                                                             'with' => 'LoaPublishingStatusRel'));
    
    var $actsAs = array('Containable', 'Logable');
	
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

		if(isset($this->data['Loa']['loaId'])) {
			$this->LoaPublishingStatusRel->deleteAll(array('LoaPublishingStatusRel.loaId' => $this->data['Loa']['loaId']));
			if (!empty($this->data['Loa']['PublishingStatusLL'])) {
	            foreach ($this->data['Loa']['PublishingStatusLL'] as $i => $pStatus) {
	                if ($thisStatus = $this->LoaPublishingStatusRel->find('first', array('conditions' => array('LoaPublishingStatusRel.loaId' => $this->data['Loa']['loaId'],
	                                                                                                           'LoaPublishingStatusRel.publishingStatusId' => $pStatus)))) {
	                    $this->data['LoaPublishingStatusRelLL'][$i] = $thisStatus;
	                }
	                else {
	                    $this->data['LoaPublishingStatusRelLL'][$i]['loaId'] = $this->data['Loa']['loaId'];
	                    $this->data['LoaPublishingStatusRelLL'][$i]['publishingStatusId'] = $pStatus;
	                    $this->data['LoaPublishingStatusRelLL'][$i]['completedDate'] = date('Y-m-d H:i:s');
	                    $this->data['LoaPublishingStatusRelLL'][$i]['site'] = 'luxurylink';
	                }
	            }
	        }
			if (!empty($this->data['Loa']['PublishingStatusFG'])) {
	            foreach ($this->data['Loa']['PublishingStatusFG'] as $i => $pStatus) {
	                if ($thisStatus = $this->LoaPublishingStatusRel->find('first', array('conditions' => array('LoaPublishingStatusRel.loaId' => $this->data['Loa']['loaId'],
	                                                                                                           'LoaPublishingStatusRel.publishingStatusId' => $pStatus)))) {
	                    $this->data['LoaPublishingStatusRelFG'][$i] = $thisStatus;
	                }
	                else {
	                    $this->data['LoaPublishingStatusRelFG'][$i]['loaId'] = $this->data['Loa']['loaId'];
	                    $this->data['LoaPublishingStatusRelFG'][$i]['publishingStatusId'] = $pStatus;
	                    $this->data['LoaPublishingStatusRelFG'][$i]['completedDate'] = date('Y-m-d H:i:s');
	                    $this->data['LoaPublishingStatusRelFG'][$i]['site'] = 'family';
	                }
	            }
	        }
		}
        unset($this->data['Loa']['PublishingStatus']);
        AppModel::beforeSave();
	    return true;
	}
	
	function afterSave() {
		if ($this->id == $this->get_current_loa($this->data['Loa']['clientId'])) {
//			$this->Client->set_sites($this->data['Loa']['clientId'], $this->data['Loa']['sites']);
		}
		if(isset($this->data['Loa']['loaId'])) {
			if (!empty($this->data['LoaPublishingStatusRelLL'])) {
				foreach($this->data['LoaPublishingStatusRelLL'] as $pStatus) {
					$this->LoaPublishingStatusRel->create();
					$this->LoaPublishingStatusRel->save($pStatus);
				}
			}
			if (!empty($this->data['LoaPublishingStatusRelFG'])) {
				foreach($this->data['LoaPublishingStatusRelFG'] as $pStatus) {
					$this->LoaPublishingStatusRel->create();
					$this->LoaPublishingStatusRel->save($pStatus);
				}
			}
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
    
    function getClientLoas($clientId) {
        if ($loas = $this->query("SELECT * FROM loa Loa WHERE Loa.clientId = {$clientId} ORDER BY Loa.startDate DESC")) {
            return $loas;
        }
        else {
            if ($client = $this->query("SELECT Client.parentClientId FROM client Client WHERE Client.clientId = {$clientId}")) {
                if ($loas = $this->query("SELECT * FROM loa Loa WHERE Loa.clientId = {$client[0]['Client']['parentClientId']} ORDER BY Loa.startDate DESC")) {
                    return $loas;
                }
            }
        }
        return array();
    }
    
    function getLoaClientId($loaId) {
        $query = "SELECT clientId FROM loa Loa WHERE Loa.loaId = {$loaId}";
        if ($clientId = $this->query($query)) {
            return $clientId[0]['Loa']['clientId'];
        }
    }
    
    function getLoaOptionList($clientId) {
        $query = "SELECT loaId, startDate, endDate FROM loa Loa
                  WHERE clientId = {$clientId} AND Loa.endDate > NOW()";
        $list = array();
        if ($loas = $this->query($query)) {
            foreach ($loas as $loa) {
                $list[$loa['Loa']['loaId']] = $loa['Loa']['loaId'] . ': ' . date('M j, Y', strtotime($loa['Loa']['startDate'])) . ' - ' . date('M j, Y', strtotime($loa['Loa']['endDate']));
            }
        }
        return $list;
    }    
}
?>
