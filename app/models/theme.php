<?php
class Theme extends AppModel {

	var $name = 'Theme';
	var $useTable = 'theme';
	var $primaryKey = 'themeId';
	var $displayField = 'themeName';
    
    var $actsAs = array('Containable');
    
    var $hasMany = array('ClientThemeRel' => array('className' => 'ClientThemeRel', 'foreignKey' => 'themeId'));
    
    function findClientThemes($clientId) {
        $this->contain('ClientThemeRel.clientId = '.$clientId);
        $themes = $this->find('all', array('order' => 'Theme.themeName', 'fields' => array('Theme.themeId', 'Theme.themeName')));
        return $themes;
    }
}
?>
