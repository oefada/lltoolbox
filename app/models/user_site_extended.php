<?php
App::import('Vendor', 'PasswordHash');

class UserSiteExtended extends AppModel {

	var $name = 'UserSiteExtended';
	var $useTable = 'userSiteExtended';
	var $primaryKey = 'userSiteExtendedId';
	
	var $belongsTo = array('User' => array('foreignKey' => 'userId'));
	
	/**
	 * This method runs before a user account is created or saved. For now it just creates a hash from the password field if it is found
	 */
	function beforeSave() {
		$hasher = new PasswordHash(10, false);
		
		//we'll take a new password for the password field, hash it and put it in the passwordHash field
		if (!empty($this->data['UserSiteExtended']['password']) &&
			strpos($this->data['UserSiteExtended']['password'], '$2a$') !== 0 //only do this if the password doesn't already look like a hash
			) {
			$this->data['UserSiteExtended']['passwordHash'] = $hasher->HashPassword($this->data['UserSiteExtended']['password']);
			
		//if the new password is in the passwordHash variable, we do the same, but the password variable above wins
		} else if(!empty($this->data['UserSiteExtended']['passwordHash']) && strpos($this->data['UserSiteExtended']['passwordHash'], '$2a$') !== 0) {
			$this->data['UserSiteExtended']['passwordHash'] = $hasher->HashPassword($this->data['UserSiteExtended']['passwordHash']);
		}
		
		return true;
	}
}
?>