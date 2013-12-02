<?php
class PressesController extends AppController {

	var $name = 'Presses';
	var $helpers = array('Html', 'Form');
	
	function beforeFilter() {
	    parent::beforeFilter();
	    $this->pageTitle = "Press/Reviews";
	}

	function index() {
		$this->Press->recursive = 0;
		$this->set('presses', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Press.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('press', $this->Press->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Press->create();
			if ($this->Press->save($this->data)) {
				$this->Session->setFlash(__('The Press has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Press could not be saved. Please, try again.', true));
			}
		}
		
		$pressTypeIds = $this->Press->PressType->find('list');
		$this->set(compact('pressTypeIds'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Press', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Press->save($this->data)) {
				$this->Session->setFlash(__('The Press has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Press could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Press->read(null, $id);
		}
		
		$pressTypeIds = $this->Press->PressType->find('list');
		$this->set(compact('pressTypeIds'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Press', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Press->del($id)) {
			$this->Session->setFlash(__('Press deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function search()
	{
	    $inactive = 0;
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
			$inactive = @$_GET['inactive'];
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
		}
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);

			$this->Press->recursive = 1;

			$conditions = array("Press.clientId = '$query' OR Client.name LIKE '$query%'");
			
			if (!$inactive) {
			    $conditions['Press.inactive'] = 0;
			}
			$results = $this->Press->find('all', array('conditions' => $conditions, 'limit' => 5));

			$this->set('query', $query);
			$this->set('results', $results);
			
			if (isset($this->params['requested'])) {
				return $results;
			} elseif(@$_GET['query'] || @ $this->params['named']['query']) {
				$this->autoRender = false;
				$this->Client->recursive = 0;

				$this->paginate = array('conditions' => $conditions);
				$this->set('query', $query);
				$this->set('presses', $this->paginate());
				$this->render('index');
			}
		endif;
	}

}
?>