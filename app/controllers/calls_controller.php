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

	function ajax()
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			Configure::write('debug', 0);
			header('Content-type: application/json');
		}
		$data = array();
		$data['asdf']='jkl';
		$this->set('ajax_for_layout', $data);
	}

	function beforeRender()
	{
		$this->set('username', ((isset($this->viewVars['user']['LdapUser']['username']) ? $this->viewVars['user']['LdapUser']['username'] : false)));
		parent::beforeRender();
	}

}
