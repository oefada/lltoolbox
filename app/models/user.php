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
            'foreignKey' => 'userId'
        ),
        'PgBooking' => array(
            'foreignKey' => 'userId'
        ),
        'UserReferrals' => array(
            'foreignKey' => 'referrerUserId',
            'dependent' => true
        ),
        'Call' => array(
            'foreignKey' => 'userId'
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
     * @param    int userId
     * @return    bool
     */
    public function userExists($userId)
    {
        return ($this->findByUserId($userId) === false) ? false : true;
    }

    /**
     * Count the number of distinct userId's associated with the email. (Legacy data issue)
     *
     * @param mixed $email
     *
     * @return int
     */
    public function countAccountsWithEmail($email)
    {
        return $this->find('count', array('conditions' => array('email' => $email)));
    }

    /**
     * Delete a user and related records
     * Currently, these are all the tables with a userId column
     * address
     * badUser
     * bid
     * creditTracking
     * dealAlert
     * giftCertBalance
     * partnerRequest
     * promoCodeOwner
     * promoCodeRecipient
     * promoOfferTracking
     * promoTicketRel
     * ticket
     * user
     * userAuctionsTracked
     * userClientFavorites
     * userClientSpecialOffers
     * userClientTracking
     * userMailOptin
     * userOauth
     * userPaymentSetting
     * userPreference
     * userSiteExtended
     *
     * @param    int userId
     * @param    bool cascade
     * @return    bool
     */
    public function deleteUserById($userId, $cascade = true)
    {
        $this->id = $userId;
        $userEmail = mysql_real_escape_string($this->field('email'));

        if ($this->delete($userId) === true) {
            if ($cascade === true) {
                // Delete all tickets and related ticket records for user
                $this->Ticket->deleteAll(array('Ticket.userId' => $userId));

                // Delete user bid records
                $this->query("DELETE FROM bid WHERE bid.userId=$userId");

                // Delete contestUserRel records
                $this->query("DELETE FROM contestUserRel WHERE contestUserRel.userId=$userId");
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * See notes above
     *
     * @return int
     */
    public function paginateCount($conditions = null, $recursive = 0, $extra = array())
    {
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
    public function getPgCountByUserId($userId)
    {

        $sql = "SELECT count(*) as pgTickets FROM pgBooking p ";
        $sql .= "WHERE p.userId = " . $userId;
        $result = $this->query($sql);

        return $result[0][0];

    }
    /**
     * @param $userId
     * @return bool
     */
    public function isInternational($userId)
    {
        $this->recursive = false;
        $user = $this->read('User.tldId', $userId);
        if (isset($user['User']['tldId'])) {
            return ($user['User']['tldId'] > 1);
        } else {
            return false;
        }
    }
}
