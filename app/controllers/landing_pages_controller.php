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
		$this->redirect(array('controller' => 'menus'));
	}
	
	function add() {
		$this->redirect(array('controller' => 'menus'));
	}
	
}
?>