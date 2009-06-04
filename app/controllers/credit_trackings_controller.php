<?php
class CreditTrackingsController extends AppController {

	var $name = 'CreditTrackings';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->CreditTracking->recursive = 0;
		$this->paginate['order'] = array('creditTrackingId' => 'desc');

		$results = $this->CreditTracking->query("
			SELECT creditTracking.creditTrackingId, userId, email, username, balance, datetime
			FROM (SELECT max(creditTrackingId) AS creditTrackingId FROM creditTracking GROUP BY userId) ct
				INNER JOIN creditTracking USING(creditTrackingId)
				INNER JOIN user USING(userId)
				LEFT JOIN userSiteExtended USING(userId)
		");
		
		$this->set('creditTrackings', $results);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid CreditTracking.', true));
			$this->redirect(array('action'=>'index'));
		}
		// $this->CreditTracking->primaryKey = 'userId';		
		$trackings = $this->CreditTracking->find('all', array('conditions' => array('CreditTracking.userId' => $id), 'order' => array('CreditTracking.creditTrackingId')));
		$this->set('creditTrackings', $trackings);
	}

	function add() {
		if (!empty($this->data)) {
			$this->CreditTracking->create();
			if ($this->CreditTracking->save($this->data)) {
				$this->Session->setFlash(__('The CreditTracking has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The CreditTracking could not be saved. Please, try again.', true));
			}
		}
		$creditTrackingTypes = $this->CreditTracking->CreditTrackingType->find('list');
		$this->set('creditTrackingTypeIds', $creditTrackingTypes);
		$this->set(compact('creditTrackingTypes'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid CreditTracking', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->CreditTracking->save($this->data)) {
				$this->Session->setFlash(__('The CreditTracking has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The CreditTracking could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->CreditTracking->read(null, $id);
		}
		$creditTrackingTypes = $this->CreditTracking->CreditTrackingType->find('list');
		$this->set(compact('creditTrackingTypes'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for CreditTracking', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->CreditTracking->del($id)) {
			$this->Session->setFlash(__('CreditTracking deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>