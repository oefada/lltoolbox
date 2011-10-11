<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('default');

if ($_REQUEST['GO'] != '') {
	
	$limit = intval($_REQUEST['nbr']);
	
	$sql = "SELECT * FROM ppvNotice WHERE emailBody IS NULL AND bodyImported IS NULL AND emailBodyFileName IS NOT NULL LIMIT " . $limit;
	// $sql = "SELECT * FROM ppvNotice WHERE ppvNoticeId = 387060";

	$results = $connected->query($sql);
		
	$updateCount = 0;
	$noFileCount = 0;
	foreach ($results as $r) {
	    $f = "../vendors/email_msgs/toolbox_sent_messages/" . $r['ppvNotice']['emailBodyFileName'];
	    if (file_exists($f)) {
		    $body = file_get_contents($f);
		    $sql = "UPDATE ppvNotice SET bodyImported = NOW(), emailBody = '" . str_replace("'", "''", $body) . "' WHERE ppvNoticeId = " . $r['ppvNotice']['ppvNoticeId'];
		    $q = $connected->query($sql);
		    // echo $sql;
		    $updateCount++;
	    } else {
		    $noFileCount++;
	    }
	}
}

$sql = "SELECT COUNT(*) AS nbr FROM ppvNotice WHERE emailBody IS NULL AND bodyImported IS NULL AND emailBodyFileName IS NOT NULL";
$count = $connected->query($sql);



if ($_REQUEST['GO'] != '') {
	echo date('m/d/Y g:i:s') . '<br><br>';
	echo $updateCount . ' records imported<br><br>';
	echo $noFileCount . ' records with missing files<br><br>';
}
echo $count[0][0]['nbr'] . ' records left to import<br><br>';

