<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->User->recursive = -1;
		$this->set('users', $this->paginate());
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
	}

	function edit($id = null) {
		$this->set('userId', $id);
		if (!$id && empty($this->data)) {
			$this->flash(__('Invalid User', true), array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The User has been saved.', true));
			} else {
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
		$contests = $this->User->Contest->find('list');
		$clients = $this->User->Client->find('list');
		$salutationIds = $this->User->Salutation->find('list');
		$paymentTypes = $this->User->UserPaymentSetting->PaymentType->find('list');
		$addressTypes = $this->User->Address->AddressType->find('list');
		$this->set('user', $this->data);
		$this->set(compact('user', 'contests','clients','salutationIds', 'paymentTypes', 'addressTypes'));
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
			$conditions = array('OR' => array('User.firstName LIKE' => "$query%", 'User.lastName LIKE' => "$query%", 'User.email LIKE' => "$query%"));

			if($_GET['query'] ||  $this->params['named']['query']) {

				$this->autoRender = false;
				$this->User->recursive = -1;

				$this->paginate = array('conditions' => $conditions, 'contain' => array('User'), 'fields' => array('User.userId', 'User.firstName', 'User.lastName', 'User.email', 'User.inactive'));
				$this->set('query', $query);

				$this->set('users', $this->paginate());
				$this->render('index');
			} else {
				$this->User->recursive = -1;
				$results = $this->User->find('all', array('conditions' => $conditions, 'limit' => 5));
				$this->set('query', $query);
				$this->set('results', $results);
				return $results;
			}
		endif;
	}
	
	/**
	 * Method resets a user's password in the UserSiteExtended model
	 * @params $id the id of the row in the UserSiteExtended table NOT the id of the main user account
	 * @returns
	 */
	function resetPassword($id = null) {
		if(!empty($this->data)) {
			$newPassword = $this->generatePassword();
			$this->User->UserSiteExtended->id = $id;
			$this->User->UserSiteExtended->saveField('passwordHash', $newPassword);
			$this->set('newPassword', $newPassword);
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

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'customers');
		
		$userId = $this->User->id;

		if(!isset($id) && isset($this->params['pass'][0])){
			$userId = $this->params['pass'][0];
		}

		$this->User->recursive = 1;
		$user = $this->User->findByUserId($this->User->id);

		$this->set('user', $user);
		$this->set('userId', $this->User->id);
		$this->set('numUserTickets', $this->User->Ticket->find('count', array('conditions' => array('Ticket.userId' => $userId))));
		$this->set('numUserBids', $this->User->Bid->find('count', array('conditions' => array('Bid.userId' => $userId))));
	}
}
?>