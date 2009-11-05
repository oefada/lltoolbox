<?php
/* $Id$ */
/**
 * LdapAuthComponent
 *
 * This component is an extension of the built-in AuthComponent. This allows us to edit
 * certain methods of the parent component to allow it to work with our LDAP authentication.
 *
 * @filesource

 * @version       $Revision$
 * @modifiedby    $LastChangedBy$
 * @lastmodified  $LastChangedDate$
 */
App::import('Component', 'Auth');

class LdapAuthComponent extends AuthComponent {
        var $ldapModel = 'LdapUser';			//the name of the model that has the 'auth' method

		/**
		 * Standard component method. Is called when attached to a controller.
		 * We use this to pre authenticate a user if we already have them in our database.
		 *
		 * @param object $controller
		 * @return parent startup method
		 */
        function startup(&$controller) {
                //Need the is_array because control->data can be an XML object if it's a soap request
                if (is_array($controller->data) && isset($controller->data[$this->userModel])) {
                        $username = $controller->data[$this->userModel][$this->fields['username']];

                        $password = $controller->data[$this->userModel][$this->fields['password']];

                        $res = $this->preauthUser($username, $password);

                        if (!$res) {
                                //set password to blank to ensure the auth fails
                                $controller->data[$this->userModel][$this->fields['password']] ='';
                        }
                }
                
                //Continue with standard auth process
                return parent::startup(&$controller);
        }
		
		/**
		 * Attempt to authenticate the user against LDAP. If authentication succeeds,
		 * we store this user in the adminUsers database for future authentication.
		 * This allows the base AuthComponent to work as normal.
		 *
		 * @return boolean true on successful authentication, false otherwise
		 */
        function preauthUser($username, $password) {
                //TODO: un-hard-code the other database model fields.
                $ldap =& $this->getLdapModel();
                $model =& $this->getModel();

                $res = $ldap->auth($username, $password);
 
                if ($res !== false) {
                        //Successfull LDAP bind - update user database
                        $data = $model->findByUsername($username);
                        if (!$data) {
                                $data = array();
                                $data[$this->userModel][$this->fields['username']] = $username;
                                $data[$this->userModel]['created'] = date('Y-m-d H:i:s');
                        }
                        $data[$this->userModel]['displayName'] = $res[0][$this->ldapModel]['displayname'];
                        
                        //TODO: if data hasn't changed, avoid updating the database
                        $data[$this->userModel][$this->fields['password']] = $this->password($password);

                        $data[$this->userModel]['email'] = $res[0][$this->ldapModel]['mail'];

                        $model->save($data);
                        
                        $this->Session->setFlash('Welcome back, '.$res[0][$this->ldapModel]['givenname'], 'default', array(), 'success');
                        
                        return true;
                }
                return false;
        }

        function &getLdapModel($name = null) {
                $model = null;
                if (!$name) {
                        $name = $this->ldapModel;
                }

                if (PHP5) {
                        $model = ClassRegistry::init($name);
                } else {
                        $model =& ClassRegistry::init($name);
                }

                if (empty($model)) {
                        trigger_error(__('LdapAuth::getLdapModel() - Model is not set or could not be found', true), E_USER_WARNING);
                        return null;
                }

                return $model;
        }
        
    /**
 	 * Get the current user from the session.
 	 *
 	 * @param string $key field to retrive.  Leave null to get entire User record
 	 * @return mixed User record. or null if no user is logged in.
 	 * @access public
 	 */
	function user($key = null) {
	    $ldap =& $this->getLdapModel();
	    
		$this->__setDefaults();
		
		if (!$this->Session->check($this->sessionKey)) {
			return null;
		}
		
		$localUser = $this->Session->read($this->sessionKey);
		
		if (!isset($localUser['samaccountname'])) {
		    $user = $ldap->read(null, $localUser['username']);
		    $user = $user['LdapUser'];
		    $user['username'] = $localUser['username'];
		    $user['password'] = @$localUser['password'];
		    $this->Session->write($this->sessionKey, $user);
		}

		if ($key == null) {
			return array($this->ldapModel => $this->Session->read($this->sessionKey));
		} else {
			$user = $this->Session->read($this->sessionKey);
			if (isset($user[$key])) {
				return $user[$key];
			}
			return null;
		}

	}
}
?>