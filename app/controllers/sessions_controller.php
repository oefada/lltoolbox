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
    
    
    function masquerade($user = null) {
        $currentUser = $this->LdapAuth->user();

        if ((!$currentUser || !in_array('Geeks', $currentUser['LdapUser']['groups'])) && $user != 'revert') {
            $this->redirect('/');
        }
        
        if ('revert' == $user) {
            if (!$currentUser['LdapUser']['masquerading']) {
                $this->redirect('/');
            } else {
        	    $this->Session->write($this->LdapAuth->sessionKey, $currentUser['LdapUser']['originalUser']['LdapUser']);
        	    
        	    $this->Session->setFlash('Back to being yourself');
        	    $this->redirect('/');
            }
        }
        
        $masqueradeUser = $this->LdapUser->read(null, $user);
        
        $masqueradeUser['LdapUser']['username'] = $masqueradeUser['LdapUser']['samaccountname'];
        $masqueradeUser['LdapUser']['password'] = '';
        $masqueradeUser['LdapUser']['originalUser'] = $currentUser;
        $masqueradeUser['LdapUser']['masquerading'] = true;
        
	    $this->Session->write($this->LdapAuth->sessionKey, $masqueradeUser['LdapUser']);
	    
	    $this->Session->setFlash('Now masquerading as '.$user);
	    $this->redirect('/');
    }
}

?>