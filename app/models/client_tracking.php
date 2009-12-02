<?php
class ClientTracking extends AppModel {

	var $name = 'ClientTracking';
	var $useTable = 'clientTracking';
	var $primaryKey = 'clientTrackingId';
	var $displayField = 'clientTrackingId';
	
	var $belongsTo = array('Client' => array('className' => 'Client', 'foreignKey' => 'clientId'));
	
	var $actsAs = array('Multisite');
	
	function beforeSave() {
		if (empty($this->data['ClientTracking']['sites'])) {
				$client = $this->Client->find('first', array('conditions' => array('Client.clientId' => $this->data['ClientTracking']['clientId'])));
				if (!empty($client)) {
						$this->data['ClientTracking']['sites'] = $client['Client']['sites'];
				}
		}
		return true;
	}
	
}
?>