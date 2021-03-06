<?php
class ClientAmenityRel extends AppModel {

		var $name = 'ClientAmenityRel';
		var $useTable = 'clientAmenityRel';
		var $primaryKey = 'clientAmenityRelId';
		
		var $belongsTo = array('Amenity' => array('className' => 'Amenity', 'foreignKey' => 'amenityId'),
								'Client' => array('className' => 'Client', 'foreignKey' => 'clientId')
								);
        
        var $multisite = true;
        var $inheritsFrom = array('modelName' => 'Client', 'siteField' => 'sites');
		
		function save_amenity($data, $sites) {
            $this->useDbConfig = 'default';
            $this->create();
            $this->data['ClientAmenityRel'] = $data;
            $this->data['ClientAmenityRel']['sites'] = $sites;
            if (!isset($this->data['ClientAmenityRel']['clientAmenityRelId'])) {
                $am = $this->query("SELECT * FROM clientAmenityRel WHERE clientId = {$this->data['ClientAmenityRel']['clientId']} AND  amenityId = {$this->data['ClientAmenityRel']['amenityId']}");
                if (!empty($am)) {
                    $this->data['ClientAmenityRel']['clientAmenityRelId'] = $am[0]['clientAmenityRel']['clientAmenityRelId'];
                }		
            }
            $this->set($this->data);
            $this->save();
		}
		
		function deleteAllFromFrontEnd($client_id, $sites) {
            foreach ($sites as $site) {
                $this->useDbConfig = $site;
                $this->deleteAll(array('ClientAmenityRel.clientId' => $client_id));
            }
		}
}
?>