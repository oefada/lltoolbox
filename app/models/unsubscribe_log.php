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
				$q.="WHERE mailingId=? and siteId=? ";
				$q.="GROUP BY siteId, mailingId, unsubDate_ym ";
				$q.="ORDER BY siteId, mailingId, unsubDate_ym DESC";
				$r[]=$this->query($q,array($nlId, $siteId));
			}
		}
 
		return $r;

	}

	/**
	 * TODO: short description.
	 * 
	 * @return TODO
	 */
	public function getSubCountByMailingListId(){

		$q="SELECT COUNT(*) as num, mailingListId FROM userMailOptin WHERE optin=1 GROUP BY mailingListId";
		return $this->query($q);

	}
}

?>
