<?php
class UnsubscribeLog extends AppModel {

	var $name = 'UnsubscribeLog';
	var $useTable = 'unsubscribeLog';

	public function getUnsubCountByMonth($nlDataArr){

		$r=array();
		foreach($nlDataArr as $siteId=>$arr){
			foreach($arr as $nlId=>$row){
				$q="select siteId, mailingId, count(*) as num, from_unixtime(unsubDate, '%Y-%m') as unsubDate_ym ";
				$q.="FROM unsubscribeLog ";
				$q.="WHERE mailingId=? and siteId=? AND unsubDate>=".mktime(0,0,0,6,1,2012)." ";
				$q.="GROUP BY siteId, mailingId, unsubDate_ym ";
				$q.="ORDER BY siteId, mailingId, unsubDate_ym DESC";
				$tmp=$this->query($q,array($nlId, $siteId));
				if (count($tmp)>0){
					$r[]=$tmp;
				}
			}
		}
 
		return $r;

	}


	// Count the emails by month and newsletter that were optedout in Silverpop, but didn't exist in our db
	public function getUnOptOutCountByMonth($nlDataArr){

		$r=array();
		foreach($nlDataArr as $siteId=>$arr){
			foreach($arr as $nlId=>$row){
				$q="select siteId, newsletterId, count(*) as num, from_unixtime(dateUtYmd, '%Y-%m') as unsubDate_ym ";
				$q.="FROM unOptOutLog ";
				$q.="WHERE newsletterId=? AND siteId=? ";
				$q.="AND dateUtYmd>=".mktime(0,0,0,6,1,2012)." ";
				$q.="GROUP BY siteId, newsletterId, unsubDate_ym ";
				$q.="ORDER BY siteId, newsletterId, unsubDate_ym DESC";
				$tmp=$this->query($q,array($nlId, $siteId));
				if (count($tmp)>0){
					$r[]=$tmp;
				}
			}
		}
 
		return $r;

	}


	public function getSubCountByMailingListId(){

		$q="SELECT COUNT(*) as num, mailingListId FROM userMailOptin WHERE optin=1 GROUP BY mailingListId";
		return $this->query($q);

	}

	/**
	 * Update a batch of emails as unsub'd 
	 * 
	 * @param mixed $emailArr has the email as the key and unsub unixtime date as the value
	 *
	 * @return null
	 */
	public function insertIntoUnsubLog($emailArr, $siteId, $mailingId){

		foreach($emailArr as $unsubDate_ut=>$tmp){
			$offset=0;
			$length=300;
			while($arr=array_slice($tmp, $offset,$length,true)){
				$q="INSERT INTO unsubscribeLog (email, siteId, mailingId, unsubDate) ";
				$q.="VALUES ";
				foreach($arr as $email){
					$q.="('$email', $siteId, $mailingId, $unsubDate_ut), ";
				}
				$q=substr($q,0,-2)." ";
				$q.="ON DUPLICATE KEY UPDATE id=id";
				$this->query($q);
				$offset+=$length;
			}
		}

	}

}
