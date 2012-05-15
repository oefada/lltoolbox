<?php

App::import("Vendor","NL",array('file' => "appshared".DS."legacy".DS."classes".DS."newsletter_manager.php"));

class UnsubscribeLogsController extends AppController 
{

	var $name = 'UnsubscribeLogs';
	var $helpers = array('Form', 'Javascript');

	var $components = array('RequestHandler');

	/**
	 * Process posted data for retrieval of records by date range and mailing list id
	 * 
	 * @return null
	 */
	function index(){

		$this->set('startYear', date("Y"));
		$this->set('startMonth', date("n"));
		$this->set('startDay', date("j"));

		$this->set('endYear', date("Y"));
		$this->set('endMonth', date("n"));
		$this->set('endDay', date("j"));

		$nlMgr=new NewsletterManager();
		$this->set('nlIdArr',$nlMgr->getNewsletterIdArr());


	}

	/**
	 * Export result set to csv file
	 * 
	 * @return download
	 */
	public function export(){

		$start=mktime(0,0,0,$this->data['start']['month'],$this->data['start']['day'],$this->data['start']['year']);
		$end=mktime(0,0,0,$this->data['end']['month'],$this->data['end']['day'],$this->data['end']['year']);		

		// Stop Cake from displaying action's execution time 
		Configure::write('debug',0); 

		$nlMgr=new NewsletterManager();
		$nlArr=$nlMgr->getNewsletterIdArr();

		// Find fields needed without recursing through associated models 
		$data = $this->UnsubscribeLog->find('all',
			array(
				'conditions'=>array(
					'mailingId'=>$this->data['unsubscribe_logs']['mailingList'],
					'unsubDate >='=>$start,
					'unsubDate <='=>$end,
				),
				'fields' => array(
					'email',
					'mailingId',
					"from_unixtime(subDate, '%Y-%m-%d') as subDate","from_unixtime(unsubDate, '%Y-%m-%d') as unsubDate" 
				),
				'order' => "unsubDate DESC", 
				'contain' => false 
			)
		); 

		// Define column headers for CSV file, in same array format as the data itself 
		$headers = array( 
			'UnsubscribeLog'=>array( 
				'email' => 'Email', 
				'mailingId' => $nlArr[$this->data['unsubscribe_logs']['mailingList']], 
				'subDate' => 'Subscribed', 
				'unsubDate' => 'Unsubscribed' 
			) 
		); 

		// Add headers to start of data array 
		array_unshift($data,$headers); 
		// Make the data available to the view (and the resulting CSV file) 
		$this->set(compact('data')); 

	}

	/**************************
	// Cake's auto-built stuff 
	*/////////////////////////

	function listLog() {
		$this->UnsubscribeLog->recursive = 0;
		$this->set('unsubscribeLogs', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid UnsubscribeLog', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('unsubscribeLog', $this->UnsubscribeLog->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->UnsubscribeLog->create();
			if ($this->UnsubscribeLog->save($this->data)) {
				$this->Session->setFlash(__('The UnsubscribeLog has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The UnsubscribeLog could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid UnsubscribeLog', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->UnsubscribeLog->save($this->data)) {
				$this->Session->setFlash(__('The UnsubscribeLog has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The UnsubscribeLog could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->UnsubscribeLog->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for UnsubscribeLog', true));
			$this->redirect(array('action' => 'index'));
		}
		if ($this->UnsubscribeLog->del($id)) {
			$this->Session->setFlash(__('UnsubscribeLog deleted', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('The UnsubscribeLog could not be deleted. Please, try again.', true));
		$this->redirect(array('action' => 'index'));
	}

}
?>
