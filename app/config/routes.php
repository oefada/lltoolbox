<?php
/* SVN FILE: $Id$ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
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
 * @subpackage		cake.app.config
 * @since			CakePHP(tm) v 0.2.9
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.thtml)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
	
	
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
/**
 * Then we connect url '/test' to our test controller. This is helpful in
 * developement.
 */
	Router::connect('/tests', array('controller' => 'tests', 'action' => 'index'));
	
// ROUTES ADDED 09-09-08 for adding loa item to loa
	Router::connect('/loas/:loaId/loa_items/:action', array('controller' => 'LoaItems', 'action' => 'view'), array('loaId' => "[0-9]+"));
	
// ROUTES ADDED 09-16-08 for adding validity to package
	Router::connect('/packages/:packageId/packageValidityPeriods/:action', array('controller' => 'PackageValidityPeriods', 'action' => 'view'), array('packageId' => "[0-9]+"));
	
// ROUTES ADDED 09-17-08 for adding loa item to package
	Router::connect('/packages/:packageId/packageLoaItemRels/:action', array('controller' => 'PackageLoaItemRels', 'action' => 'view'), array('packageId' => "[0-9]+"));

// ROUTES ADDED 09-18-08 for adding rate period to loa item	
	Router::connect('/loaItems/:loaItemId/loaItemRatePeriods/:action', array('controller' => 'LoaItemRatePeriods', 'action' => 'view'), array('loaItemId' => "[0-9]+"));
	
// ROUTES ADDED 09-24-08 for adding package promo to package
	Router::connect('/packages/:packageId/packagePromos/:action', array('controller' => 'PackagePromos', 'action' => 'view'), array('packageId' => "[0-9]+"));
	
// ROUTES ADDED 10-13-08 for adding package promo to package
	Router::connect('/clients/:id', array('controller' => 'clients', 'action' => 'view'), array('id' => '[0-9]+', 'pass' => array('id')));
	Router::connect('/clients/:clientId/:controller/:action', array(), array('clientId' => '[0-9]+', 'pass' => array('clientId')));
	Router::connect('/clients/:clientId/:controller/:action/:id', array(), array('clientId' => '[0-9]+', 'id' => '[0-9]+', 'pass' => array('clientId','id')));
	
// ROUTES ADDED 10-15-08 for adding payment detail to the worksheet
	Router::connect('/worksheets/:worksheetId/:controller/:action', array(), array('worksheetId' => "[0-9]+"));
	
?>