<?php
class ClientContact extends AppModel {

	var $name = 'ClientContact';
	var $useTable = 'clientContact';
	var $primaryKey = 'clientContactId';
	var $displayField = 'name';
	var $order = array('ClientContact.contactTypeId');

	var $belongsTo = array(
						   'Client' => array('className' => 'Client', 'foreignKey' => 'clientId')
					 );


}
?>
