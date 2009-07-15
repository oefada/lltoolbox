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
			
			// get clientIds for ContestClientRel
			foreach (explode(',', $this->data['Contest']['clientIds']) as $key => $value) {
				if (is_numeric($value)) {
					$client_row = $this->Contest->query("SELECT 1 FROM client WHERE clientId = $value");
					if (!empty($client_row[0])) {
						$this->data['ContestClientRel'][] = array('clientId' => $value);
					}	
				}				
			}
			
			$this->Contest->create();
			if ($this->Contest->saveAll($this->data)) {
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
			
			// get clientIds for ContestClientRel
			foreach (explode(',', $this->data['Contest']['clientIds']) as $key => $value) {
				if (is_numeric($value)) {
					$client_row = $this->Contest->query("SELECT 1 FROM client WHERE clientId = $value");
					if (!empty($client_row[0])) {
						$this->data['ContestClientRel'][] = array('clientId' => $value);
					}	
				}				
			}
			$del_client_ids = $this->Contest->query("DELETE FROM contestClientRel WHERE contestId = $id");
			
			if ($this->Contest->saveAll($this->data)) {
				$this->Session->setFlash(__('The Contest has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Contest could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Contest->read(null, $id);
			$client_ids = $this->Contest->ContestClientRel->find('list', array('fields' => array('clientId'), 'conditions' => array('ContestClientRel.contestId' => $id)));
			$this->set('clientIds', implode(',', $client_ids));
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
