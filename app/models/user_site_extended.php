<?php
App::import('Vendor', 'PasswordHash');

class UserSiteExtended extends AppModel {

	var $name = 'UserSiteExtended';
	var $useTable = 'userSiteExtended';
	var $primaryKey = 'userSiteExtendedId';
	
	var $belongsTo = array('User' => array('foreignKey' => 'userId'));
	
	function afterSave() {
		if (isset($this->data['UserSiteExtended']['userSiteExtendedId'])) {
			// get user from userSiteExtendedId
			$this->id = $this->data['UserSiteExtended']['userSiteExtendedId'];
			$this->read();

			$ch = curl_init();
			$cacheKey = 'UserEntity_1_'.$this->data['UserSiteExtended']['userId'];
			curl_setopt($ch, CURLOPT_URL, "http://live.luxurylink.com/shell/memcache/commands.php?request_command=delete&request_key=$cacheKey&request_duration=&request_data=&request_delay=&request_server=LL&request_api=Server");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_exec($ch);
			curl_close($ch);
		}
	}

	/**
	 * This method runs before a user account is created or saved. For now it just creates a hash from the password field if it is found
	 */
	function beforeSave() {
		/* SEE COMMENTS IN vendors/password_hash.php */
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
