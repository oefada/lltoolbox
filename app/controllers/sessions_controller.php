<?php
class SessionsController extends AppController {

	var $name = 'Sessions';
	var $uses = array('LdapUser');
	var $layout = 'login';
	
	function login() {
	}
	
	function logout(){
        $this->Session->setFlash('Logout');
        $this->LdapAuth->_isLoggedIn = false;
	    $this->redirect($this->LdapAuth->logout());
    }
    
}

?>