<?php
class UsersController extends AppController {

	var $name = 'Users';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->flash(__('Invalid User', true), array('action'=>'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->flash(__('User saved.', true), array('action'=>'index'));
			} else {
			}
		}
		$contests = $this->User->Contest->find('list');
		$clients = $this->User->Client->find('list');
		$salutations = $this->User->Salutation->find('list');
		$this->set(compact('contests', 'clients', 'salutations'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->flash(__('Invalid User', true), array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->flash(__('The User has been saved.', true), array('action'=>'index'));
			} else {
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
		$contests = $this->User->Contest->find('list');
		$clients = $this->User->Client->find('list');
		$salutations = $this->User->Salutation->find('list');
		$this->set(compact('contests','clients','salutations'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->flash(__('Invalid User', true), array('action'=>'index'));
		}
		if ($this->User->del($id)) {
			$this->flash(__('User deleted', true), array('action'=>'index'));
		}
	}

}
?>