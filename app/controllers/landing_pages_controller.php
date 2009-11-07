<?php
class LandingPagesController extends AppController {

	var $name = 'LandingPages';
	var $helpers = array('Html', 'Javascript', 'Ajax');
	
	function search() {
		if (!empty($this->params['form']['query'])) {
			$query = $this -> Sanitize -> paranoid($this->params['form']['query']);
	
			if (strlen($query) > 0) {
				$result = $this->LandingPage->findAll("styleName LIKE '%".$query."%' OR styleId LIKE '%".$query."%'");
				$this->set('result', $result);
			}
		}
		$this->layout = 'ajax';
	}

	function index() {
		$this->LandingPage->recursive = 0;
		$this->paginate['limit'] = 100;
		$this->set('landingPages', $this->paginate());
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid LandingPage', true));
			$this->redirect(array('action'=>'index'));
		}
		
		if (!empty($this->data)) {
			if ($this->LandingPage->save($this->data)) {
				$this->Session->setFlash(__('The LandingPage has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The LandingPage could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->LandingPage->read(null, $id);
		}
		$menus = $this->LandingPage->Menu->find('list');
		$landingPageTypes = $this->LandingPage->LandingPageType->find('list');
		$this->set(compact('menus'));
		$this->set('landingPageTypeIds', $landingPageTypes);
	}

	/* disable for now
	
	function add() {
		if (!empty($this->data)) {
			$this->LandingPage->create();
			if ($this->LandingPage->save($this->data)) {
				$this->Session->setFlash(__('The LandingPage has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The LandingPage could not be saved. Please, try again.', true));
			}
		}
		$menus = $this->LandingPage->Menu->find('list');
		$landingPageTypes = $this->LandingPage->LandingPageType->find('list');
		$this->set(compact('menus', 'landingPageTypes'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for LandingPage', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->LandingPage->del($id)) {
			$this->Session->setFlash(__('LandingPage deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

	*/
}
?>
