<?php
class BidsController extends AppController {

	var $name = 'Bids';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Bid->recursive = 0;
		$this->set('bids', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Bid.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('bid', $this->Bid->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Bid->create();
			if ($this->Bid->save($this->data)) {
				$this->Session->setFlash(__('The Bid has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Bid could not be saved. Please, try again.', true));
			}
		}
		$users = $this->Bid->User->find('list');
		$offers = $this->Bid->Offer->find('list');
		$this->set(compact('users', 'offers'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Bid', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Bid->save($this->data)) {
				$this->Session->setFlash(__('The Bid has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Bid could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Bid->read(null, $id);
		}
		$users = $this->Bid->User->find('list');
		$offers = $this->Bid->Offer->find('list');
		$this->set(compact('users','offers'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Bid', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Bid->del($id)) {
			$this->Session->setFlash(__('Bid deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>