<?php
class LoaPublishingStatusRel extends AppModel {

	var $name = 'LoaPublishingStatusRel';
	var $useTable = 'loaPublishingStatusRel';
	var $primaryKey = 'loaPublishingStatusRelId';
	
	var $belongsTo = array('Loa' => array('foreignKey' => 'loaId'),
                           'PublishingStatus' => array('foreignKey' => 'publishingStatusId'));
    
	var $actsAs = array('Containable');
    
}
    
?>