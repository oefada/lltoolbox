<?php

App::import("Vendor","NL",array('file' => "appshared".DS."legacy".DS."classes".DS."newsletter_manager.php"));

class SyncEmailsController extends AppController 
{

	var $name = 'SyncEmails';
	var $helpers = array('Form', 'Javascript');
	var $uses = array('UserMailOptin','UnsubscribeLog', 'UndeliverableLog');
	var $components = array('RequestHandler');

	public function index(){

		$selectedNlId=0;
		$selectedSiteId=0;
		if (isset($this->data['sync_emails']['mailingList'])){
			$tmp=explode("-",$this->data['sync_emails']['mailingList']);
			$selectedNlId=$tmp[1];
		}

		// for mailing list drop down selector
		$nlMgr=new NewsletterManager();
		$nlArr=$nlMgr->getNewsletterData();
		foreach($nlArr as $siteId=>$arr){
			// Skip vcom for now
			if ($siteId==3){
				//continue;
			}
			foreach($arr as $nlId=>$row){
				//
				// There are some LL newsletters associated with FG, however, the db only sees them as LL
				if ($siteId==2 && isset($nlArr[1][$nlId])){
					continue;
				}

				if ($selectedNlId==$nlId && $selectedSiteId==false){
					$selectedSiteId=$siteId;
				}
				$nlIdArr[$siteId][$siteId.'-'.$nlId]=$row['name'];
			}
		}

		// Set a '0' newsletterId for undeliverables
		array_unshift($nlIdArr,'Select');
		$this->set('nlIdArr',$nlIdArr);

		// POSTED DATA
		if (!empty($this->data)){

			if ($this->data['sync_emails']['csv']['tmp_name']==''){
				if ($this->data['sync_emails']['csv']['error']==1){
					$msg="File too large. Break it up and re-upload parts of it.";
				}else{
					$msg="You didn't choose a file.";
				}
				$this->Session->setFlash(__($msg, true), 'default', array(), 'success');
				$this->redirect("/sync_emails/index");
			}

			$origFilename=$this->data['sync_emails']['csv']['name'];
			$file=$this->data['sync_emails']['csv']['tmp_name'];
			$str=file_get_contents($file);
			preg_match_all('~"([^@\n"]+@[^"]+)","([^"]+)"~is',$str,$arr);
			if (!isset($arr[0]) || count($arr[0])==0){
				//007enzo1@gmail.com,11/24/2011 9:14
				preg_match_all('~([^,@\n]+@[^,\n]+),([^\n]+)~is',$str,$arr);
			}
			if (!isset($arr[0]) || count($arr[0])==0){
				$this->Session->setFlash(__("Unable to parse file. Be sure it is of type csv.", true), 'default', array(), 'success');
				$this->redirect("/sync_emails/index");
			}

			$emailArr=array();
			foreach($arr[1] as $key=>$email){
				$email=mysql_real_escape_string(trim(strtolower($email)));

				// group emails by optout year-month
				$date=$arr[2][$key];
				$year_month=date("Y-m",strtotime($date));
				$date_ut=strtotime($year_month);
				$emailArr[$date_ut][]=$email;

				$undelivEmailArr[$date_ut][]=$email;
			}

			// make sure email exists in userMailOptin before adding to unsubscribeLog
			$emailArr=$this->UserMailOptin->unsetNonexistantEmails($emailArr,$selectedNlId, $selectedSiteId);

			// Undeliverables are not specific to a newsletter
			if ($this->data['sync_emails']['id']=='undeliverables'){
				$this->UserMailOptin->setOptin($emailArr);
				$this->UndeliverableLog->insertIntoUndelivLog($undelivEmailArr, 0, 0 );
			}else{
				$this->UserMailOptin->setOptin($emailArr,0,$selectedNlId);
				$this->UnsubscribeLog->insertIntoUnsubLog($emailArr, $selectedSiteId,$selectedNlId);
			}
			$this->set("msg","Uploaded file: $origFilename siteId:$selectedSiteId nlId: $selectedNlId");
			if ($_SERVER['ENV']!='development'){
				$this->Session->setFlash(__("Updated!", true), 'default', array(), 'success');
				$this->redirect("/sync_emails/index");
			}

		}

	}

}
