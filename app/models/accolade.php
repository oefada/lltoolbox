<?php
class Accolade extends AppModel {

	var $name = 'Accolade';
	var $useTable = 'accolade';
	var $primaryKey = 'accoladeId';

	var $belongsTo = array('AccoladeSource' => array('foreignKey' => 'accoladeSourceId'),
	                        'Client' => array('foreignKey' => 'clientId'));

	var $multisite = true;

	function getAccolades(){
		$q="SELECT * FROM accolade WHERE clientId is not null";
		return $this->query($q);
	}

	function insertIntoAccolade($row,$site,$tablename){

		$this->useDbConfig=$site;
		//$this->save($tablename,$row);
		$q="INSERT INTO accolade SET accoladeId=".$row['accoladeId'].", ";
		$q.="clientId=".$row['clientId'].", ";
		$q.="accoladeSourceId=".$row['accoladeSourceId'].", ";
		$q.="accoladeName='".mysql_real_escape_string($row['accoladeName'])."', ";
		$q.="description='".mysql_real_escape_string($row['description'])."', ";
		$q.="accoladeDate='".$row['accoladeDate']."', ";
		$q.="displayDate='".$row['displayDate']."', ";
		$q.="inactive='".$row['inactive']."'";
		echo $q."<br>";
		flush();
		$this->query($q);


	}

}
?>
