<?php
class SearchRedirectsController extends AppController {

	var $name = 'SearchRedirects';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->SearchRedirect->recursive = 0;
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
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
			$inactive = @$_GET['inactive'];
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
		}
		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);
						
			$conditions = array("keyword LIKE '$query%' OR redirectUrl LIKE '%$query%'");
			
			$results = $this->SearchRedirect->find('all', array('conditions' => $conditions, 'limit' => 5));

			$this->set('query', $query);
			$this->set('results', $results);
			
			if (isset($this->params['requested'])) {
				return $results;
			} elseif(@$_GET['query'] || @ $this->params['named']['query']) {
				$this->autoRender = false;
				$this->SearchRedirect->recursive = 0;

				$this->paginate = array('conditions' => $conditions);
				$this->set('query', $query);
				$this->set('searchRedirects', $this->paginate());
				$this->render('index');
			}
		endif;
	}


}
?>