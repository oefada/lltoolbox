<?php
class UserReferrals extends AppModel {

	var $name = 'UserReferrals';
	var $useTable = 'userReferrals';
	var $primaryKey = 'id';
	
	var $belongsTo = Array('User' => Array('foreignKey' => 'referrerUserId'));
	
}
?>
