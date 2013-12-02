<?php
class ClientTag extends AppModel {

	var $name = 'ClientTag';
	var $useTable = 'clientTag';
	var $primaryKey = 'clientTagId';
	var $displayField = 'description';
    
    var $actsAs = array('Containable');
    
    var $hasMany = array('ClientTagRel' => array('className' => 'ClientTagRel', 'foreignKey' => 'clientTagId'));
    
}
?>
