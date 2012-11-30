<?php

$loadModules = array(
	'BlockMarkupModule',
	'BlockPageModule',
	'BlockDivModule',
);
foreach ($loadModules as $blockModuleName) {
	App::import('Vendor', $blockModuleName, array('file' => 'appshared' . DS . 'modules' . DS . $blockModuleName . DS . $blockModuleName . '.php'));
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
