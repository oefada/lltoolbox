<?php
class CallsController extends AppController
{

	var $name = 'Calls';
	var $uses = array('Call');
	var $helpers = array(
		'Html',
		'Form'
	);
	var $layout = 'cstool';

	function index()
	{
		$this->Call->recursive = 0;
		$this->set('calls', $this->paginate());
	}
/*
	function view($id = null)
	{
		if (!$id) {
			$this->Session->setFlash(__('Invalid Call', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('call', $this->Call->read(null, $id));
	}

	function add()
	{
		if (!empty($this->data)) {
			$this->Call->create();
			if ($this->Call->save($this->data)) {
				$this->Session->setFlash(__('The Call has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Call could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null)
	{
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Call', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Call->save($this->data)) {
				$this->Session->setFlash(__('The Call has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The Call could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Call->read(null, $id);
		}
	}

	function delete($id = null)
	{
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Call', true));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->Call->del($id)) {
			$this->Session->setFlash(__('Call deleted', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('The Call could not be deleted. Please, try again.', true));
		$this->redirect(array('action' => 'index'));
	}
*/
}
