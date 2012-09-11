<?php

App::import("Vendor","NL",array('file' => "appshared".DS."legacy".DS."classes".DS."newsletter_manager.php"));


class UnsubscribeLogsController extends AppController 
{

	var $name = 'UnsubscribeLogs';
	var $helpers = array('Form', 'Javascript');
	var $uses = array('UserMailOptin','UnsubscribeLog', 'UndeliverableLog');

	var $components = array('RequestHandler');

	private $start_ut;
	private $end_ut;

	function beforeFilter(){

		parent::beforeFilter();
		$this->set('hideSidebar', true);

	}

	/**
	 * Count number of unsubs per mailingListId ie. per specific newsletter
	 * 
	 * @return arr
	 */
	public function mid(){

		$data = $this->UnsubscribeLog->find('all',
			array(
				'conditions'=>array(
					'unsubDate >='=>mktime(0,0,0,8,15,2012)
				),
				'fields' => array(
					'mailingListInstanceId',
					'COUNT(*) as num',
					'siteId'
				),
				'order' => "unsubDate DESC", 
				'group' => "mailingListInstanceId, siteId",
				'contain' => false 
			)
		); 

		$this->set("unsubMidArr",$data);

	}

	/**
	 * Process posted data for retrieval of records by date range and mailing list id
	 * 
	 * @return null
	 */
	function index(){

		// populate mailing list drop down selector
		$nlMgr=new NewsletterManager();
		$nlArr=$nlMgr->getNewsletterData();
		foreach($nlArr as $siteId=>$arr){
			foreach($arr as $nlId=>$row){
				$nlIdArr[$nlId]=$row['name'];
			}
		}
		$nlArr[0][0]['name']='Undeliverables';
		$nlArr[0][0]['contactId']='0';
		$this->set('nlIdArr',$nlIdArr);
		$this->set('nlDataArr',$nlArr);
/*
		$undelivCountArr=$this->UndeliverableLog->getUndelivCountByMonth($nlArr);
		$this->set("undelivCountArr", $undelivCountArr);

		$unsubCountArr=$this->UnsubscribeLog->getUnsubCountByMonth($nlArr);
		$this->set("unsubCountArr", $unsubCountArr);

		$unOptOutCountArr=$this->UnsubscribeLog->getUnOptOutCountByMonth($nlArr);
		$this->set("unOptOutCountArr", $unOptOutCountArr);

		$subCountArr=$this->UnsubscribeLog->getSubCountByMailingListId();
		$this->set("subCountArr", $subCountArr);

		$unsubUMOCountArr=$this->UserMailOptin->getUnsubCountByMonth($nlArr);
		$this->set("unsubUMOCountArr", $unsubUMOCountArr);
*/
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
			$this->set('startYear', $this->startYear);
			$this->set('startMonth', $this->startMonth);
			$this->set('startDay', $this->startDay);
			$this->set('endYear', $this->endYear);
			$this->set('endMonth',$this->endMonth);
			$this->set('endDay', $this->endDay);

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
		$this->startMonth=$this->data['start']['month'];
		$this->startDay=$this->data['start']['day'];
		$this->startYear=$this->data['start']['year'];
		$this->start_ut=mktime(0,0,0,$this->startMonth,$this->startDay,$this->startYear);

	}

	/**
	 * Set the end unixtime based on user posted data 
	 * 
	 * @return null
	 */
	private function setEndUt(){

		$this->endMonth=$this->data['end']['month'];
		$this->endDay=$this->data['end']['day'];
		$this->endYear=$this->data['end']['year'];
		$this->end_ut=mktime(0,0,0,$this->endMonth,$this->endDay,$this->endYear);

	}

	/**
	 * Export result set to csv file
	 * 
	 * @return download
	 */
	public function export(){

		// Stop Cake from displaying action's execution time 
		Configure::write('debug',0); 

		// sets $this->start_ut
		$this->setStartUt();
		// sets $this->end_ut
		$this->setEndUt();

		$data=$this->getUnsubData();

		// Define column headers for CSV file, in same array format as the data itself 
		$headers = array( 
			'UnsubscribeLog'=>array( 
				'email' => 'Email', 
				'mailingId' => $this->data['unsubscribe_logs']['mailingList'], 
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
