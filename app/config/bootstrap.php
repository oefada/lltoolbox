<?php
/* SVN FILE: $Id$ */
/**
* Short description for file.
*
* Long description for file
*
* PHP versions 4 and 5
*
* CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
* Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
*
* Licensed under The MIT License
* Redistributions of files must retain the above copyright notice.
*
* @filesource
* @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
* @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
* @package       cake
* @subpackage    cake.app.config
* @since         CakePHP(tm) v 0.10.8.2117
* @version       $Revision$
* @modifiedby    $LastChangedBy$
* @lastmodified  $Date$
* @license       http://www.opensource.org/licenses/mit-license.php The MIT License
*/
/**
*
* This file is loaded automatically by the app/webroot/index.php file after the core bootstrap.php is loaded
* This is an application wide file to load any function that is not used within a class define.
* You can also use this to include or require any files in your application.
*
*/
/**
* The settings below can be used to set additional paths to models, views and controllers.
* This is related to Ticket #470 (https://trac.cakephp.org/ticket/470)
*
* $modelPaths = array('full path to models', 'second full path to models', 'etc...');
* $viewPaths = array('this path to views', 'second full path to views', 'etc...');
* $controllerPaths = array('this path to controllers', 'second full path to controllers', 'etc...');
*
*/
if (!defined('REVISION')) {
	if (file_exists(APP . '.svn' . DS . 'entries')) {
		$svn = file(APP . '.svn' . DS . 'entries');
			 if (is_numeric(trim($svn[3]))) {
					 $version = $svn[3];
			 } else { // pre 1.4 svn used xml for this file
					 $version = explode('"', $svn[4]);
					 $version = $version[1];    
			 }
			 define ('REVISION', trim($version));
			 unset ($svn);
			 unset ($version);
	 } else {
			 define ('REVISION', 0); // default if no svn data avilable
	 }
}

/**
* Handle logging errors in production mode
*/
if (Configure::read() === 0) {

	// Disable the default handling and include logger
	define('DISABLE_DEFAULT_ERROR_HANDLING', 1);
	uses('cake_log');
	error_reporting(E_ALL & ~E_DEPRECATED);

	/**
	* A function to directly log errors
	*
	* @param $errno The error number
	* @param $errstr The error description
	* @param $errfile The file where the error occured
	* @param $errline The line of the file where the error occured
	* @return bool Success
	*/
	function productionError($errno, $errstr, $errfile, $errline) {
		// Ignore E_STRICT and suppressed errors
		if ($errno === 2048 || error_reporting() === 0) {
		return;
	}

	// What type of error
	$level = LOG_DEBUG;
	switch ($errno) {
		case E_PARSE:
		case E_ERROR:
		case E_CORE_ERROR:
		case E_COMPILE_ERROR:
		case E_USER_ERROR:
		$error = 'Fatal Error';
		$level = LOG_ERROR;
	break;
		case E_WARNING:
		case E_USER_WARNING:
		case E_COMPILE_WARNING:
		//case E_RECOVERABLE_ERROR:
		// PHP Notice:  Use of undefined constant E_RECOVERABLE_ERROR - assumed 'E_RECOVERABLE_ERROR'
		$error = 'Warning';
		$level = LOG_WARNING;
	break;
		case E_NOTICE:
		case E_USER_NOTICE:
		$error = 'Notice';
		$level = LOG_NOTICE;
	break;
		default:
		return false;
	break;
	}

	// Log
	CakeLog::write($level, sprintf('%s (%d): %s in [%s, line %d]',
	$error, $errno, $errstr, $errfile, $errline));

	// Die if fatal
	if ($level === LOG_ERROR) {
		die();
	}

		return true;
	}

	// Use the above handling
	set_error_handler('productionError');

}

//LuxuryLink address
//@TODO, consolidate constants and definations in a centeral location, for simple updates.
define('ADDRESS_LL','5510 Lincoln Blvd, Suite 275. Los Angeles, CA 90094');

?>
