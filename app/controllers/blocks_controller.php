<?php

$modules = array(
	'Markup',
	'Block',
	'Div',
);

foreach ($modules as $module) {
	App::import('Vendor', $module . 'Module', array('file' => 'appshared' . DS . 'modules' . DS . $module . 'Module' . DS . $module . 'Module' . '.php'));
}

class BlocksController extends AppController
{
	var $name = 'Blocks';
	var $helpers = array('Html');
	var $uses = array();
	function index()
	{
	}

}

/*
 * Create this instead of loading the real 'Module'
 */
class Module
{
	function __construct()
	{
	}

}
