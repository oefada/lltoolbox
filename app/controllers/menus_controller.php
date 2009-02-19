<?php
class MenusController extends AppController {

	var $name = 'Menus';
	var $helpers = array('Html', 'Form', 'Ajax', 'Javascript');
	var $uses = array('Menu', 'LandingPage');
	var $components = array('RequestHandler');

	function index() {
		$this->LandingPage->recursive = 1;
		
		$this->set('landingPages', $this->paginate('LandingPage'));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Menu.', true));
			$this->redirect(array('action'=>'index'));
		}

		$this->set('menu', $this->Menu->read(null, $id));
	}
	
	function edit_by_landing_page($landingPageId = null) {
		$this->LandingPage->recursive = 2;
		$menus = $this->LandingPage->read(null, $landingPageId);
		
		usort($menus['Menu'], array("Menu", "__sort"));
		
		//can't use association to find the menuItems otherwise the weight order is not maintained
		foreach($menus['Menu'] as $mk=>&$menu) {
			$menu['MenuItem'] = array();
			$menuItems = $this->Menu->MenuItem->find('all', array('conditions' => array('MenuItem.menuId' => $menu['menuId'])));
		
			foreach($menuItems as &$menuItem) {
				$menu['MenuItem'][] = $menuItem['MenuItem'];
			}
			
		}
		$this->set('landingPageId', $landingPageId);
		$this->set('menus', $menus);
	}
	
	function fetch_title_image() {
		$this->layout = 'ajax';
		$menuTitleImage = $this->Menu->MenuTitleImage->read(null, $this->data['Menu']['menuTitleImageId']);
		
		$this->set('menuTitleImage', $menuTitleImage['MenuTitleImage']);
		
		$this->render('fetch_title_image', 'ajax');
	}

	function add() {
		if (!empty($this->data)) {
			$this->Menu->create();
			if ($this->Menu->save($this->data)) {
				$this->Session->setFlash(__('The Menu has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Menu could not be saved. Please, try again.', true));
			}
		}
		$landingPages = $this->Menu->LandingPage->find('list');
		
		if(isset($this->params['named']['landingPageId']) && is_numeric($this->params['named']['landingPageId'])) {
			$this->data['LandingPage']['LandingPage'][] = $this->params['named']['landingPageId'];
		}
		
		$menuTitleImageIds = $this->Menu->MenuTitleImage->find('list');
		
		$this->set(compact('landingPages', 'menuTitleImageIds'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Menu', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Menu->save($this->data)) {
				$this->Session->setFlash(__('The Menu has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Menu could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Menu->read(null, $id);
		}
		$landingPages = $this->Menu->LandingPage->find('list');
		$menuTitleImageIds = $this->Menu->MenuTitleImage->find('list');
		$this->set(compact('landingPages','menuTitleImageIds'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Menu', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Menu->del($id)) {
			$this->Session->setFlash(__('Menu deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

	function order() {
		if ($this->RequestHandler->isAjax()) {
			foreach ($this->params['form']['menus'] as $weight => $item) {
				$this->Menu->id = $item;
				$this->Menu->saveField('weight', $weight);
				}
			$this->autoRender = false;
		}
	}
}
?>