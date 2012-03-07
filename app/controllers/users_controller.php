<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form');
	var $user;

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
			    $this->User->setDataSource("live");

        		$this->User->save($this->data);

				$this->Session->setFlash(__('The User has been saved.', true));

				$this->redirect("/users/".$this->data['User']['userId']);
			} else {
				
			}
		}
		if (empty($this->data)) {
			$this->data = $this->user;
		}
	
		$salutationIds = $this->User->Salutation->find('list');
		$paymentTypes = $this->User->UserPaymentSetting->PaymentType->find('list');
		$addressTypes = $this->User->Address->AddressType->find('list');
		$mailingListIds = $this->User->UserMailOptin->MailingList->find('list');
		
		$this->loadModel("CreditTracking");
		$cof = $this->CreditTracking->find('all', array(
			'fields' => array('CreditTracking.*'), 
			'contain' => array('User'),
			'conditions' => array('User.userId' => $id),
			'order' => 'CreditTracking.creditTrackingId DESC',
			'limit' => 1,
		));

		$this->data['CreditTracking'] = $cof[0]['CreditTracking'];

		$this->set('user', $this->data);
		$this->set(compact('user', 'salutationIds', 'paymentTypes', 'addressTypes', 'mailingListIds'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->flash(__('Invalid User', true), array('action'=>'index'));
		}
		if ($this->User->del($id)) {
			$this->flash(__('User deleted', true), array('action'=>'index'));
		}
	}

	function search()
	{
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
		}
		if(strlen($this->params['form']['query']) <= 3) {
			return;
		}
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);
			$origQuery = $query;
			$parts = explode(' ', $query);

			$query = '';
			foreach ($parts as $part) {
			    if (strlen($part) > 2) {
			        $query .= '+';
			    }
			    $query .= $part.' ';
			}

		    $conditions = array(
		    	'OR' => array(
		    		"MATCH(User.lastName,User.firstName) AGAINST('$query' IN BOOLEAN MODE)",
	    		),
			);

			$noSpace = FALSE;
			$isEmail = false;
			
			$fields = array('UserSiteExtended.username', 'User.userId', 'User.firstName', 'User.lastName', 'User.email', 'User.inactive');
			$order = array();
			
			if (intval($origQuery) !== 0) {
				$conditions['OR']['User.userId'] = $origQuery;
			} elseif (strpos($origQuery, " ") === FALSE) {
				$noSpace = true;
				
				if (strpos($origQuery, "@")) {
					$isEmail = true;
					$fields[] = "(CASE WHEN User.email LIKE '".$origQuery."' THEN 1 ELSE 0 END) AS emailmatch";
					$conditions['OR']['User.email LIKE'] = '%'.$origQuery.'%';
					$order[] = 'emailmatch DESC';
				} else {
					$order[] = 'usernamematch DESC';
					$fields[] = "(CASE WHEN UserSiteExtended.username LIKE '".$origQuery."' THEN 1 ELSE 0 END) AS usernamematch";
					$conditions['OR']['UserSiteExtended.username LIKE'] = "%$origQuery%";
				}
			}
			
			if($_GET['query'] ||  $this->params['named']['query']) {
				$this->autoRender = false;
                $this->User->Behaviors->attach('Containable');
				$this->paginate = array(
					'conditions' => $conditions, 
					'contain' => array('UserSiteExtended', 'Ticket'), 
					'fields' => $fields,
					'order' => $order,
				);
				
				$this->set('query', $query);

				$this->set('users', $this->paginate());
				$this->render('index');
			} else {
				$this->User->Behaviors->attach('Containable');
        		$this->User->contain(array('User','UserSiteExtended'));
				$results = $this->User->find('all', array('conditions' => $conditions, 'limit' => 5));
				$this->set('query', $query);
				$this->set('results', $results);
				var_dump($results);
				return $results;
			}
		endif;
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
				$referrerStatus = 1;
				$referrerBonus = 1;
			}
			
			$data = array(
				'siteId' => $toLink['User']['siteId'],
				'referrerUserId' => $id,
				'referredEmail' => $toLink['User']['email'],
				'statusTypeId' => $referrerStatus,
				'referrerBonusApplied' => 0,
				'referredBonusApplied' => 1,
			);

			if ($this->UserReferrals->save($data)) {
				$refId = $this->UserReferrals->getLastInsertId();
				
				/*
				if ($referrerBonus) {
					// Apply credit to referrer
					$this->UserReferrals->completeReferral($refId,3);
				}
				*/
				
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

			if (isset($x['Ticket']) && is_array($x['Ticket']) && count($x['Ticket']) > 0) {
				$r['UserReferrals']['hasPurchase'] = 1;
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
