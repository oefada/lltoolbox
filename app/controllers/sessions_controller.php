<?php
class SessionsController extends AppController {

	var $name = 'Sessions';
	var $uses = array('LdapUser');
	var $layout = 'login';
	
	function login() {
	}
	
	function logout(){
        $this->Session->setFlash('Logout');
        $this->Auth->_isLoggedIn = false;
	    $this->redirect($this->LdapAuth->logout());
    }
}

?>