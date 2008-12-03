<?php
uses('sanitize');
class PopularTravelIdeasController extends AppController {

	var $name = 'PopularTravelIdeas';
	var $helpers = array('Html', 'Form', 'Text');

	function index() {
		$this->PopularTravelIdea->recursive = 2;
		$this->set('popularTravelIdeas', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PopularTravelIdea.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('popularTravelIdea', $this->PopularTravelIdea->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->PopularTravelIdea->create();
			if ($this->PopularTravelIdea->save($this->data)) {
				$this->Session->setFlash(__('The PopularTravelIdea has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The PopularTravelIdea could not be saved. Please, try again.', true));
			}
		}
		$styleIds = $this->PopularTravelIdea->Style->find('list');
		$this->set(compact('styleIds'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PopularTravelIdea', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->PopularTravelIdea->save($this->data)) {
				$this->Session->setFlash(__('The PopularTravelIdea has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The PopularTravelIdea could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PopularTravelIdea->read(null, $id);
		}
		$styleIds = $this->PopularTravelIdea->Style->find('list');
		$this->set(compact('styleIds'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for PopularTravelIdea', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PopularTravelIdea->del($id)) {
			$this->Session->setFlash(__('PopularTravelIdea deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function search() {
		if (!empty($this->params['form']['query'])) {
			$query = $this->Sanitize->paranoid($this->params['form']['query']);
			$this->set('query', $query);
			if (strlen($query) > 0) {
				$results = $this->PopularTravelIdea->findAll("style.styleName LIKE '%".$query."%' OR popularTravelIdeaId LIKE '%".$query."%' OR styleId LIKE '%".$query."%' OR popularTravelIdeaName LIKE '%".$query."%' OR keywords LIKE '%".$query."%' OR linkToMultipleStyles LIKE '%".$query."%'");
				$this->set('results', $results);
			}
		}
		$this->layout = 'ajax';
		Configure::write('debug', '0');
	}
	
	function beforeFilter() {
		$this->Sanitize = new Sanitize();
	}
}
?>