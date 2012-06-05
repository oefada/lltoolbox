<?php

App::import("Vendor","NL",array('file' => "appshared".DS."legacy".DS."classes".DS."newsletter_manager.php"));

class UnsubscribeLogsController extends AppController 
{

	var $name = 'UnsubscribeLogs';
	var $helpers = array('Form', 'Javascript');

	var $components = array('RequestHandler');

	private $start_ut;
	private $end_ut;

	/**
	 * Process posted data for retrieval of records by date range and mailing list id
	 * 
	 * @return null
	 */
	function index(){

		// populate mailing list drop down selector
		$nlMgr=new NewsletterManager();
		$nlIdArr=$nlMgr->getNewsletterIdArr();
		$this->set('nlIdArr',$nlIdArr);

		if (!empty($this->data)){

			// this sets $this->start_ut
			$this->setStartUt();
			// sets $this->end_ut
			$this->setEndUt();

			// get the data inside the date range
			$data=$this->getUnsubData();

			// set the view data
			$this->set('output', $this->data['unsubscribe_logs']['Output']);
			$this->set('start_ut',$this->start_ut);
			$this->set('end_ut',$this->end_ut);
			$this->set('unsubLogs', $data);
			$this->set('nl',$nlIdArr[$this->data['unsubscribe_logs']['mailingList']]);
			$this->set('startYear', $startYear);
			$this->set('startMonth', $startMonth);
			$this->set('startDay', $startDay);
			$this->set('endYear', $endYear);
			$this->set('endMonth',$endMonth);
			$this->set('endDay', $endDay);

		}else{
			// default form criteria 
			$this->set('output', 'csv');
			$this->set('startYear', date("Y"));
			$this->set('startMonth', date("n"));
			$this->set('startDay', date("j"));
			$this->set('endYear', date("Y"));
			$this->set('endMonth', date("n"));
			$this->set('endDay', date("j"));
		}

	}


	/**
	 * Set the start unixtime based on user posted data 
	 * 
	 * @return null
	 */
	private function setStartUt(){

		// repopulate the form with criteria user selected
		$startMonth=$this->data['start']['month'];
		$startDay=$this->data['start']['day'];
		$startYear=$this->data['start']['year'];
		$this->start_ut=mktime(0,0,0,$startMonth,$startDay,$startYear);

	}

	/**
	 * Set the end unixtime based on user posted data 
	 * 
	 * @return null
	 */
	private function setEndUt(){

		$endMonth=$this->data['end']['month'];
		$endDay=$this->data['end']['day'];
		$endYear=$this->data['end']['year'];
		$this->end_ut=mktime(0,0,0,$endMonth,$endDay,$endYear);

	}

	/**
	 * Export result set to csv file
	 * 
	 * @return download
	 */
	public function export(){

		// Stop Cake from displaying action's execution time 
		Configure::write('debug',0); 

		// for mailing list drop down selector
		$nlMgr=new NewsletterManager();
		$nlArr=$nlMgr->getNewsletterIdArr();

		// sets $this->start_ut
		$this->setStartUt();
		// sets $this->end_ut
		$this->setEndUt();

		$data=$this->getUnsubData();

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
		// Make the data available to the view (and the resultant CSV file) 
		$this->set(compact('data')); 

	}

	private function getUnsubData(){


		// Find fields needed without recursing through associated models 
		// Note about the date range:
		// the end date is hour zero of the date, but there are 24 hours occuring after that that are still  
		// the end date, so add 86400 seconds to it: end_ut+86400. 
		// eg. 2012-01-01 00:00:00 + 24hours
		$data = $this->UnsubscribeLog->find('all',
			array(
				'conditions'=>array(
					'mailingId'=>$this->data['unsubscribe_logs']['mailingList'],
					'unsubDate >='=>$this->start_ut,
					'unsubDate <='=>$this->end_ut+86400,
				),
				'fields' => array(
					'email',
					'mailingId',
					"from_unixtime(subDate, '%Y-%m-%d') as subDateYmd",
					"from_unixtime(unsubDate, '%Y-%m-%d') as unsubDateYmd" 
				),
				'order' => "unsubDate DESC", 
				'contain' => false 
			)
		); 

		return $data;

	}

}
