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
		$this->layout = 'default';
		if (isset($this->params['named']['format']) && $this->params['named']['format'] == 'csv') {
			Configure::write('debug', '0');
			$this->set('calls', $this->Call->find('all', array('order' => 'created DESC', )));
			$this->viewPath .= '/csv';
			$this->layoutPath = 'csv';
		} else {
			$this->paginate['order'] = array('Call.callId' => 'desc');
			$this->set('calls', $this->paginate());
		}
	}

	function omnibox()
	{
		$this->layout = 'default';
		$search = false;
		if (isset($this->data['Call']['Search'])) {
			$search = trim($this->data['Call']['Search']);
		} elseif (isset($_GET['q'])) {
			$search = trim($_GET['q']);
		}
		$this->set('search', $search);
	}

	function search()
	{
		$this->layout = 'default';
		$search = false;
		if (isset($this->data['Call']['Search'])) {
			$search = trim($this->data['Call']['Search']);
		} elseif (isset($_GET['q'])) {
			$search = trim($_GET['q']);
		}
		if ($search) {
			if (preg_match('/^~{0,1}email .*$/i', $search) || preg_match('/^.*@.*\..*$/', $search)) {
				$this->_emailSearch($search);
			} elseif (preg_match('/^~{0,1}name .*$/i', $search) || preg_match('/^~{0,1}user [A-Za-z]+ [A-Za-z]+$/i', $search) || preg_match('/^[A-Za-z]+[ ]+[A-Za-z]+$/', $search)) {
				$this->_nameSearch($search);
			} elseif (preg_match('/^~{0,1}username .*$/i', $search) || preg_match('/^~{0,1}user [A-Za-z0-9\-]+$/i', $search) || preg_match('/^[A-Za-z][A-Za-z\-0-9]*$/', $search)) {
				$this->_usernameSearch($search);
			} elseif (preg_match('/^~{0,1}userid [0-9]+$/i', $search)) {
				$this->_userIdSearch($search);
			} elseif (preg_match('/^~{0,1}client .*$/i', $search) || is_numeric($search) && (intval($search) <= 99999)) {
				$this->_clientSearch($search);
			} elseif (preg_match('/^~{0,1}ticket .*$/i', $search) || is_numeric($search) && (intval($search) <= 999999)) {
				$this->_ticketSearch($search);
			}
		}
		$this->set('search', $search);
	}

	private function _ticketSearch($ticketId)
	{
		$ticketId = preg_replace('/^~{0,1}ticket /i', '', $ticketId);
		$this->Session->setFlash('Searching for ticket #' . $ticketId);
		$this->redirect(array(
			'controller' => 'tickets',
			'action' => 'view',
			$ticketId,
		));
	}

	private function _clientSearch($clientId)
	{
		$clientId = preg_replace('/^~{0,1}client /i', '', $clientId);
		if (is_numeric($clientId)) {
			$this->Session->setFlash('Searching for client #' . $clientId);
			$this->redirect(array(
				'controller' => 'clients',
				'action' => 'edit',
				$clientId,
			));
		} else {
			$this->redirect(array(
				'controller' => 'clients',
				'action' => 'index',
				'query' => $clientId,
			));
		}
	}

	private function _usernameSearch($username)
	{
		$username = preg_replace('/^~{0,1}user /i', '', $username);
		$this->Session->setFlash('Searching for username: ' . $username);
		$this->redirect(array(
			'controller' => 'users',
			'action' => 'search',
			'username' => $username,
		));
	}

	private function _userIdSearch($userId)
	{
		$userId = preg_replace('/^~{0,1}userid /i', '', $userId);
		$this->Session->setFlash('Searching for userid: ' . $userId);
		$this->redirect(array(
			'controller' => 'users',
			'action' => 'view',
			$userId,
		));
	}

	private function _emailSearch($email)
	{
		$email = preg_replace('/^~{0,1}email /i', '', $email);
		$this->Session->setFlash('Searching for email address: ' . $email);
		$this->redirect('/users/search?query=' . $email);
	}

	private function _nameSearch($fullname)
	{
		$fullname = preg_replace('/^~{0,1}(name|user) /i', '', $fullname);
		$this->Session->setFlash('Searching for user with the name: ' . ucwords($fullname));
		$name = split(' ', $fullname);
		$this->redirect(array(
			'controller' => 'users',
			'action' => 'search',
			'firstName' => ucwords($name[0]),
			'lastName' => ucwords($name[1]),
		));
	}

	function popup($id = null)
	{
		$this->Call->recursive = 0;
		if (!empty($this->data)) {
			if (isset($_POST['loadTicket'])) {
				if (is_numeric($_POST['loadTicket'])) {
					$this->data = $this->Call->read(null, intval($_POST['loadTicket']));
					$this->set('callIdLabel', $_POST['loadTicket']);
				}
			} else {
				$this->data['Call']['representative'] = $this->viewVars['user']['LdapUser']['username'];
				foreach ($this->data['Call'] as $k => $v) {
					if (is_array($this->data['Call'][$k])) {
						$this->data['Call'][$k] = reset($v);
					}
				}
				if ($id) {
					$this->Call->create();
					$this->Call->id = $id;
				}
				if ($this->Call->save($this->data)) {
					$this->data = array();
				}
			}
		}
		$this->set('lastTenCalls', $this->Call->find('all', array(
			'conditions' => array(
				'Call.representative' => isset($this->viewVars['user']['LdapUser']['username']) ? $this->viewVars['user']['LdapUser']['username'] : 'none',
				'Call.created LIKE' => (date('Y-m-d')) . ' %',
			),
			'limit' => 10,
			'order' => 'Call.created DESC',
		)));
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
