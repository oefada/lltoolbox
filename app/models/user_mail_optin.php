<?php
class UserMailOptin extends AppModel {

	var $name = 'UserMailOptin';
	var $useTable = 'userMailOptin';
	var $primaryKey = 'userMailOptinId';
	
	var $belongsTo = array('MailingList' => array('foreignKey' => 'mailingListId'),
						   'User' => array('foreignKey' => 'userId')
						  );

}
?>
