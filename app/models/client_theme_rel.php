<?php
class ClientThemeRel extends AppModel {

	var $name = 'ClientThemeRel';
	var $useTable = 'clientThemeRel';
	var $primaryKey = 'clientThemeRelId';

	var $belongsTo = array('Theme' => array('foreignKey' => 'themeId'));
	var $actsAs = array('Multisite');
	
	function saveThemes($data) {
		foreach($data as $themeId => $details) {
			$this->create();
			$this->recursive = -1;
			$theme = $this->find('first', array('conditions' => array('ClientThemeRel.clientId' => $details['ClientThemeRel']['clientId'], 'ClientThemeRel.themeId' => $details['ClientThemeRel']['themeId'])));
			if (!empty($theme)) {
				$this->id = $theme['ClientThemeRel']['clientThemeRelId'];
				$details['ClientThemeRel']['clientThemeRelId'] = $theme['ClientThemeRel']['clientThemeRelId'];
			}
			$this->save($details);
		}
	}
}
?>
