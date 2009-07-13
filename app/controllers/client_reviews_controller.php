<?php
class ClientReviewsController extends AppController {

	var $name = 'ClientReviews';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->ClientReview->recursive = 0;
		$this->set('clientReviews', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid ClientReview.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('clientReview', $this->ClientReview->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->ClientReview->create();
			if ($this->ClientReview->save($this->data)) {
				$this->Session->setFlash(__('The ClientReview has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The ClientReview could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid ClientReview', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->ClientReview->save($this->data)) {
				$this->Session->setFlash(__('The ClientReview has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The ClientReview could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->ClientReview->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for ClientReview', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->ClientReview->del($id)) {
			$this->Session->setFlash(__('ClientReview deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>