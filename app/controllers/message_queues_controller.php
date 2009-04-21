<?php
class MessageQueuesController extends AppController {

	var $name = 'MessageQueues';
	var $helpers = array('Html', 'Form');

	function index() {
	    $this->autoRender = false;
	    
		$this->MessageQueue->recursive = 0;
		$this->set('messageQueues', $this->paginate(array("MessageQueue.toUser = '{$this->user['LdapUser']['username']}'")));
		
		if ($this->RequestHandler->isAjax()) {
		    $this->render('list');
		} else {
		    $this->render('index');
		}
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid MessageQueue.', true));
			$this->redirect(array('action'=>'index'));
		}
	
		$this->set('messageQueue', $this->MessageQueue->read(null, $id));
		$this->MessageQueue->saveField('read', true);
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for MessageQueue', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->MessageQueue->del($id)) {
			$this->Session->setFlash(__('MessageQueue deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>