<?php
class ClientTagRel extends AppModel {

	var $name = 'ClientTagRel';
	var $useTable = 'clientTagRel';
	var $primaryKey = 'clientTagRelId';
	
	var $belongsTo = array('ClientTag' => array('foreignKey' => 'clientTagId'),
                           'Client' => array('foreignKey' => 'clientId'));
    
	var $actsAs = array('Containable');
    
    
	function saveTags($data) {
		$this->useDbConfig = 'default';
		foreach($data as $tag => $details) {
			$this->create();
			$this->recursive = -1;
			$this->save($details);
		}
	}
	
	function deleteAllByClient($client_id) {
		$this->deleteAll(array('ClientTagRel.clientId' => $client_id));
	}
}
?>
