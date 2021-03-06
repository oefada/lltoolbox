<?php
class DestinationsController extends AppController {

	var $name = 'Destinations';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->Destination->recursive = 0;
		$this->set('destinations', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Destination.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('destination', $this->Destination->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Destination->create();

			if ($this->Destination->save($this->data)) {
				$this->Session->setFlash(__('The Destination has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Destination could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Destination', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Destination->save($this->data)) {
				$this->Session->setFlash(__('The Destination has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Destination could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Destination->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Destination', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Destination->del($id)) {
			$this->Session->setFlash(__('Destination deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

	function get_parent_tree() {
		$tree = $this->Destination->getLineageForId($this->params['url']['id']);
		echo json_encode(array('tree'=>$tree));
		exit;
		
	}


}
?>