<?php
class MenuItemsController extends AppController {

	var $name = 'MenuItems';

	function index() {
		$this->MenuItem->recursive = 0;
		
		$this->data = $this->MenuItem->find('threaded');

		$this->set('menuItems', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid MenuItem.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('menuItem', $this->MenuItem->read(null, $id));
	}

	function add($menuId = null) {
		if (!empty($this->data)) {
			$this->MenuItem->create();
			$this->data['MenuItem']['menuId'] = $menuId;
			if ($this->MenuItem->save($this->data)) {
				$this->Session->setFlash(__('The MenuItem has been saved', true));
				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
				} else {
					$this->redirect(array('action'=>'index'));
				}		
			} else {
				$this->Session->setFlash(__('The MenuItem could not be saved. Please, try again.', true));
			}
		}

		if (!isset($this->data['MenuItem']['externalLink'])) {
			$this->data['MenuItem']['externalLink'] = 0;
		}
		
		if (isset($menuId)) {
			$this->data['MenuItem']['menuId'] = $menuId;
		} else {
			$this->Session->setFlash(__('Invalid MenuId', true));
			$this->redirect(array('action' => 'index'));
		}
		
		$landingPages = $this->MenuItem->Menu->LandingPage->find('list');

		$this->set(compact('landingPages'));
	}

	function edit($menuId = null, $id = null) {
		
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid MenuItem', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->MenuItem->save($this->data)) {
				$this->Session->setFlash(__('The MenuItem has been saved', true));
				
				if ($this->RequestHandler->isAjax()) {
					$this->set('closeModalbox', true);
				} else {
					$this->redirect(array('action'=>'index'));
				}
			} else {
				$this->Session->setFlash(__('The MenuItem could not be saved. Please, try again.', true));
			}
		}
		
		if (empty($this->data)) {
			$this->data = $this->MenuItem->read(null, $id);
		}
		
		$landingPages = $this->MenuItem->Menu->LandingPage->find('list');
		$this->set(compact('landingPages'));
	}

	function delete($id = null) {
		if ($this->RequestHandler->isAjax()) {
			$this->autoRender = false;
		}
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for MenuItem', true));
			if ($this->RequestHandler->isAjax()) {
				return 'MenuItem could not be deleted';
			} else {
				$this->redirect(array('action'=>'index'));
			}
		}
		if ($this->MenuItem->del($id)) {
			if ($this->RequestHandler->isAjax()) {
				return;
			} else {
				$this->Session->setFlash(__('MenuItem deleted', true));
				$this->redirect(array('action'=>'index'));
			}
		} else {
			if ($this->RequestHandler->isAjax()) {
				return false;
			} else {
				$this->Session->setFlash(__('MenuItem could not be deleted', true));
				$this->redirect(array('action'=>'index'));
			}
		}
	}
	
	function order($menuId = null) {
		if ($this->RequestHandler->isAjax()) {
			foreach ($this->params['form']['menuItems_'.$menuId] as $weight => $item) {
				$this->MenuItem->id = $item;
				$this->MenuItem->saveField('weight', $weight);
				}
			$this->autoRender = false;
		}
	}
	
	function url_input_form() {
		$this->data['MenuItem']['externalLink'] = 1;
	}
	
	function landing_pages_select_form() {
		$this->data['MenuItem']['externalLink'] = 0;
		
		$landingPages = $this->MenuItem->Menu->LandingPage->find('list');
		
		$this->set(compact('landingPages'));
	}
}
?>