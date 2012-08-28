<?

class UndeliverableLog extends AppModel{

	var $name='UndeliverableLog';
	var $useTable='undeliverableLog';
	var $primaryKey='email';

	public function insertIntoUndelivLog($emailArr){

		foreach($emailArr as $unsubDate_ut=>$tmp){
			$offset=0;
			$length=300;
			while($arr=array_slice($tmp, $offset,$length,true)){
				$q="INSERT INTO undeliverableLog (email, dateUtYmd) ";
				$q.="VALUES ";
				foreach($arr as $email){
					$q.="('$email', $unsubDate_ut), ";
				}
				$q=substr($q,0,-2)." ";
				$q.="ON DUPLICATE KEY UPDATE dateUtYmd=$unsubDate_ut";
				$this->query($q);
				$offset+=$length;
			}
		}

	}


	public function getUndelivCountByMonth(){

		$q="select count(*) as num, dateUtYmd, ";
		$q.="FROM_UNIXTIME(dateUtYmd, '%Y-%m') as dateYm "; 
		$q.="FROM undeliverableLog ";
		$q.="WHERE dateUtYmd>=".mktime(0,0,0,6,1,2012)." ";
		$q.="GROUP BY dateYm ";
		$q.="ORDER BY dateUtYmd DESC";
		$r=$this->query($q);
		return $r;
	}

}
