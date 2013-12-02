<?php
class DealAlert extends AppModel {

	var $name = 'DealAlert';
	var $useTable = 'dealAlert';
	var $primaryKey = 'dealAlertId';
	
	var $belongsTo = array('User' => array('foreignKey' => 'userId'));
}
?>
