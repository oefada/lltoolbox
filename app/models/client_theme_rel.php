<?php
class ClientThemeRel extends AppModel {

	var $name = 'ClientThemeRel';
	var $useTable = 'clientThemeRel';
	var $primaryKey = 'clientThemeRelId';
	
	var $belongsTo = array('Theme' => array('foreignKey' => 'themeId'));
	var $actsAs = array('Multisite');

	function saveThemes($data) {
		$this->useDbConfig = 'default';
		foreach($data as $themeId => $details) {
			$this->create();
			$this->recursive = -1;
			$this->save($details);
		}
	}
	
	function deleteAllFromFrontEnd($client_id, $sites) {
		foreach ($sites as $site) {
			$this->useDbConfig = $site;
			$this->deleteAll(array('ClientThemeRel.clientId' => $client_id));
		}
	}
}
?>
