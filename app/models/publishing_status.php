<?php
class PublishingStatus extends AppModel {

	var $name = 'PublishingStatus';
	var $useTable = 'publishingStatus';
	var $primaryKey = 'publishingStatusId';
	var $displayField = 'publishingStatusName';
    
    var $actsAs = array('Containable');
    
    var $hasMany = array('LoaPublishingStatusRel' => array('className' => 'LoaPublishingStatusRel', 'foreignKey' => 'publishingStatusId'));
    
}
    
?>