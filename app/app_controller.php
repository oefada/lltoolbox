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
class AppController extends Controller {
	var $helpers = array('Html2', 'Form', 'Text', 'Pagination', 'Layout', 'Ajax', 'StrictAutocomplete', 'Number', 'DatePicker', 'Prototip', 'Session');
	var $components = array('RequestHandler', 'Auth');
	var $Sanitize;
	
	function beforeFilter() {
        $this->Auth->loginAction = array('controller' => 'sessions', 'action' => 'login');  
        $this->Auth->loginRedirect = array('controller' => 'pages', 'action' => 'display', 'home');
        $this->Auth->allow('none');
        $this->Auth->loginError = "Could not log you in, please try again.";
        $this->Auth->authError = "Insufficient access rights.<br />Must be logged in, or logged in with elevated access.";
        $this->Auth->authorize = 'controller';
        
        $user = $this->Auth->user();
        $this->set('user', $user);
        $this->set('userDetails', $user['User']['LdapUser']);
        
		$this->Sanitize = new Sanitize();
		
		if($this->RequestHandler->isAjax()) {
			Configure::write('debug', '0');
		}
		
		if ($this->RequestHandler->prefers('pdf')) {
		    error_reporting(E_ERROR);
		    Configure::write('debug', '0');
		}
	}
	
	function isAuthorized() {  
	    return true;  
	}
}
?>