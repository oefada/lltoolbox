<?php
class ArticlesController extends AppController {

	var $name = 'Articles';
	var $helpers = array('Html', 'Form');
	var $uses = array('Article', 'LandingPage');

	function index() {
		$this->Article->recursive = 0;
		$this->set('articles', $this->paginate());
		$primaryStyles = $this->LandingPage->find('list', array('order' => 'landingPageName'));
		$this->set('primaryStyleIds', $primaryStyles);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Article.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('article', $this->Article->read(null, $id));
		$primaryStyles = $this->LandingPage->find('list', array('order' => 'landingPageName'));
		$this->set('primaryStyleIds', $primaryStyles);
	}

	function add() {
		if (!empty($this->data)) {
			$this->Article->create();
			if ($this->Article->save($this->data)) {
				$this->Session->setFlash(__('The Article has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Article could not be saved. Please, try again.', true));
			}
		}
		$primaryStyles = $this->LandingPage->find('list', array('order' => 'landingPageName'));
		$this->set('primaryStyleIds', $primaryStyles);
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Article', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Article->save($this->data)) {
				$this->Session->setFlash(__('The Article has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Article could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Article->read(null, $id);
		}
		$primaryStyles = $this->LandingPage->find('list', array('order' => 'landingPageName'));
		$this->set('primaryStyleIds', $primaryStyles);
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Article', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Article->del($id)) {
			$this->Session->setFlash(__('Article deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>
