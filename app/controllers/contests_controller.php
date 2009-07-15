<?php
class ContestsController extends AppController {

	var $name = 'Contests';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Contest->recursive = 0;
		$this->paginate['limit'] = 200;
		$this->set('contests', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Contest.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('contest', $this->Contest->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Contest->create();
			if ($this->Contest->save($this->data)) {
				$this->Session->setFlash(__('The Contest has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Contest could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Contest', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Contest->save($this->data)) {
				$this->Session->setFlash(__('The Contest has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Contest could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Contest->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Contest', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Contest->del($id)) {
			$this->Session->setFlash(__('Contest deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>
