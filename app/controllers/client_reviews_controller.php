<?php
class ClientReviewsController extends AppController {

	var $name = 'ClientReviews';
	var $helpers = array('Html', 'Form');

	function index() {
		
		if (isset($this->passedArgs[0]) && $this->passedArgs[0]=='clientId'){
			$clientId=$this->passedArgs[1];
			$this->paginate['conditions']=array("Client.clientId=$clientId");
		}
		$this->ClientReview->recursive = 0;
		$this->paginate['order'] = 'ClientReview.datetime DESC';
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
			$sites['luxurylink'] = (isset($this->data['ClientReview']['sites']) && in_array('luxurylink', $this->data['ClientReview']['sites'])) ? true : false;
			$sites['family'] = (isset($this->data['ClientReview']['sites']) && in_array('family', $this->data['ClientReview']['sites'])) ? true : false;
			$this->set('sites', $sites);
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
