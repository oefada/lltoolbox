<?php
class SchedulingInstancesController extends AppController {

	var $name = 'SchedulingInstances';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->SchedulingInstance->recursive = 0;
		$this->set('schedulingInstances', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid SchedulingInstance.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('schedulingInstance', $this->SchedulingInstance->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->SchedulingInstance->create();
			if ($this->SchedulingInstance->save($this->data)) {
				$this->Session->setFlash(__('The SchedulingInstance has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SchedulingInstance could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid SchedulingInstance', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->SchedulingInstance->save($this->data)) {
				$this->Session->setFlash(__('The SchedulingInstance has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The SchedulingInstance could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->SchedulingInstance->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for SchedulingInstance', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->SchedulingInstance->del($id)) {
			$this->Session->setFlash(__('SchedulingInstance deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>