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

			$key=0;
			if (isset($this->data['ClientTracking'][5]))$key=5;
			else if (isset($this->data['ClientTracking'][6]))$key=6;

			if ($key>0){

				$arr=array('conditions' => array('Client.clientId' => $this->data['ClientTracking'][$key]['clientId']));

			}else{

				$arr=array('conditions' => array('Client.clientId' => $this->data['ClientTracking']['clientId']));

			}


			$client = $this->Client->find('first', $arr);

				if (!empty($client)) {

					$this->data['ClientTracking']['sites'] = $client['Client']['sites'];

				}else {

					$arr=array('conditions' => array('Package.packageId' => $this->data['ClientTracking']['packageId']));
					if ($package = $this->Package->find('first', $arr)) {
						$this->data['ClientTracking']['sites'] = $package['Package']['sites'];
					}

				}


		}

		return true;

	}
	
}
?>
