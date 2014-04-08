<?php
class UsersController extends AppController
{
    var $name = 'Users';
    var $helpers = array('Html', 'Form');
    var $user;
    var $uses = array('User', 'MailingList');

    public $components = array(
        'LltgServiceHelper'
    );

    /**
     * @var LltgServiceHelperComponent $LltgServiceHelper
     */
    public $LltgServiceHelper;

    function index()
    {
        $this->set('users', array());
    }

    function add()
    {
        if (!empty($this->data)) {
            $this->User->create();
            if ($this->User->save($this->data)) {
                $this->flash(__('User saved.', true), array('action' => 'index'));
            } else {

            }
        }

        $contests = $this->User->Contest->find('list');
        $clients = $this->User->Client->find('list');
        $salutationIds = $this->User->Salutation->find('list');
        $this->set(compact('contests', 'clients', 'salutationIds'));
    }

    /**
     * Subscribes user to a given email list
     *
     * @param int $id
     *
     * @return nuttin'
     */

    function sub($id = null)
    {
        $mailVendor = $this->MailingList->getMailVendorHelper();
        $profile = $this->MailingList->getUserProfile();

        if (!$id || empty($this->data)) {
            //$this->flash(__('Invalid User', true), array('action' => 'edit'));
        } else {
            if (!empty($this->data)) {
                $email = $this->data['User']['email'];
                $userId = $this->data['User']['userId'];

                if (isset($this->data['User']['mailingListSubData']) &&
                    count($this->data['User']['mailingListSubData']) > 0) {

                    // Contains POST data of user choices
                    $mailingListDataArr = $this->data['User']['mailingListSubData'];

                    // Preparing Profile Object
                    // For more, in LL codebase, go to appshared/legacy/class/newsletter_manager.php and look @ class
                    // newsletterUserProfile
                    $profile->setEmail($email);
                    $profile->setFirstName($this->user['User']['firstName']);
                    $profile->setLastName($this->user['User']['lastName']);
                    $profile->setZip($this->user['Address'][0]['postalCode']);
                    $profile->setCountry($this->user['Address'][0]['countryCode']);


                    // Goal
                    // 1 - Loops through each POST var. Var is in the form of "SID~MLID" where SID is Site ID and MLID is
                    //     Mailing List ID
                    // 2 - Get UserMailOptIn Object Associated with Current User
                    // 3 - Given a specific mailing list id, cycle through the Optin Rows to see if record exists
                    // 4 - If it does, enter it via an update
                    // 5 - If not, insert it into the query directly

                    foreach($mailingListDataArr as $subscription) {

                        // Extracting SID and MLID from POST
                        $subscriptionArr = explode('~',$subscription);
                        $subscriptionSiteId = $subscriptionArr[0];
                        $profile->setSiteId($subscriptionSiteId);
                        $subscriptionMailingListId = $subscriptionArr[1];

                        // Flag that determines whether an update is required or new entry
                        $foundSubscription = false;

                        // UPDATE PHASE -> If previously subscribed to this newsletter, updates row to set optin=1 (True)
                        foreach($this->user['UserMailOptin'] as $optInRow) {
                            foreach($optInRow as $key => $value) {
                                if ($optInRow['mailingListId'] == $subscriptionMailingListId) {
                                    $foundSubscription = true;
                                    $this->User->UserMailOptin->updateAll(
                                        array(
                                            'UserMailOptin.optin' => '1',
                                            'UserMailOptin.optinDatetime' => "'" . date("Y-m-d H:m:s") . "'",
                                        ),
                                        array(
                                            'UserMailOptin.userId' => $userId,
                                            'UserMailOptin.mailingListId' => $subscriptionMailingListId,
                                        )
                                    );
                                }
                            }
                        }

                        // NEW PHASE -> If this is a new subscription to this newsletter, enters row in optin table
                        // in DB
                        if (!$foundSubscription) {
                            $optInData = array();
                            $optInData['UserMailOptin']['mailingListId'] = $subscriptionMailingListId;
                            $optInData['UserMailOptin']['optin'] = 1;
                            $optInData['UserMailOptin']['optinDatetime'] = date("Y-m-d H:m:s");
                            $optInData['UserMailOptin']['userId'] = $userId;
                            $optInData['UserMailOptin']['subscribeDatetime'] = date("Y-m-d H:m:s");
                            $optInData['UserMailOptin']['source'] = "toolbox";

                            $this->User->UserMailOptin->saveAll($optInData);
                        }

                        $result[] = $mailVendor->addUserToList($profile, $subscriptionMailingListId);
                    }
                }
            }
        }

        $this->Session->setFlash(__('Subscribed ' . $this->data['User']['email'] . ' to newsletters', true));
        $this->redirect(array("action" => 'edit', $userId));
    }



    /**
     * Unsub user from all newsletters
     *
     * @param int $id
     *
     * @return nuttin'
     */
    function unsub($id = null)
    {

        $mailvendor = $this->MailingList->getMailVendorHelper();

        $error_str = '';

        if (!$id || empty($this->data)) {

            $this->flash(__('Invalid User', true), array('action' => 'edit'));

        } else {
            if (!empty($this->data)) {

                $email = $this->data['User']['email'];
                $userId = $this->data['User']['userId'];

                if (isset($this->data['User']['mailingListData']) && count(
                        $this->data['User']['mailingListData']
                    ) > 0
                ) {

                    $mailingListDataArr = $this->data['User']['mailingListData'];

                    foreach ($mailingListDataArr as $key => $str) {
                        $arr = explode("~", $str);
                        $checkedMailingListId = $arr[0];
                        $postedSiteId = $arr[1];
                        $optinDatetime = $arr[2];

                        $mailvendor->removeEmailFromList($postedSiteId, $email, $checkedMailingListId);

                        $this->User->UserMailOptin->updateAll(
                            array(
                                'UserMailOptin.optin' => '0',
                                'UserMailOptin.optoutDatetime' => "'" . date("Y-m-d H:m:s") . "'",
                            ),
                            array(
                                'UserMailOptin.userId' => $userId,
                                'UserMailOptin.mailingListId' => $checkedMailingListId
                            )
                        );

                        $this->loadModel('UnsubscribeLog');
                        $conditions = array(
                            'email' => $email,
                            'siteId' => $postedSiteId,
                            'mailingId' => $checkedMailingListId,
                        );

                        $updateConditions = $this->prepareModelData('UPDATE', 'UnsubscribeLog', $conditions);

                        if ($this->UnsubscribeLog->hasAny($updateConditions)) {
                            $this->UnsubscribeLog->updateAll(
                                array(
                                    'UnsubscribeLog.unsubDate' => time()
                                ),
                                $updateConditions
                            );
                        } else {
                            $this->UnsubscribeLog->create();
                            $conditions['unsubDate'] = time();
                            $conditions['subDate'] = strtotime($optinDatetime);
                            $insertConditions = $this->prepareModelData('INSERT', 'UnsubscribeLog', $conditions);
                            $this->UnsubscribeLog->save($insertConditions);
                        }

                    }

                }
                if (is_array($errors) && count($errors) > 0) {
                    $error_str = implode("<br>", $errors);
                }

            }
        }
        $this->Session->setFlash(__('Updated Unsub for ' . $this->data['User']['email'], true));
        $this->redirect(array("action" => 'edit', $userId));
    }

    function view($id = null)
    {
        $this->redirect(array("action" => 'edit', $id));
        exit;
    }

    function edit($id = null)
    {
        $this->set('userId', $id);
        if (!$id && empty($this->data)) {
            $this->flash(__('Invalid User', true), array('action' => 'index'));
        }
        if (!empty($this->data)) {
            if ($this->User->save($this->data)) {
                $this->Session->setFlash(__('The User has been saved.', true));
                $this->redirect("/users/" . $this->data['User']['userId']);
            }
        }
        if (empty($this->data)) {
            $this->data = $this->user;
        }

        $lltgServiceBuilder = $this->LltgServiceHelper->getServiceBuilderFromTldId($this->user['User']['tldId']);
        $this->set('lltgServiceBuilder', $lltgServiceBuilder);

        $salutationIds = $this->User->Salutation->find('list');
        $paymentTypes = $this->User->UserPaymentSetting->PaymentType->find('list');
        $addressTypes = $this->User->Address->AddressType->find('list');

        $this->loadModel("CreditTracking");
        $cof = $this->CreditTracking->find(
            'all',
            array(
                'fields' => array('CreditTracking.*'),
                'contain' => array('User'),
                'conditions' => array('User.userId' => $id),
                'order' => 'CreditTracking.creditTrackingId DESC',
                'limit' => 1,
            )
        );

        if (!empty($cof)) {
            $this->data['CreditTracking'] = $cof[0]['CreditTracking'];
        }


        $numAccountsWithEmail = $this->User->countAccountsWithEmail($this->data['User']['email']);
        $this->set('numAccountsWithEmail', $numAccountsWithEmail);
        $this->set('email', $this->data['User']['email']);
        $this->set('user', $this->data);
        $this->set('newsletterInfo', $this->MailingList->getNewsletterData());
        $this->set(compact('user', 'salutationIds', 'paymentTypes', 'addressTypes'));
        $this->set('numOptIns', $this->getNumOfOptIns($this->data['UserMailOptin']));
    }

    /**
     * Display all accounts and their status for associated email
     *
     * @return null
     */
    public function email($id = null)
    {

        $email = $this->params['url']['email'];
        $doProcessOne = isset($this->params['url']['process']) ? $this->params['url']['process'] : 0;

        $this->set('hideSidebar', false);
        $this->set("currentTab", "customers");

        $rowArr = $this->processDupEmails(array($email), $doProcessOne);
        if ($rowArr == "done") {
            $rowArr = array();
        }

        $this->set("email", $email);
        $this->set("rowArr", $rowArr);

    }

    /*
     * Function that takes in a string query type, a string model name, and an array of the fields in the model
     * that you want to create a record for/search for. Array passed by reference is modified by the function.
     *  It's needed because Cake requires different data
     * structures for different operations. **Groan**
     *
     * @param string
     * @param string
     * @param array by reference
     */
    private function prepareModelData($queryType, $modelName , $fields)
    {
        if ($queryType = 'UPDATE') {
            // first, prefix each key in the array with the model name
            foreach ($fields as $key => $value) {
                $key = $modelName . '.' . $key;

            }
            return $fields;
        }

        if ($queryType = 'INSERT') {
            $fields = array($modelName => $fields);
            return $fields;
        }
    }

    /**
     * Returns the number of active newsletter subscriptions for a given user
     *
     * @param array
     *
     * @return string
     */
    private function getNumOfOptIns($optInArray)
    {
        $numOptIns = 0;

        foreach ($optInArray as $optIn) {
            if ($optIn['optin'] == 1) $numOptIns++;
        }
        return "(" . $numOptIns . ")";
    }

    /**
     * Batch process accounts with one email associated with multiple userIds.
     *
     * @param array
     *
     * @return array
     */
    private function processDupEmails($emailArr = array(), $doProcessOne = 0, $ajax = 0)
    {
        foreach ($emailArr as $email) {

            // see if email has some userIds without matching rows in userSiteExtended AND
            // some userIds with matching rows in userSiteExtended
            $numWith = 0;
            $numWithout = 0;
            $doProcessTwo = 0;

            if ($doProcessOne) {

                // has rows in user WITHOUT matching row in userSiteExt
                $q = "SELECT COUNT(*) AS num FROM `user` LEFT JOIN userSiteExtended ue using(userId) ";
                $q .= "WHERE ue.userId IS NULL ";
                $q .= "AND email=? ";
                $r = $this->User->query($q, array($email));
                $numWithout = $r[0][0]['num'];

                // has rows in user WITH matching row in userSiteExt
                $q = "SELECT COUNT(*) AS num FROM `user` LEFT JOIN userSiteExtended ue using(userId) ";
                $q .= "WHERE ue.userId IS NOT NULL ";
                $q .= "AND email=? ";
                $r = $this->User->query($q, array($email));
                $numWith = $r[0][0]['num'];

                // at least one userId in user table has a matching row in userSiteExtended.
                // get the userIds without matching rows in userSiteExtended and de-dup them
                if ($numWith > 0 && $numWithout > 0) {
                    $q = "SELECT * FROM `user` u LEFT JOIN userSiteExtended ue using(userId) ";
                    $q .= "WHERE ue.userId IS NULL ";
                    $q .= "AND email=? ";
                    $r = $this->User->query($q, array($email));
                    foreach ($r as $arr) {
                        $userId = $arr['u']['userId'];
                        $renameEmail = $arr['u']['email'] . '_dup_' . $userId;
                        //$q="UPDATE `user` SET email=\"$renameEmail\", inactive=1 WHERE userId=$userId";
                        $q = "UPDATE `user` SET email=?, inactive=1 WHERE userId=?";
                        $this->User->query($q, array($renameEmail, $userId));
                    }
                }
            }

            // all but one where successfully de-duped, no need to process the rest
            if ($numWith == 1) {
                $doProcessTwo = 0;
                // if this is ajax, it isn't for display and there is no further processing to do
                if ($ajax == 1) {
                    continue;
                }
            } elseif ($doProcessOne) {
                $doProcessTwo = 1;
            }

            // process emails that have userId's that all have matching rows in userSiteExtended
            // OR no matching rows in userSiteExtended.
            // ergo, userSiteExtended is no longer a criteria
            /*
             1. user row with most recent modifyDateTime (login) that has a ticketId
             2. the most recent (non null) modifyDateTime
             3. the most recent userId
            */

            $q = "SELECT u.createDateTime, u.modifyDateTime, u.email as email, ";
            $q .= "u.userId, ue.userSiteExtendedId, ";
            $q .= "u.inactive, ticket.ticketId ";
            $q .= "FROM `user` u ";
            $q .= "LEFT JOIN ticket using(userId) ";
            $q .= "LEFT JOIN userSiteExtended ue using(userId) ";
            $q .= "WHERE email=? ";
            $q .= "GROUP BY userId, ticketId ";
            $q .= "ORDER BY ticketId DESC ";
            $r = $this->User->query($q, array($email));

            $userIdArr = array();
            $modifyDateTimeArr = array();
            $ticketArr = array();
            $rowArr = array();
            $modifyDateTimeIsNotNull = false;
            $primaryUserId = false;
            foreach ($r as $arr) {
                $userId = $arr['u']['userId'];
                $renameEmail = $email . '_dup_' . $userId;

                $rowArr[$userId] = array(
                    "userId" => $userId,
                    "email" => $email,
                    "renameEmail" => $renameEmail,
                    "createDateTime" => $arr['u']['createDateTime'],
                    "modifyDateTime" => $arr['u']['modifyDateTime'],
                    "userSiteExtendedId" => $arr['ue']['userSiteExtendedId'],
                    "inactive" => $arr['u']['inactive']
                );

                $userIdArr[] = $userId;
                $modifyDateTimeArr[$userId][] = strtotime($arr['u']['modifyDateTime']);
                if ($arr['u']['modifyDateTime'] != null) {
                    $modifyDateTimeIsNotNull = true;
                }

                // This email/userId has a ticket associated with it
                if ($arr['ticket']['ticketId'] != null) {
                    $ut = strtotime($arr['u']['modifyDateTime']);
                    $ticketArr[$ut] = $userId;
                    $rowArr[$userId]['ticketId'] = $arr['ticket']['ticketId'];
                }

            }

            // a userId has a ticket. set the most recent userId to be primary userId
            if (count($ticketArr) > 0) {
                krsort($ticketArr);
                $primaryUserId = array_shift($ticketArr);
            } else {
                // no tickets and has a modifyDateTime, set most recent modifyDateTime to be primary userId
                // else just take the most recent userId
                if ($modifyDateTimeIsNotNull) {
                    arsort($modifyDateTimeArr);
                    $primaryUserId = array_shift(array_keys($modifyDateTimeArr));
                } else {
                    rsort($userIdArr);
                    $primaryUserId = array_shift($userIdArr);
                }
            }

            if ($doProcessTwo) {
                if ($primaryUserId) {
                    $q = "UPDATE `user` SET inactive=0 WHERE userId=$primaryUserId";
                    $this->User->query($q);
                    foreach ($rowArr as $userId => $arr) {
                        if ($primaryUserId == $userId) {
                            continue;
                        }
                        $q = "UPDATE `user` SET inactive=1, email=? WHERE userId=$userId";
                        $this->User->query($q, array($arr['renameEmail']));
                        unset($rowArr[$userId]);
                    }
                } else {
                    echo "<br>No primaryUserId found<br>";
                    $this->User->logit("No primaryUserId found");
                    $this->User->logit($rowArr);
                    $this->User->logit('------------------------------');
                }
            }
        }

        return isset($rowArr) ? $rowArr : array();

    }

    /**
     * Batch process accounts with multiple userId's associated with one email
     *
     * @return null
     */
    public function deleteDups()
    {

        $ajax = isset($this->params['url']['ajax']) ? $this->params['url']['ajax'] : false;
        if ($ajax) {
            $this->layout = false;
            $this->autoRender = false;
        }
        $this->set('hideSidebar', true);
        $showDupCount = isset($this->params['url']['showDupCount']) ? 1 : 0;
        $trimEmails = isset($this->params['url']['trimEmails']) ? 1 : 0;
        $showDupCountNoInactive = isset($this->params['url']['showDupCountNoInactive']) ? 1 : 0;
        $runProcess = isset($this->params['url']['runProcess']) ? 1 : 0;
        $dupCount = true;
        $dupCountNoInactive = false;

        $q = "SELECT email, COUNT(email) as num FROM user GROUP BY email HAVING num>1";
        $r = $this->User->query($q);
        $dupCount = number_format(count($r));

        if ($trimEmails) {

            $q = "UPDATE `user` SET email = TRIM(email) WHERE email<>TRIM(email)";
            $r = $this->User->query($q);

        } elseif ($showDupCountNoInactive) {

            $q = "SELECT email, COUNT(email) as num FROM user ";
            $q .= "WHERE inactive=0 ";
            $q .= "GROUP BY email HAVING num>1";
            $r = $this->User->query($q);
            $dupCountNoInactive = number_format(count($r));

        } elseif ($runProcess) {

            $q = "SELECT TRIM(LOWER(email)) as email, COUNT(email) as num FROM user GROUP BY email HAVING num>1 ";
            // offset is always 0 as the data set being operated on gets removed once operated on and
            // thus reduces the count.
            $q .= "LIMIT 0, 100";
            $r = $this->User->query($q);
            if (count($r) == 0) {
                echo "done";
                return;
            }
            foreach ($r as $arr) {
                $emailArr[] = $arr[0]['email'];
            }

            $response = $this->processDupEmails($emailArr, true, $ajax);
            if ($ajax) {
                $str = '';
                foreach ($emailArr as $email) {
                    $str .= " OR email LIKE '$email%' ";
                }
                $str .= "\n\n";
                $str .= implode("', '", $emailArr);
                echo count($emailArr);
                return;
            }

        }

        if ($ajax == false) {
            $q = "SELECT count(*) as num FROM user WHERE email<>trim(email)";
            $r = $this->User->query($q);
            $numEmailsWithWhitespace = $r[0][0]['num'];
        }

        $this->set('numEmailsWithWhitespace', $numEmailsWithWhitespace);
        $this->set('dupCount', $dupCount);
        $this->set('dupCountNoInactive', $dupCountNoInactive);

    }

    function delete($id = null)
    {
        if (!$id) {
            $this->flash(__('Invalid User', true), array('action' => 'index'));
        }
        if ($this->User->del($id)) {
            $this->flash(__('User deleted', true), array('action' => 'index'));
        }
    }

    // $query is used to query against firstName, lastName and userName at the same time
    // $specificSearch is used to signify a single search against firstName, or single search against lastName
    // or a single search against firstName + lastName, or single search against username
    // email searches use $origQuery
    function search()
    {
        // Readonly db config
        $this->User->useReadonlyDb();

        $options = array();
        $joins = array();
        $order = array();
        $origQuery = '';
        $firstName = '';
        $lastName = '';
        $username = '';
        $email = '';
        $query = '';
        $queryBooleanMode = '';
        $specificSearch = false;
        $paginateArr = array();

        $params = $this->params;

        //
        // set posted form values
        //
        if (!empty($params['url']['firstName'])) {
            $firstName = trim($this->Sanitize->escape($this->params['url']['firstName']));
            $specificSearch = 'firstName';
        } elseif (!empty($params['named']['firstName'])) {
            $firstName = trim($this->Sanitize->escape($params['named']['firstName']));
            $specificSearch = 'firstName';
        }

        if (!empty($params['url']['lastName'])) {
            $lastName = trim($this->Sanitize->escape($this->params['url']['lastName']));
            $specificSearch = 'lastName';
        } else {
            if (!empty($params['named']['lastName'])) {
                $lastName = trim($this->Sanitize->escape($this->params['named']['lastName']));
                $specificSearch = 'lastName';
            }
        }
        if (!empty($params['url']['username'])) {
            $username = trim($this->Sanitize->escape($params['url']['username']));
            $specificSearch = 'username';
        } elseif (!empty($params['named']['username'])) {
            $username = trim($this->Sanitize->escape($params['named']['username']));
            $specificSearch = 'username';
        }

        if (!empty($_GET['query']) && $_GET['query'] != 'email') {
            $this->params['form']['query'] = $_GET['query'];
        } elseif (!empty($this->params['named']['query'])) {
            $this->params['form']['query'] = $this->params['named']['query'];
        }

        if (!empty($this->params['form']['query'])) {
            $query = trim($this->Sanitize->escape($this->params['form']['query']));
            // If it has a space in it, treat as first and last name
            if (strstr($query, " ")) {
                $qArr = explode(" ", $query);
                $firstName = $qArr[0];
                $lastName = $qArr[1];
                $specificSearch = "fullName";
            }
        }
        if ($query != '' && strlen($query) <= 3) {
            return;
        }

        if (strstr($query, "@")) {
            $email = trim($query);
        }


        //
        // set sort and direction of sort
        //
        if (isset($this->params['named']['sort'])) {
            $sort = $this->params['named']['sort'];
            $dir = $this->params['named']['direction'];
        } else if (isset($this->passedArgs['sort'])) {
            $sort = $this->passedArgs['sort'];
            $dir = $this->passedArgs['direction'];
        } else {
            $sort = 'ticketCount';
            $dir = 'DESC';
        }

        // For some reason, to get ticketCount to work, this has to be unset.
        if (isset($this->passedArgs['sort']) && $this->passedArgs['sort'] == 'ticketCount') {
            unset($this->passedArgs['sort']);
            unset($this->passedArgs['direction']);
        }


        //
        // build queries for "Auction + BuyNow" tickets
        //
        if ($query || $specificSearch || $email) {

            $fields = array(
                'ticket.ticketId',
                'UserSiteExtended.username',
                'User.userId',
                'User.firstName',
                'User.lastName',
                'User.email',
                'User.inactive',
                'count(*) as ticketCount',
                '(CASE WHEN ticket.ticketId is not null THEN 1 ELSE 0 END) AS hasTicketId'
            );

            $joins = array(
                array(
                    'table' => 'userSiteExtended',
                    'alias' => 'UserSiteExtended',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'User.userId=UserSiteExtended.userId',
                    )
                ),
                array(
                    'table' => 'ticket',
                    'type' => 'LEFT',
                    'conditions' => 'User.userId=ticket.userId'
                )
            );

            if ($email) {

                $conditions = array("User.email" => $email);

            } else {

                if ($firstName && $lastName) {
                    $conditions = array("(User.lastName like '" . $lastName . "%' AND User.firstName LIKE '" . $firstName . "%')");
                    $order[] = "fullNameMatch DESC";
                    $fields[] = "(CASE WHEN User.lastname ='" . $lastName . "' AND User.firstName = '" . $firstName . "' THEN 2  WHEN User.lastname like '" . $lastName . "%' AND User.firstName like '" . $firstName . "%' THEN 1   ELSE 0 END ) AS fullNameMatch";
                } else if ($firstName) {
                    $conditions = array("User.firstName LIKE '" . $firstName . "%'");
                } else if ($lastName) {
                    $conditions = array("User.lastName LIKE '" . $lastName . "%'");
                } else if ($username) {
                    $conditions = array();
                    $joins[0]['type'] = "INNER";
                    $joins[0]['conditions'][] = ("UserSiteExtended.username LIKE  '" . $username . "%'");
                    //$conditions[]=("UserSiteExtended.username LIKE  '".$username."%'");
                } else {
                    $conditions = array(
                        "(User.lastName like '" . $query . "%' OR User.firstName LIKE '" . $query . "%' OR UserSiteExtended.username LIKE  '" . ($query) . "%')"
                    );
                }

            }

            $paginateArr = array(
                'joins' => $joins,
                'fields' => $fields,
                'contain' => false,
                'conditions' => $conditions,
                'group' => 'User.userId',
                'order' => $order,
                'limit' => 20
            );

            $order[] = $sort . ' ' . $dir;
            if ($sort == 'ticketCount') {
                // this is necessary as count() will count a row that is actually a null result set as one,
                // putting real result sets of one on the same level
                $order[] = 'hasTicketId ' . $dir;
            }

            $paginateArr = array(
                'joins' => $joins,
                'fields' => $fields,
                'contain' => false,
                'conditions' => $conditions,
                'group' => 'User.userId',
                'order' => $order,
                'limit' => 20
            );

            $this->paginate = $paginateArr;
//print "<pre>";print_r($paginateArr);exit;
            if (!empty($query)) {
                $this->set('query', $query);
            } else if (!empty($specificSearch)) {
                $this->set('query', $specificSearch);
            }
//print "<pre>";print_r($paginateArr);
            //$this->UserReferrals->recursive = 0;
            $this->autoRender = false;
            $this->User->Behaviors->attach('Containable');
            //debug($this->User->find('all', $paginateArr));
            $users = $this->paginate();

            foreach($users as $key => $user) {
                $count = $this->User->getPgCountByUserId($user['User']['userId']);
                if ($count) {
                   array_push($users[$key],$count);
                }
            }

            if ($this->params['paging']['User']['count'] == 1) {
                $user = reset($users);
                if (isset($user['User']['userId'])) {
                    $this->Session->setFlash(
                        'Exactly 1 user found for search' . (!empty($query) ? ': ' . $query : '.')
                    );
                    $this->redirect(array('action' => 'edit', $user['User']['userId']));
                }
            }
            $this->set('users', $users);
            $this->render('index');

        }

    }

    /**
     * Method resets a user's password in the UserSiteExtended model. Needs to update directly on live, hence the setDataSource
     * @params $id the id of the row in the UserSiteExtended table NOT the id of the main user account
     * @returns
     */
    function resetPassword($id = null)
    {
        if (!empty($this->data)) {

            // unknown why we are calling setDataSource (seems to cause crash)
            // $this->User->setDataSource("live");
            // $this->User->UserSiteExtended->setDataSource("live");

            $newPassword = $this->generatePassword();
            $this->set('newPassword', $newPassword);

            // password is hashed in saveField call
            $this->User->UserSiteExtended->id = $this->data['User']['userId'];
            $this->User->UserSiteExtended->saveField('passwordHash', $newPassword);

            $userSiteExtended = $this->User->UserSiteExtended->read(null, $id);
            $this->User->id = $userSiteExtended['UserSiteExtended']['userId'];
            $this->User->saveField('transmitted', 0);

            $url = Configure::read("Url.LL") . '/ajax/cc.php?e=u&id=' . $userSiteExtended['UserSiteExtended']['userId'];
            // $result = file_get_contents($url);
            $this->set('cacheClearUrl', $url);

        } else {
            $this->data = $this->User->read(null, $id);
        }
    }

    function generatePassword($length = 9, $strength = 4)
    {
        $vowels = 'aeuy';
        $consonants = 'bdghjmnpqrstvz';
        if ($strength & 1) {
            $consonants .= 'BDGHJLMNPQRSTVWXZ';
        }
        if ($strength & 2) {
            $vowels .= "AEUY";
        }
        if ($strength & 4) {
            $consonants .= '23456789';
        }
        if ($strength & 8) {
            $consonants .= '@#$%';
        }

        $password = '';
        $alt = time() % 2;
        for ($i = 0; $i < $length; $i++) {
            if ($alt == 1) {
                $password .= $consonants[(rand() % strlen($consonants))];
                $alt = 0;
            } else {
                $password .= $vowels[(rand() % strlen($vowels))];
                $alt = 1;
            }
        }
        return $password;
    }

    function tickets($id)
    {
        $this->autoRender = false;
        $userTickets = $this->paginate('Ticket', array('Ticket.userId' => $id));
        $this->set('tickets', $userTickets);
        $this->render('../tickets/index');
    }

    function bids($id)
    {
        $this->autoRender = false;
        $this->set('bids', $this->paginate('Bid', array('Bid.userId' => $id)));
        $this->render('../bids/index');
    }

    function linkReferral($id)
    {
        $this->layout = "ajax";

        $linkId = $this->params['url']['linkid'];

        // Lookup user's email
        $toLink = $this->User->find(
            'first',
            Array(
                'conditions' => Array('User.userId' => $linkId),
            )
        );

        $this->UserReferrals = new UserReferrals;
        // Check if user has already been referred
        $alreadyReferred = $this->UserReferrals->find(
            'first',
            array(
                'conditions' => Array('UserReferrals.referredEmail' => $toLink['User']['email']),
            )
        );

        $this->UserReferrals->recursive = -1;

        if ($alreadyReferred !== false) {
            echo json_encode(
                array(
                    'msg' => 'ALREADY',
                    'userId' => $alreadyReferred['UserReferrals']['referrerUserId']
                )
            );

            exit;
        } else {
            $referrerStatus = 2;
            $referrerBonus = 0;

            if (!empty($toLink['Ticket'])) {
                foreach ($toLink['Ticket'] as $t) {
                    if ($t['ticketStatusId'] == 4) {
                        $referrerBonus = 1;
                        $referrerStatus = 1;
                    }
                }
            }

            // Temp fix for weird users (ticket 3036)
            if ($toLink['User']['siteId'] == null) {
                $toLink['User']['siteId'] = 1;
                $toLink['User']['createDatetime'] = date("Y-m-d H:i:s");
                $this->User->save($toLink['User']);
            }

            $data = array(
                'siteId' => $toLink['User']['siteId'],
                'referrerUserId' => $id,
                'referredEmail' => $toLink['User']['email'],
                'statusTypeId' => $referrerStatus,
                'referrerBonusApplied' => $referrerBonus,
                'referredBonusApplied' => 1,
                'createdDt' => date("Y-m-d H:i:s"),
            );

            if ($this->UserReferrals->save($data)) {
                $refId = $this->UserReferrals->getLastInsertId();

                if ($referrerBonus) {
                    // Apply credit to referrer
                    $this->UserReferrals->completeReferral($refId, 3);
                }

                echo json_encode(array('msg' => 'OK'));
                exit;
            }
        }

        echo json_encode(array('msg' => 'ERROR'));

        //referrerUserId
        $this->render('../user_referrals/link');
    }

    function referralsSent($id)
    {
        $this->autoRender = false;

        $this->params['form'] = $id . '/';

        // referrals sent by user
        $this->paginate = Array(
            'conditions' => Array(
                'UserReferrals.referrerUserId' => $id
            ),
            'order' => Array(
                'UserReferrals.statusTypeId' => 'desc',
                'UserReferrals.referredEmail' => 'asc'
            ),
            'limit' => 20
        );

        $this->UserReferrals = new UserReferrals;
        $this->UserReferrals->recursive = -1;
        $referralsSent = $this->paginate('UserReferrals');

        // check for registered users that were referred, with status 1
        foreach ($referralsSent AS &$r) {
            $params = Array('conditions' => Array('email' => $r['UserReferrals']['referredEmail']));
            $x = $this->User->find('first', $params);

            if (is_array($x['Ticket']) && count($x['Ticket']) > 0) {
                $r['UserReferrals']['hasPurchase'] = 0;

                // Verify that a ticket has been completed
                foreach ($x['Ticket'] as $t) {
                    // "Reservation confirmed"
                    if ($t['ticketStatusId'] == 4) {
                        $r['UserReferrals']['hasPurchase'] = 1;
                        break;
                    }
                }
            } else {
                $r['UserReferrals']['hasPurchase'] = 0;
            }

            if ($r['UserReferrals']['statusTypeId'] == 1) {
                if (is_array($x) && count($x) > 0) {
                    $r['UserReferrals']['isRegistered'] = 1;
                } else {
                    $r['UserReferrals']['isRegistered'] = 0;
                }
            } else {
                if ($x !== false) {
                    $r['UserReferrals']['isRegistered'] = 1;
                } else {
                    $r['UserReferrals']['isRegistered'] = 0;
                }
            }

            if ($x !== false) {
                $r['User'] = $x['User'];
            }
        }

        $this->set('referralsSent', $referralsSent);
        $this->set('user', $this->user);
        $this->render('../user_referrals/sent');
    }

    function referralsRecvd($id)
    {
        $this->autoRender = false;

        $this->params['form'] = $id . '/';

        // referrals sent to user's email address
        $this->paginate = Array(
            'conditions' => Array('UserReferrals.referredEmail' => $this->user['User']['email']),
            'order' => Array(
                'User.email' => 'asc',
                'UserReferrals.statusTypeId' => 'desc',
                'UserReferrals.referredEmail' => 'asc'
            ),
            'limit' => 20
        );
        $referralsRecvd = $this->paginate('UserReferrals');


        $this->set('referralsRecvd', $referralsRecvd);
        $this->set('user', $this->user);
        $this->render('../user_referrals/received');
    }

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->set('currentTab', 'customers');

        $userId = $this->User->id;

        if (!isset($id) && isset($this->params['pass'][0])) {
            $userId = $this->params['pass'][0];
        }

        // 07/01/11 jwoods - don't pull User info without id
        if (intval($userId) == 0) {
            return;
        }

        $this->User->recursive = 1;
        $this->user = $this->User->find('first', array('conditions' => array('User.userId' => $userId)));

        $this->set('user', $this->user);
        $this->set('userId', $userId);
        $this->set(
            'numUserTickets',
            $this->User->Ticket->find('count', array('conditions' => array('Ticket.userId' => $userId)))
        );
        $this->set(
            'numUserBids',
            $this->User->Bid->find('count', array('conditions' => array('Bid.userId' => $userId)))
        );
    }
}
