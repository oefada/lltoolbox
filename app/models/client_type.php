<?php
class ClientType extends AppModel {

	var $name = 'ClientType';
	var $useTable = 'clientType';
	var $primaryKey = 'clientTypeId';
	var $displayField = 'clientTypeName';

    public $hasMany = array('ClientTypeHistory' => array('className' => 'ClientTypeHistory', 'foreignKey' => 'clientId'));
}
?>