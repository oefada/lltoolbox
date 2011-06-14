<?php
class Loa extends AppModel {

	var $name = 'Loa';
	var $useTable = 'loa';
	var $primaryKey = 'loaId';

	var $belongsTo = array('LoaCustomerApprovalStatus' => array('foreignKey' => 'customerApprovalStatusId'),
						   'Client' => array('foreignKey' => 'clientId'),
						   'Currency' => array('foreignKey' => 'currencyId'),
						   'LoaLevel' => array('foreignKey' => 'loaLevelId'),
						   'LoaMembershipType' => array('foreignKey' => 'loaMembershipTypeId'),
						   'AccountType' => array('foreignKey' => 'accountTypeId')
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

		$this->data['Loa']['modified'] = date('Y-m-d h:i:s');
		if(isset($this->data['Loa']['loaId'])) {
			$this->saveLoaStatuses('PublishingStatusLL', 'LoaPublishingStatusRelLL', 'luxurylink');
			$this->saveLoaStatuses('PublishingStatusFG', 'LoaPublishingStatusRelFG', 'family');
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

    private function saveLoaStatuses($siteArrayIndex, $siteSaveArrayIndex, $site) {
    	if (!empty($this->data['Loa'][$siteArrayIndex])) {
			for ($i = 0; $i < 5; $i++) {
				$pStatus = $i + 1;
				// Let's see if we have a status already set in the database
				$thisStatus = $this->LoaPublishingStatusRel->find
	                		('first', array(
	                			'conditions' => array(
									'LoaPublishingStatusRel.loaId' => $this->data['Loa']['loaId'],
									'LoaPublishingStatusRel.publishingStatusId' => $pStatus, 
	                				'LoaPublishingStatusRel.site' => $site)));
				// If this status was saved before and is still selected by user just remember the selection
				if ($thisStatus && in_array($pStatus, $this->data['Loa'][$siteArrayIndex])) {
					// Remember what was saved in the database
					$this->data[$siteSaveArrayIndex][$i] = $thisStatus;
				}
				// If this status was not saved before but selected by user this time create new status
				else if(in_array($pStatus, $this->data['Loa'][$siteArrayIndex])) {
					$this->data[$siteSaveArrayIndex][$i]['loaId'] = $this->data['Loa']['loaId'];
					$this->data[$siteSaveArrayIndex][$i]['publishingStatusId'] = $pStatus;
					$this->data[$siteSaveArrayIndex][$i]['completedDate'] = date('Y-m-d H:i:s');
					$this->data[$siteSaveArrayIndex][$i]['site'] = $site;
				}
				// Otherwise clear the status
				else {
					$this->LoaPublishingStatusRel->deleteAll(array(
	                    						'LoaPublishingStatusRel.loaId' => $this->data['Loa']['loaId'], 
	                    						'LoaPublishingStatusRel.site' => $site,
	                    						'LoaPublishingStatusRel.publishingStatusId' => $thisStatus['LoaPublishingStatusRel']['publishingStatusId'])
					);
				}
			}
		} else {
			// Clear the statues
			$this->LoaPublishingStatusRel->deleteAll(array(
	                    						'LoaPublishingStatusRel.loaId' => $this->data['Loa']['loaId'], 
	                    						'LoaPublishingStatusRel.site' => $site));
		}
    }
}
?>
