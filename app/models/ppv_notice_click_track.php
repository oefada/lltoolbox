<?php
class PpvNoticeClickTrack extends AppModel {

	var $name = 'PpvNoticeClickTrack';
	var $useTable = 'ppvNoticeClickTrack';
	var $primaryKey = 'ppvNoticeClickTrackId';
	
	var $belongsTo = array('PpvNoticeType' => array('foreignKey' => 'ppvNoticeTypeId'));
}
?>