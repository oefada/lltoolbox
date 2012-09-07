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

	function beforeRender()
	{
		$this->set('username', ((isset($this->viewVars['user']['LdapUser']['username']) ? $this->viewVars['user']['LdapUser']['username'] : false)));
		parent::beforeRender();
	}

}
