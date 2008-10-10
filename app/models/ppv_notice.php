<?php
class PpvNotice extends AppModel {

	var $name = 'PpvNotice';
	var $useTable = 'ppvNotice';
	var $primaryKey = 'ppvNoticeId';
	
	var $belongsTo = array('Worksheet' => array('foreignKey' => 'worksheetId'),
						   'PpvNoticeType' => array('foreignKey' => 'ppvNoticeTypeId')
						  );

}
?>