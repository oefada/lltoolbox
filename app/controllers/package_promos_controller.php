<?php
class PackagePromosController extends AppController {

	var $name = 'PackagePromos';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->PackagePromo->recursive = 0;
		$this->set('packagePromos', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PackagePromo.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('packagePromo', $this->PackagePromo->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->PackagePromo->create();
			if ($this->PackagePromo->save($this->data)) {
				$this->Session->setFlash(__('The PackagePromo has been saved', true));
				$this->redirect(array('controller' => 'packages', 'action'=>'view', 'id' => $this->data['PackagePromo']['packageId']));
			} else {
				$this->Session->setFlash(__('The PackagePromo could not be saved. Please, try again.', true));
			}
		}
		$this->data['PackagePromo']['packageId'] = $this->params['packageId'];
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PackagePromo', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->PackagePromo->save($this->data)) {
				$this->Session->setFlash(__('The PackagePromo has been saved', true));
				$this->redirect(array('controller' => 'packages', 'action'=>'view', 'id' => $this->data['PackagePromo']['packageId']));
			} else {
				$this->Session->setFlash(__('The PackagePromo could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PackagePromo->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for PackagePromo', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PackagePromo->del($id)) {
			$this->Session->setFlash(__('PackagePromo deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>