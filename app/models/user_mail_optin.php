<?php
class UserMailOptin extends AppModel {

	var $name = 'UserMailOptin';
	var $useTable = 'userMailOptin';
	var $primaryKey = 'userMailOptinId';
	
	var $belongsTo = array('User' => array('foreignKey' => 'userId'));

	public function getUnsubCountByMonth($nlDataArr, $limit='2012-05-01 00:00:00'){

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

}
?>
