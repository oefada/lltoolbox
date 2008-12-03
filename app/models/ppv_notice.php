<?php
class PpvNotice extends AppModel {

	var $name = 'PpvNotice';
	var $useTable = 'ppvNotice';
	var $primaryKey = 'ppvNoticeId';
	
	var $belongsTo = array(//'Ticket' => array('foreignKey' => 'ticketId'),
						   'PpvNoticeType' => array('foreignKey' => 'ppvNoticeTypeId')
						  );

}
?>