<?php
class MailingList extends AppModel {

	var $name = 'MailingList';
	var $useTable = 'mailingList';
	var $primaryKey = 'mailingListId';
	var $displayField = 'mailListName';

	var $hasMany = array('UserMailOptin' => array('foreignKey' => 'mailingListId'));
}
?>
