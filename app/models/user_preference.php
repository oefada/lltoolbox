<?php
class UserPreference extends AppModel {

	var $name = 'UserPreference';
	var $useTable = 'userPreference';
	var $primaryKey = 'userPreferenceId';

	var $belongsTo = array('PreferenceType' => array('foreignKey' => 'preferenceTypeId'));
}
?>
