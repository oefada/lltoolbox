<?php
class DealOfTheDaysController extends AppController {

	var $name = 'DealOfTheDays';
	var $helpers = array('Html', 'Javascript', 'Ajax');


	function index() {
		$this->DealOfTheDay->recursive = 0;
		$this->paginate['limit'] = 100;
		$this->set('deals', $this->paginate());
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid DealOfTheDay', true));
			$this->redirect(array('action'=>'index'));
		}

		if (!empty($this->data)) {
			if ($this->DealOfTheDay->save($this->data)) {
				$this->Session->setFlash(__('The DealOfTheDay has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The DealOfTheDay could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->DealOfTheDay->read(null, $id);
		}
		$this->set(compact('menus'));
	}


	function add() {
		if (!empty($this->data)) {
			$this->DealOfTheDay->create();
			if ($this->DealOfTheDay->save($this->data)) {
				$this->Session->setFlash(__('The DealOfTheDay has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The DealOfTheDay could not be saved. Please, try again.', true));
			}
		}
		//$menus = $this->DealOfTheDay->Menu->find('list');
	}


	/* disable for now

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
