<?php
class UserReferral extends AppModel {

	var $name = 'UserReferral';
	var $useTable = 'userReferral';
	var $primaryKey = 'userReferralId';

	var $belongsTo = array('User' => array('foreignKey' => 'userId'));
}
?>
