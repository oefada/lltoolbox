<?php
class ClientScoresController extends AppController {

	var $name = 'ClientScores';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');

	function index() {
		$this->ClientScore->recursive = 0;
		$this->ClientScore->Client->recursive = -1;
		$this->set('clientScores', $this->paginate());
	}

	function add() {
		$this->ClientScore->Client->recursive = -1;
		if (!empty($this->data)) {
			$this->ClientScore->create();
			if ($this->ClientScore->save($this->data)) {
				$this->Session->setFlash(__('The ClientScore has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The ClientScore could not be saved. Please, try again.', true));
			}
		}
		$clientScoreTypes = $this->ClientScore->ClientScoreType->find('list');
		$clients = $this->ClientScore->Client->find('list');
		$this->set(compact('clientScoreTypes', 'clients'));
		$this->set('clientScoreTypeIds', $clientScoreTypes);
	}

	function edit($id = null) {
		$this->ClientScore->Client->recursive = -1;
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid ClientScore', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->ClientScore->save($this->data)) {
				$this->Session->setFlash(__('The ClientScore has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The ClientScore could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->ClientScore->read(null, $id);
		}
		$clientScoreTypes = $this->ClientScore->ClientScoreType->find('list');
		$clients = $this->ClientScore->Client->find('list');
		$this->set(compact('clientScoreTypes','clients'));
		$this->set('clientScoreTypeIds', $clientScoreTypes);
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for ClientScore', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->ClientScore->del($id)) {
			$this->Session->setFlash(__('ClientScore deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>
