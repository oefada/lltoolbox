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
		),
		'Call' => array(
			'foreignKey' => 'userId',
		),		
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
	 * Count the number of distinct userId's associated with the email. (Legacy data issue) 
	 * 
	 * @param mixed $email 
	 * 
	 * @return int
	 */
	public function countAccountsWithEmail($email){

		return $this->find('count', array('conditions'=>array('email'=>$email)));

	}
	
	/**
	 * Delete a user and related records
		Currently, these are all the tables with a userId column 
		address
		badUser
		bid
		creditTracking
		dealAlert
		giftCertBalance
		partnerRequest
		promoCodeOwner
		promoCodeRecipient
		promoOfferTracking
		promoTicketRel
		ticket
		user
		userAuctionsTracked
		userClientFavorites
		userClientSpecialOffers
		userClientTracking
		userMailOptin
		userOauth
		userPaymentSetting
		userPreference
		userSiteExtended
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

	// This method wasn't matching the query it was paginating for and failing
	// 
	// Query:
	// 
	// SELECT *, count(*) as ticketCount, (CASE WHEN ticket.ticketId is not null THEN 1 ELSE 0 END) AS hasTicketId 
	// FROM `user` AS `User` LEFT JOIN userSiteExtended AS `UserSiteExtended` 
	// ON (`User`.`userId`=`UserSiteExtended`.`userId`) LEFT JOIN ticket 
	// ON (`User`.`userId`=`ticket`.`userId`) 
	// WHERE (`User`.`lastName` like 'roybatty%' OR `User`.`firstName` LIKE 'roybatty%' 
	// OR `UserSiteExtended`.`username` LIKE 'roybatty%') 
	// GROUP BY `User`.`userId` ORDER BY `ticketCount` DESC, `hasTicketId` DESC LIMIT 20
	// 
	// Failed pagination query: 
	//
	// SELECT COUNT(*) AS `count` FROM `user` AS `User`   
	// WHERE (`User`.`lastName` like 'roybatty%' OR `User`.`firstName` 
	// LIKE 'roybatty%' OR `UserSiteExtended`.`username` LIKE  'roybatty%')  
	// 
	// Fix implemented below
	// http://stackoverflow.com/questions/7120257/cakephp-pagination-count-not-matching-query
	/*
	public function paginateCount($conditions = null, $recursive = 0, $extra = array())
	{
		$this->recursive = $recursive;
		$r = $this->find('count', array('conditions' => $conditions));
		return ($r);
	}
	*/

	/**
	* See notes above
	* 
	* @return int
	*/
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$parameters = compact('conditions', 'recursive');
		if (isset($extra['group'])) {
			$parameters['fields'] = $extra['group'];
			if (is_string($parameters['fields'])) {
				// pagination with single GROUP BY field
				if (substr($parameters['fields'], 0, 9) != 'DISTINCT ') {
					$parameters['fields'] = 'DISTINCT ' . $parameters['fields'];
				}
				unset($extra['group']);
				$count = $this->find('count', array_merge($parameters, $extra));
			} else {
				// resort to inefficient method for multiple GROUP BY fields
				$count = $this->find('count', array_merge($parameters, $extra));
				$count = $this->getAffectedRows();
			}
		} else {
			// regular pagination
			$count = $this->find('count', array_merge($parameters, $extra));
		}
		return $count;
	}

}
