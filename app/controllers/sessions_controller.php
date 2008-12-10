<?php
class SessionsController extends AppController {

	var $name = 'Sessions';
	var $uses = array('LdapUser');
	var $components = array('Auth');
	var $layout = 'login';
	
	function login() {
	    $this->Auth->logout();
	    if (!empty($this->data)) {
	        $user = $this->LdapUser->auth($this->data['LdapUser']['username'], $this->data['LdapUser']['password']); 
            $this->Auth->userModel = 'LdapUser';
	        if ($this->Auth->login($user)) {
				if ($this->Auth->autoRedirect) {
				    $this->Session->setFlash('Welcome back, '.$user['LdapUser']['givenname'], 'default', array(), 'success');
					$this->redirect($this->Auth->redirect(), null, true);
				}
			} else {
				$this->Session->setFlash($this->Auth->loginError, 'default', array(), 'auth');
				$this->data[$this->Auth->userModel]['password'] = null;
			}
	    }
	}
	
	function logout(){
        $this->Session->setFlash('Logout');
	    $this->redirect($this->Auth->logout());
    }
}

?>