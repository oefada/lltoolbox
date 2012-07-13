<?php

App::import("Vendor","NL",array('file' => "appshared".DS."legacy".DS."classes".DS."newsletter_manager.php"));

class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form');
	var $user;
	var $uses = array('User');

	function index() {

		// 07/01/11 jwoods - index page is still paginating all users but not displaying anything
		// remove everything for now

		// $this->User->Behaviors->attach('Containable');
		// $this->User->contain(array('UserSiteExtended'));

		// $this->set('users', $this->paginate());
		$this->set('users', array());
	}

	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->flash(__('User saved.', true), array('action'=>'index'));
			} else {

			}
		}

		$contests = $this->User->Contest->find('list');
		$clients = $this->User->Client->find('list');
		$salutationIds = $this->User->Salutation->find('list');
		$this->set(compact('contests', 'clients', 'salutationIds'));
	}

	/**
	 * Unsub user from all newsletters 
	 * 
	 * @param int $id
	 * 
	 * @return nuttin'
	 */
	function unsub($id=null){

		$error_str='';

		if (!$id || empty($this->data)) {

			$this->flash(__('Invalid User', true), array('action'=>'edit'));

		}else if (!empty($this->data)){

			$email=$this->data['User']['email'];
			$userId=$this->data['User']['userId'];

			$nlMgr=new NewsletterManager();

			if (isset($this->data['User']['mailingListData']) && count($this->data['User']['mailingListData'])>0){
			
				$mailingListDataArr=$this->data['User']['mailingListData'];

				$allNlArr=NewsletterManager::$nlDataArr;

				foreach($mailingListDataArr as $key=>$str){

					$arr=explode("~",$str);
					$checkedMailingListId=$arr[0];
					$postedSiteId=$arr[1];
					$optinDatetime=$arr[2];

					// Retrieve id's related to silverpop from $nlDataArr and unsub from silverpop
					$row=$allNlArr[$postedSiteId][$checkedMailingListId];
					$r=$nlMgr->removeUserFromContactList($email, $row['contactId']);
					if (isset($row['legacyId']) && $row['legacyId']>0){
						$nlMgr->optoutUserFromDatabase($email, $row['legacyId']);
					}
					if (isset($row['altContactIdSub']) && is_array($row['altContactIdSub'])){
						foreach($row['altContactIdSub'] as $contactId){
							$r=$nlMgr->removeUserFromContactList($email, $contactId);
						}
					}


					// Update local dbs
					// update userMailOptin table
					$this->User->UserMailOptin->updateAll( 
						array(
							'UserMailOptin.optin'=>'0',
							'UserMailOptin.optoutDatetime'=>"'".date("Y-m-d H:m:s")."'",
						),
						array(
							'UserMailOptin.userId'=>$userId, 
							'UserMailOptin.mailingListId'=>$checkedMailingListId
						)
					);
					$this->User->bindModel(
						array(
							'hasMany'=>array(
								'unsubscribeLog'=>array(
									'UnsubscribeLog'=>array(
										'className'=>'UnsubscribeLog'
									)
								)
							)
						)
					);
					// update unsubscribeLog table
					$arr['unsubscribeLog']=array(
						'email'=>$email, 
						'siteId'=>$postedSiteId,
						'mailingId'=>$checkedMailingListId,
						'unsubDate'=>time(),
						'subDate'=>strtotime($optinDatetime)
					);
					$this->User->unsubscribeLog->create();
					$this->User->unsubscribeLog->save($arr);

				}

			}	

			$errors=$nlMgr->getErrors();
			if (is_array($errors) && count($errors)>0){
				$error_str=implode("<br>",$errors);
			}

		}

		if ($error_str!=''){
			$this->Session->setFlash(__('Errors unsubing '.$this->data['User']['email'].'<br>'.$error_str, true));
		}else{
			$this->Session->setFlash(__('Updated Unsub for '.$this->data['User']['email'], true));
		}

		$this->redirect(array("action" => 'edit', $userId));


	}

	function view($id = null) {
		$this->redirect(array("action" => 'edit', $id));
		exit;
	}

	function edit($id = null) {

		$this->set('userId', $id);
		if (!$id && empty($this->data)) {
			$this->flash(__('Invalid User', true), array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved.', true));
				$this->redirect("/users/".$this->data['User']['userId']);
			} 
		}
		if (empty($this->data)) {
			$this->data = $this->user;
		}

		$salutationIds = $this->User->Salutation->find('list');
		$paymentTypes = $this->User->UserPaymentSetting->PaymentType->find('list');
		$addressTypes = $this->User->Address->AddressType->find('list');
		//$options['conditions']=array('userId'=>$id);
		//$options['fields']=array('mailingListId','optin');
		//$mailingListIds = $this->User->UserMailOptin->find('list', $options);

		$this->loadModel("CreditTracking");
		$cof = $this->CreditTracking->find('all', array(
			'fields' => array('CreditTracking.*'), 
			'contain' => array('User'),
			'conditions' => array('User.userId' => $id),
			'order' => 'CreditTracking.creditTrackingId DESC',
			'limit' => 1,
		));

		if (!empty($cof)) {
			$this->data['CreditTracking'] = $cof[0]['CreditTracking'];
		}

		$this->set('user', $this->data);
		$this->set(compact('user', 'salutationIds', 'paymentTypes', 'addressTypes'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->flash(__('Invalid User', true), array('action'=>'index'));
		}
		if ($this->User->del($id)) {
			$this->flash(__('User deleted', true), array('action'=>'index'));
		}
	}

	// $query is used to query against firstName, lastName and userName at the same time
	// $specificSearch is used to signify a single search against firstName, or single search against lastName
	// or a single search against firstName + lastName, or single search against username
	// email searches use $origQuery
	function search()
	{

		$options = array();
		$joins = array();
		$order=array();
		$origQuery='';
		$firstName='';
		$lastName='';
		$username='';
		$email='';
		$query='';
		$queryBooleanMode='';
		$specificSearch=false;
		$paginateArr=array();

		$params=$this->params;

		//
		// set posted form values 
		//
		if (!empty($params['url']['firstName'])){
			$firstName=trim($this->Sanitize->escape($this->params['url']['firstName']));
			$specificSearch='firstName';
		}elseif (!empty($params['named']['firstName'])){
			$firstName=trim($this->Sanitize->escape($params['named']['firstName']));
			$specificSearch='firstName';
		}

		if (!empty($params['url']['lastName'])){
			$lastName=trim($this->Sanitize->escape($this->params['url']['lastName']));
			$specificSearch='lastName';
		}else if (!empty($params['named']['lastName'])){
			$lastName=trim($this->Sanitize->escape($this->params['named']['lastName']));
			$specificSearch='lastName';
		}
		if (!empty($params['url']['username'])){
			$username=trim($this->Sanitize->escape($params['url']['username']));
			$specificSearch='username';
		}elseif(!empty($params['named']['username'])){
			$username=trim($this->Sanitize->escape($params['named']['username']));
			$specificSearch='username';
		}

		if(!empty($_GET['query']) && $_GET['query']!='email') {
			$this->params['form']['query'] = $_GET['query'];
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
		}

		if (!empty($this->params['form']['query'])){
			$query = trim($this->Sanitize->escape($this->params['form']['query']));
			// If it has a space in it, treat as first and last name 
			if (strstr($query," ")){
				$qArr=explode(" ", $query);
				$firstName=$qArr[0];
				$lastName=$qArr[1];
				$specificSearch="fullName";
			}
		}
		if($query!='' && strlen($query) <= 3){
			return;
		}

		if (strstr($query, "@")){
			$email=trim($query);
		}


		//
		// set sort and direction of sort
		//
		if (isset($this->params['named']['sort'])){
			$sort=$this->params['named']['sort'];
			$dir=$this->params['named']['direction'];
		}else if (isset($this->passedArgs['sort'])){
			$sort=$this->passedArgs['sort'];
			$dir=$this->passedArgs['direction'];
		}else{
			$sort='ticketCount';
			$dir='DESC';
		}

		// For some reason, to get ticketCount to work, this has to be unset.
		if (isset($this->passedArgs['sort']) && $this->passedArgs['sort']=='ticketCount'){
			unset($this->passedArgs['sort']);
			unset($this->passedArgs['direction']);
		}

		//
		// build queries
		//
		if ($query || $specificSearch || $email){

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

			$joins=array(
				array(
					'table'=>'userSiteExtended',
					'alias'=>'UserSiteExtended',
					'type'=>'LEFT',
					'conditions'=>array(
						'User.userId=UserSiteExtended.userId',
					)
				),
				array(
					'table'=>'ticket',
					'type'=>'LEFT',
					'conditions'=>'User.userId=ticket.userId'
				),
			);

			if ( $email ){ 

				$conditions=array("User.email"=>$email);

			}else{

				if ($firstName && $lastName){
					$conditions=array( "(User.lastName like '".$lastName."%' AND User.firstName LIKE '".$firstName."%')");
					$order[]="fullNameMatch DESC";
					$fields[]="(CASE WHEN User.lastname ='".$lastName."' AND User.firstName = '".$firstName."' THEN 2  WHEN User.lastname like '".$lastName."%' AND User.firstName like '".$firstName."%' THEN 1   ELSE 0 END ) AS fullNameMatch";
				}else if ($firstName){
					$conditions=array("User.firstName LIKE '".$firstName."%'");
				}else if ($lastName){
					$conditions=array("User.lastName LIKE '".$lastName."%'");
				}else if ($username){
					$conditions=array("UserSiteExtended.username LIKE  '".$username."%'");
				}else{
					$conditions=array(
						"(User.lastName like '".$query."%' OR User.firstName LIKE '".$query."%' OR UserSiteExtended.username LIKE  '".($query)."%')"
					);
				}

			}

			$order[]=$sort.' '.$dir;
			if ($sort=='ticketCount'){
				// this is necessary as count() will count a row that is actually a null result set as one, 
				// putting real result sets of one on the same level
				$order[]='hasTicketId '.$dir;
			}

			$paginateArr=array(
				'joins' => $joins,
				'fields'=>$fields,
				'contain' => false,
				'conditions' => $conditions, 
				'group' => 'User.userId',
				'order' => $order,
				'limit'=>20
			);

			$this->paginate = $paginateArr;

			if(!empty($query)){
				$this->set('query', $query);
			}else if (!empty($specificSearch)){
				$this->set('query', $specificSearch);
			}
//print "<pre>";print_r($paginateArr);
			//$this->UserReferrals->recursive = 0;
			$this->autoRender = false;
			$this->User->Behaviors->attach('Containable');
			//debug($this->User->find('all', $paginateArr));
			$users=$this->paginate();
//			var_dump($users);exit;
			$this->set('users', $users);
			$this->render('index');

		}

	}

	/**
	 * Method resets a user's password in the UserSiteExtended model. Needs to update directly on live, hence the setDataSource
	 * @params $id the id of the row in the UserSiteExtended table NOT the id of the main user account
	 * @returns
	 */
	function resetPassword($id = null) {
		if(!empty($this->data)) {

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

		} else {
			$this->data = $this->User->read(null, $id);
		}
	}

	function generatePassword($length=9, $strength=4) {
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

	function tickets($id) {
		$this->autoRender = false;
		$userTickets = $this->paginate('Ticket', array('Ticket.userId' => $id));
		$this->set('tickets', $userTickets);
		$this->render('../tickets/index');
	}

	function bids($id) {
		$this->autoRender = false;
		$this->set('bids', $this->paginate('Bid', array('Bid.userId' => $id)));
		$this->render('../bids/index');
	}

	function linkReferral($id) {
		$this->layout = "ajax";
		
		$linkId = $this->params['url']['linkid'];
		
		// Lookup user's email
		$toLink = $this->User->find('first', Array(
			'conditions' => Array('User.userId' => $linkId),
		));
		
		$this->UserReferrals = new UserReferrals;
		// Check if user has already been referred
		$alreadyReferred = $this->UserReferrals->find('first', array(
			'conditions' => Array('UserReferrals.referredEmail' => $toLink['User']['email']),
		));

		$this->UserReferrals->recursive = -1;
				
		if ($alreadyReferred !== false) {
			echo json_encode(array(
				'msg'=>'ALREADY',
				'userId' => $alreadyReferred['UserReferrals']['referrerUserId']
			));
			
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
	
	function referralsSent($id) {
		$this->autoRender = false;

		$this->params['form'] = $id . '/';
		
		// referrals sent by user
		$this->paginate = Array(
			'conditions' => Array('UserReferrals.referrerUserId' => $id),
			'order' => Array('UserReferrals.statusTypeId' => 'desc',
							 'UserReferrals.referredEmail' => 'asc'),
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
	
	function referralsRecvd($id) {
		$this->autoRender = false;

		$this->params['form'] = $id . '/';
		
		// referrals sent to user's email address
		$this->paginate = Array(
			'conditions' => Array('UserReferrals.referredEmail' => $this->user['User']['email']),
			'order' => Array('User.email' => 'asc',
							 'UserReferrals.statusTypeId' => 'desc',
							 'UserReferrals.referredEmail' => 'asc'),
			'limit' => 20
		);
		$referralsRecvd = $this->paginate('UserReferrals');
		
		//var_dump($referralsRecvd); die();

		$this->set('referralsRecvd', $referralsRecvd);
		$this->set('user', $this->user);
		$this->render('../user_referrals/received');
	}

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'customers');

		$userId = $this->User->id;

		if(!isset($id) && isset($this->params['pass'][0])){
			$userId = $this->params['pass'][0];
		}

		// 07/01/11 jwoods - don't pull User info without id
		if (intval($userId) == 0) { return; }

		$this->User->recursive = 1;
		$this->user = $this->User->find('first',array('conditions' => array('User.userId' => $userId)));

		$this->set('user', $this->user);
		$this->set('userId', $userId);
		$this->set('numUserTickets', $this->User->Ticket->find('count', array('conditions' => array('Ticket.userId' => $userId))));
		$this->set('numUserBids', $this->User->Bid->find('count', array('conditions' => array('Bid.userId' => $userId))));
	}
}
?>
