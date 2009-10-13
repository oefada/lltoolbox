<?php
/* SVN FILE: $Id: app_controller.php 6311 2008-01-02 06:33:52Z phpnut $ */
/**
 * Short description for file.
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.app
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 6311 $
 * @modifiedby		$LastChangedBy: phpnut $
 * @lastmodified	$Date: 2008-01-01 22:33:52 -0800 (Tue, 01 Jan 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Short description for class.
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.app
 */
uses('sanitize');
App::import('Model', 'MessageQueue');
class AppController extends Controller {
	var $helpers = array('Html2', 'Html', 'Form', 'Text', 'Pagination', 'Layout', 'Ajax', 'StrictAutocomplete', 'Number', 'DatePicker', 'Prototip', 'Session');
	var $components = array('Acl', 'LdapAuth', 'RequestHandler');
	var $publicControllers = array('sessions');
	var $Sanitize;
	
	function beforeFilter() {
	    if (isset($this->LdapAuth)) {
            $this->LdapAuth->loginAction = array('controller' => 'sessions', 'action' => 'login');
            $this->LdapAuth->loginError = "Could not log you in, please try again.";
            $this->LdapAuth->authError = "Insufficient access rights.<br />Must be logged in, or logged in with elevated access.";
            $this->LdapAuth->userModel = 'AdminUser';
            $this->LdapAuth->authorize = 'controller';
            
            if (in_array(low($this->params['controller']), $this->publicControllers)) {
            	    $this->LdapAuth->allow();
            }
            
            $user = $this->LdapAuth->user();
            $this->user = $user;
            $this->set('user', $user);
            $this->set('userDetails', $user['LdapUser']);
        }
                
		$this->Sanitize = new Sanitize();
		
		if($this->RequestHandler->isAjax()) {
			Configure::write('debug', '0');
		}
		
		if ($this->RequestHandler->prefers('pdf') || $this->RequestHandler->prefers('doc')) {
		    error_reporting(E_ERROR);
		    Configure::write('debug', '0');
		}

		if (isset($this->{$this->modelClass}) && is_object($this->{$this->modelClass}) && 
			isset($this->{$this->modelClass}->Behaviors) &&
			$this->{$this->modelClass}->Behaviors->attached('Logable')) {
     	    $this->{$this->modelClass}->setUserData($user);
     	    $this->{$this->modelClass}->setUserIp($this->_userIp());
     	 }
     	 
     	 $messageQueue = new MessageQueue;

         $unread = $messageQueue->total(array('toUser' => $user['LdapUser']['username'], 'read <>' => 1));
         $severity = $messageQueue->total(array('toUser' => $user['LdapUser']['username'], 'read <>' => 1, 'severity' => 3));

     	 $this->set('queueCountUnread', $unread);
     	 $this->set('queueCountSeverity', $severity);
		 $this->set('sites', array('luxurylink' => 'Luxury Link', 'family' => 'Family'));
     	 $this->_defineConstants();
	}
	
	/**
	 * Method used to define constants for things we use repeatedly. Examples are the ID's for certain offer types.
	 */
	function _defineConstants() {
	    define('OFFER_TYPES_FIXED_PRICED', serialize(array(3, 4)));
	    define('OFFER_TYPES_AUCTION', serialize(array(1, 2, 6)));
	}
	
	function _userIp() {
	    if (@$_SERVER['HTTP_X_FORWARD_FOR']) {
            $ip = $_SERVER['HTTP_X_FORWARD_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
	}
	
	function isAuthorized() {
	    return true;
	}
}
?>