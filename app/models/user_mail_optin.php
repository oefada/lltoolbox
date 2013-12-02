<?php
class UserMailOptin extends AppModel {

	var $name = 'UserMailOptin';
	var $useTable = 'userMailOptin';
	var $primaryKey = 'userMailOptinId';
	
	var $belongsTo = array('User' => array('foreignKey' => 'userId'));

	public function getUnsubCountByMonth($nlDataArr, $limit='2012-06-01 00:00:00'){

		foreach($nlDataArr as $siteId=>$arr){
			foreach($arr as $nlId=>$row){
				$nlIdArr[$nlId]=$nlId;
			}
		}

		$r=array();
			foreach($nlIdArr as $nlId){
			$q="select mailingListId, count(*) as num, ";
			//$q.="optinDatetime, optoutDatetime, ";
			//$q.="unix_timestamp(optinDatetime) as optinDatetime_ut, ";
			$q.="unix_timestamp(optoutDatetime) as optoutDatetime_ut, ";
			//$q.="substring(optinDatetime,1,7) as optinDatetime_ym, ";
			$q.="substring(optoutDatetime,1,7) as optoutDatetime_ym ";
			$q.="FROM userMailOptin ";
			$q.="WHERE mailingListId=? ";
			if ($limit){
				$q.="AND optoutDatetime>=? ";
			}
			$q.="GROUP BY mailingListId, optoutDatetime_ym ";
			$q.="ORDER BY mailingListId, optoutDatetime_ym DESC";
			$tmp=$this->query($q,array($nlId, $limit));
			if (count($tmp)>0)$r[]=$tmp;
		}
 
		return $r;

	}

	/**
	 * Given an array of undeliverable emails, set all optin values to 0 for each email.
	 * We don't care about the specific date of optopt, only the month year. Makes update query better.
	 * 
	 * @param mixed $emailArr 
	 * 
	 * @return null
	 */
	public function setOptin($emailArr, $val=0, $newsletterId=false){

		foreach($emailArr as $unsubDate_ut=>$tmp){
			$offset=0;
			$length=300;
			while($arr=array_slice($tmp, $offset,$length,true)){
				$arr=array_map('mysql_real_escape_string',$arr);
				$q="UPDATE userMailOptin umo INNER JOIN user u ON (umo.userId=u.userId) ";
				$q.="SET optin=$val, optoutDatetime='".date("Y-m-d H:i:s",$unsubDate_ut)."' ";
				$q.="WHERE email IN ('".implode("', '", $arr)."') ";
				if ($newsletterId){
					$q.="AND mailingListId=$newsletterId";
				}
				$this->query($q);
				$offset+=$length;
			}
		}

	}

	/**
	 * After uploading an optout list from silverpop, remove emails on the list that are not in our db.
	 * The code operates on batches for optimization and ease of use
	 * 
	 * @param mixed $emailArr 
	 * 
	 * @return arr
	 */
	public function unsetNonexistantEmails($emailArr, $newsletterId=0, $siteId=0){

		$newEmailArr=array();
		foreach($emailArr as $key=>$arr){
			$offset=0;
			$length=300;
			while($tmp=array_slice($arr, $offset,$length,true)){
				$emailInStr="('".implode("', '",($tmp))."')";
				$conditions=array("email IN $emailInStr");
				$joins=array();
				if ($newsletterId){
					$conditions[]="mailingListId=$newsletterId";
					$joins=array(
						array(
							'table'=>'userMailOptin',
							'alias'=>'UserMailOptin',
							'type'=>'INNER',
							'conditions'=>'User.userId=UserMailOptin.userId'
						)	
					);
					
				}
				//$this->User->unBindModel(array('belongsTo'=>array('Salutation')));
				//$this->User->unBindModel(array('hasOne'=>array('UserSiteExtended')));
				$r=$this->User->find('list', array(
					'fields'=>array('User.email'),
					'conditions'=>$conditions,
					'group'=>'email',
					'joins'=>$joins
				));
				$offset+=$length;
				//AppModel::printR($r);exit;
				if (count($r)==0 || !is_array($r)){
					continue;
				}
				// For some reason, cake doesn't return the email address when using LOWER
				//'fields'=>array('LOWER(User.email) as email'),
				// So having php do it
				$r=array_map('strtolower',$r);

				// If the db result has a different count than email array used to query against it, that means
				// it didn't find a row in the db for that email. Build new array without the email as it 
				// shouldn't be in the unsubscribeLog
				if (isset($newEmailArr[$key])){
					$newEmailArr[$key]+=$r;
				}else{
					$newEmailArr[$key]=$r;
				}

			}

		}

		// get emails that are not in our db, but were opted out in silverpop
		$wtfArr=array();
		foreach($emailArr as $key=>$arr){
			if (count($arr)==0){
				continue;
			}
			if (!isset($newEmailArr[$key])){
				$wtfArr[$key]=$arr;
			}else{
				$tmp=array_diff($arr,$newEmailArr[$key]);
				if (count($tmp)>0){
					$wtfArr[$key]=$tmp;
				}
			}
		}

		if (count($wtfArr)>0){
			foreach($wtfArr as $optoutDate=>$emailArr){
				$offset=0;
				$length=300;
				while($tmp=array_slice($emailArr, $offset,$length,true)){
					$q="INSERT INTO unOptOutLog (email, dateUtYmd, newsletterId, siteId) VALUES ";
					foreach($tmp as $email){
						$q.="('".mysql_real_escape_string($email)."', $optoutDate, $newsletterId, $siteId), ";
					}
					$q=substr($q,0,-2)." ";
					$q.="ON DUPLICATE KEY UPDATE dateUtYmd=$optoutDate";
					//echo "'".implode("', '", $tmpArr)."'";
					$this->query($q);
					$offset+=$length;
				}
			}
		}

		return ($newEmailArr);

	}

}
