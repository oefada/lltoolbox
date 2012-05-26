<?php
class User extends AppModel
{
	public $name = 'User';
	public $useTable = 'user';
	public $primaryKey = 'userId';

	public $validate = array(	
		'email' => array(
			'rule' => 'email',
			'message' => 'Invalid email address.'
		)
	);

	public $belongsTo = array(
		'Salutation' => array(
			'foreignKey' => 'salutationId'
		)
	);

	public $hasOne = array(
		'UserSiteExtended' => array(
			'foreignKey' => 'userId',
			'dependent' => true			
		)
	);

	public $hasMany = array(
		'UserMailOptin' => array(
			'foreignKey' => 'userId',
			'dependent' => true
		),
		'UserPaymentSetting' => array(
			'foreignKey' => 'userId',
			'dependent' => true
		),
		'UserPreference' => array(
			'foreignKey' => 'userId',
			'dependent' => true
		),
		'Bid' => array(
			'foreignKey' => 'userId',
			'dependent' => true
		),
		'Address' => array(
			'foreignKey' => 'userId',
			'dependent' => true
		),
		'UserAcquisitionSource' => array(
			'foreignKey' => 'userAcquisitionSourceId',
			'dependent' => true
		),
		'Ticket' => array(
			'foreignKey' => 'userId',
			//'dependent' => true
		),
		'UserReferrals' => array(
			'foreignKey' => 'referrerUserId',
			'dependent' => true
		)
	);

	public $hasAndBelongsToMany = array(
		'Contest' => 
			array(
				'className' => 'Contest',
				'joinTable' => 'contestUserRel',
				'foreignKey' => 'userId',
				'associationForeignKey' => 'contestId'
			)
	);
	
	/**
	 * Determine if a user exists with userId=$userId
	 * 
	 * @param	int userId
	 * @return	bool
	 */
	public function userExists($userId)
	{
		return ($this->findByUserId($userId) === FALSE) ? FALSE : TRUE;
	}
	
	/**
	 * Delete a user and related records
	 * 
	 * @param	int userId
	 * @param	bool cascade
	 * @return	bool
	 */
	public function deleteUserById($userId, $cascade = true)
	{
		$this->id = $userId;
		$userEmail = mysql_real_escape_string($this->field('email'));

		if ($this->delete($userId) === TRUE) {
			if ($cascade === TRUE) {
				// Delete all tickets and related ticket records for user
				$this->Ticket->deleteAll(array('Ticket.userId' => $userId));
				
				// Delete user bid records
				$this->query("DELETE FROM bid WHERE bid.userId=$userId");
				
				// Delete contestUserRel records
				$this->query("DELETE FROM contestUserRel WHERE contestUserRel.userId=$userId");
				
				// Delete lltUser, lltUserEvent, and lltUserPref data
				/*
				$this->query("DELETE FROM lltUserEvent WHERE lltUserEvent.lltUserId IN (SELECT lltUserId FROM lltUser WHERE sharedUserId=$userId)");
				$this->query("DELETE FROM lltUserPref WHERE lltUserPref.lltUserId IN (SELECT lltUserId FROM lltUser WHERE sharedUserId=$userId)");
				$this->query("DELETE FROM lltUser WHERE lltUser.sharedUserId=$userId");
				*/
				
				// Delete community data
				/*$this->setDataSource('community');
				$communityUserId = $this->query("SELECT userid FROM user WHERE user.email=\"$userEmail\"");
				$communityUserId = isset($communityUserId[0]['user']['userid']) ? (int) $communityUserId[0]['user']['userid'] : false;
				if ($communityUserId !== false) {
					$this->query("DELETE FROM user WHERE user.userid=$communityUserId");
				}
				$this->setDataSource('default');
				*/
			}
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * TODO: short description.
	 * 
	 * @return TODO
	 */
	public function paginateCount($conditions = null, $recursive = 0, $extra = array())
	{
		$this->recursive = $recursive;
		$r = $this->find('count', array('conditions' => $conditions));
		return ($r);
	}
}
?>
