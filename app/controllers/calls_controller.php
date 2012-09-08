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
		if (isset($_GET['thing']) && isset($_GET['value'])) {
			$data['requestThing'] = isset($_GET['thing']) ? $_GET['thing'] : '-= empty =-';
			$data['requestValue'] = isset($_GET['value']) ? $_GET['value'] : '-= empty =-';
			if (is_numeric($_GET['value'])) {
				$value = intval($_GET['value']);
				switch($_GET['thing']) {
					case 'users' :
						$this->loadModel('User');
						$this->User->create();
						$this->User->recursive = 0;
						$this->User->id = $value;
						$this->User->read();
						$data['User'] = array(
							'userId' => $this->User->data['User']['userId'],
							'name' => trim(ucwords($this->User->data['User']['firstName'] . ' ' . $this->User->data['User']['lastName'])),
						);
						break;
					case 'tickets' :
						$this->loadModel('Ticket');
						$this->Ticket->create();
						$this->Ticket->recursive = 0;
						$this->Ticket->id = $value;
						$this->Ticket->read();
						$data['Ticket'] = array('ticketId' => $this->Ticket->data['Ticket']['ticketId'], );
						$data['User'] = array(
							'userId' => $this->Ticket->data['User']['userId'],
							'name' => trim(ucwords($this->Ticket->data['User']['firstName'] . ' ' . $this->Ticket->data['User']['lastName'])),
						);
						$clients = reset($this->Ticket->getClientsFromPackageId($this->Ticket->data['Ticket']['packageId']));
						$value = $clients['Client']['clientId'];
					// break; NO BREAK, FALL THROUGH TO CLIENTS
					case 'clients' :
						$this->loadModel('Client');
						$this->Client->create();
						$this->Client->recursive = 0;
						$this->Client->id = $value;
						$this->Client->read();
						$data['Client'] = array(
							'clientId' => $this->Client->data['Client']['clientId'],
							'name' => trim(ucwords($this->Client->data['Client']['name'])),
						);
						break;
				}
			}
		}
		$data['timestamp'] = time();
		$this->set('ajax_for_layout', $data);
	}

	function beforeRender()
	{
		$this->set('username', ((isset($this->viewVars['user']['LdapUser']['username']) ? $this->viewVars['user']['LdapUser']['username'] : false)));
		parent::beforeRender();
	}

}
