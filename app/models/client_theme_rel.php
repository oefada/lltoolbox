<?php
class ClientThemeRel extends AppModel {

	var $name = 'ClientThemeRel';
	var $useTable = 'clientThemeRel';
	var $primaryKey = 'clientThemeRelId';
	
	var $belongsTo = array('Theme' => array('foreignKey' => 'themeId'),
                           'Client' => array('foreignKey' => 'clientId'));
    
	var $actsAs = array('Containable');
    
    var $multisite = true;
    
    function countThemesSites($clientId) {
        $i = 0;
        $this->recursive = -1;
        $themes = $this->find('all', array('conditions' => array('clientId' => $clientId),
                                           'fields' => 'sites'));
        foreach ($themes as $theme) {
            $i += count($theme['ClientThemeRel']['sites']);
        }
        return $i;
    }

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
