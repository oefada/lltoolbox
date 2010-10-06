<?php
class ClientTracking extends AppModel {

	var $name = 'ClientTracking';
	var $useTable = 'clientTracking';
	var $primaryKey = 'clientTrackingId';
	var $displayField = 'clientTrackingId';
	
	var $belongsTo = array('Client' => array('className' => 'Client', 'foreignKey' => 'clientId'),
                           'Package' => array('className' => 'Package', 'foreignKey' => 'packageId'));
	
	function beforeSave() {
		if (empty($this->data['ClientTracking']['sites'])) {
				$client = $this->Client->find('first', array('conditions' => array('Client.clientId' => $this->data['ClientTracking']['clientId'])));
				if (!empty($client)) {
						$this->data['ClientTracking']['sites'] = $client['Client']['sites'];
				}
                else {
                    if ($package = $this->Package->find('first', array('conditions' => array('Package.packageId' => $this->data['ClientTracking']['packageId'])))) {
                        $this->data['ClientTracking']['sites'] = $package['Package']['sites'];
                    }
                }
		}
		return true;
	}
	
}
?>