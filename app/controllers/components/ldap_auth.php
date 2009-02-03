<?php
App::import('Component', 'Auth');

class LdapAuthComponent extends AuthComponent {
        var $ldapModel = 'LdapUser';

        function startup(&$controller) {
                if (isset($controller->data[$this->userModel])) {
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