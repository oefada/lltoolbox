<?php
class SearchRedirectsController extends AppController {

	var $name = 'SearchRedirects';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->SearchRedirect->recursive = 0;
		
		if (isset($this->params['named']['query'])) {
			$query = $this->Sanitize->escape($this->params['named']['query']);
			$conditions = array(
				'OR' => array(
					'keyword LIKE' => '%'.$query.'%',
				),
			);
			
			$this->set('query',$query);
		} else {
			$conditions = array();
		}
		
		$this->paginate = array(
			'conditions' => $conditions,
		);

		$this->set('searchRedirects', $this->paginate());
	}
	
	function add() {
		if (!empty($this->data)) {
			$this->SearchRedirect->create();
			if ($this->SearchRedirect->save($this->data)) {
				$this->Session->setFlash(__('The SearchRedirect has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SearchRedirect could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid SearchRedirect', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->SearchRedirect->save($this->data)) {
				$this->Session->setFlash(__('The SearchRedirect has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SearchRedirect could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->SearchRedirect->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SearchRedirect', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->SearchRedirect->del($id)) {
			$this->Session->setFlash(__('SearchRedirect deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function search()
	{
		$this->redirect(array('action'=>'index','query' => $this->params['url']['query']));
	}


}
?>