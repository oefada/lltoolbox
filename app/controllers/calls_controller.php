<?php
class CallsController extends AppController
{

	var $name = 'Calls';
	var $uses = array('Call');
	var $helpers = array(
		'Html',
		'Form'
	);
	var $layout = 'cstool';

	function index()
	{
		$this->redirect(array('controller' => 'users'));
	}

	function popup()
	{
	}

}
