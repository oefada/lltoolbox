<?php
class PpvNoticeType extends AppModel {

	var $name = 'PpvNoticeType';
	var $useTable = 'ppvNoticeType';
	var $primaryKey = 'ppvNoticeTypeId';
	var $displayField = 'ppvNoticeTypeName';
	
	var $hasOne = array('PpvNoticeClickTrack' => array('foreignKey' => 'ppvNoticeTypeId'));
}
?>