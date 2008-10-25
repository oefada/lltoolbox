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

	function edit($id = null) {
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
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);
			$conditions = array('OR' => array('firstName LIKE' => "$query%",'lastName LIKE' => "$query%", 'email LIKE' => "$query%"));

			if($_GET['query'] ||  $this->params['named']['query']) {
				$this->autoRender = false;
				$this->Client->recursive = 0;

				$this->paginate = array('conditions' => $conditions);
				$this->set('query', $query);
				$this->set('users', $this->paginate());
				$this->render('index');
			} else {
				$this->Client->recursive = -1;
				$results = $this->User->find('all', array('conditions' => $conditions, 'limit' => 5));
				$this->set('query', $query);
				$this->set('results', $results);
				return $results;
			}
		endif;
	}
	
	function resetPassword($id = null) {
		if(!empty($this->data)) {
			$newPassword = $this->generatePassword();
		//	$this->User->find($this->data['userId']);
		//	$this->User->save($this->data);
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
	
	
	function beforeFilter() {
		$this->Sanitize = new Sanitize();
		
		if($this->RequestHandler->isAjax()) {
			Configure::write('debug', '0');
		}
	}
}
?>