<?php
class ClientThemeRel extends AppModel {

	var $name = 'ClientThemeRel';
	var $useTable = 'clientThemeRel';
	var $primaryKey = 'clientThemeRelId';

	var $belongsTo = array('Theme' => array('foreignKey' => 'themeId'));

}
?>
