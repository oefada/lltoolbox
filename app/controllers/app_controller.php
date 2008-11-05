<?php
/* SVN FILE: $Id: app_controller.php 9 2008-10-10 05:08:45Z vgarcia $ */
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
 * @subpackage		cake.cake.libs.controller
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision: 9 $
 * @modifiedby		$LastChangedBy: vgarcia $
 * @lastmodified	$Date: 2008-10-09 22:08:45 -0700 (Thu, 09 Oct 2008) $
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * This is a placeholder class.
 * Create the same file in app/app_controller.php
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		cake
 * @subpackage	cake.cake.libs.controller
 */
uses('sanitize');
class AppController extends Controller {
	var $helpers = array('Html2', 'Form', 'Text', 'Pagination', 'Layout', 'Ajax', 'StrictAutocomplete', 'Number');
	var $components = array('RequestHandler');
	var $Sanitize;
	
	function beforeFilter() {
		$this->Sanitize = new Sanitize();
		
		if($this->RequestHandler->isAjax()) {
			Configure::write('debug', '0');
		}
	}
}
?>