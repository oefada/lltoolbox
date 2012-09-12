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

	function omnibox()
	{
		$this->layout = 'default';
		if ($search = isset($this->data['Call']['Search']) ? trim($this->data['Call']['Search']) : false) {
			if (preg_match('/^.*@.*\..*$/', $search)) {
				$this->_emailSearch($search);
			} elseif (preg_match('/^[A-Za-z]+[ ]+[A-Za-z]+$/', $search)) {
				$this->_nameSearch($search);
			} elseif (preg_match('/^[A-Za-z][A-Za-z\-0-9]*$/', $search)) {
				$this->_usernameSearch($search);
			} elseif (is_numeric($search) && (intval($search) <= 99999)) {
				$this->_clientSearch($search);
			} elseif (is_numeric($search) && (intval($search) <= 999999)) {
				$this->_ticketSearch($search);
			}
		}
		$this->set('search', $search);
	}

	private function _ticketSearch($ticketId)
	{
			$this->Session->setFlash('Searching for ticket #' . $ticketId);
		$this->redirect(array(
			'controller' => 'tickets',
			'action' => 'view',
			$ticketId,
		));
	}

	private function _clientSearch($clientId)
	{
		$this->Session->setFlash('Searching for client #' . $clientId);
		$this->redirect(array(
			'controller' => 'clients',
			'action' => 'view',
			$clientId,
		));
	}

	private function _usernameSearch($username)
	{
		$this->Session->setFlash('Searching for username: ' . $username);
		$this->redirect(array(
			'controller' => 'users',
			'action' => 'search',
			'username' => $username,
		));
	}

	private function _emailSearch($email)
	{
		$this->Session->setFlash('Searching for email address: ' . $email);
		$this->redirect('/users/search?query=' . $email);
	}

	private function _nameSearch($fullname)
	{
		$this->Session->setFlash('Searching for user with the name: ' . ucwords($fullname));
		$name = split(' ', $fullname);
		$this->redirect(array(
			'controller' => 'users',
			'action' => 'search',
			'firstName' => ucwords($name[0]),
			'lastName' => ucwords($name[1]),
		));
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
