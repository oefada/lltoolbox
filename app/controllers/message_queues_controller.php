<?php
class MessageQueuesController extends AppController {

	var $name = 'MessageQueues';
	var $helpers = array('Html', 'Form');

	function index() {
	    $this->autoRender = false;
	    
		$this->MessageQueue->recursive = 0;
		$unreadMessages = $this->paginate(array("MessageQueue.toUser = '{$this->user['LdapUser']['username']}'", 'read' => 0));
		$this->set('unreadMessages', $unreadMessages);
		$this->set('readMessages', $this->MessageQueue->find('all', array('conditions' => array("MessageQueue.toUser = '{$this->user['LdapUser']['username']}'", 'read' => 1))));
		
		if ($this->RequestHandler->isAjax()) {
			$this->set('messages', $unreadMessages);
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
	
	function ajax_get_totals() {
	    $this->autoRender = false;
	    
	    $unread = $this->MessageQueue->total(array('toUser' => $this->user['LdapUser']['username'], 'read <>' => 1));
        $severity = $this->MessageQueue->total(array('toUser' => $this->user['LdapUser']['username'], 'read <>' => 1, 'severity' => 3));

        echo "$unread, $severity";
	}
	
	function change_status() {
		$this->autoRender = false;

		$this->set('messageQueue', $this->MessageQueue->read(null, $this->params['named']['messageQueueId']));

		if ($this->params['named']['status'] == 'read') {
			$status = true;
		} else {
			$status = false;
		}
		
		$this->MessageQueue->saveField('read', $status);
		
		return true;
	}
}
?>