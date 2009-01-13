<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html>
<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
</head>

<style type="text/css">
body {
	margin: 20px;
}
body, td, input, select, span, textarea {
	font-family: Verdana, Arial, Geneva, Helvetica, sans-serif;
	font-size: 11px;
	color: #444444;
	line-height: 15px;
}
a {
	color: #336699;
}
a:hover {
	color: #990000;
}
th {
	text-align: left;
	font-size: 11px;
	background: #EEEEEE;
	border-bottom: 1px solid silver;
}
td, th {
	padding-right: 10px;
}
.small td {
	font-size: 10px;
	color: #666666;
}
</style>

</head>
<body>

<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('default');

ini_set('error_reporting', 2039);

function str_month_to_number($month) {
 	$months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
 	for($i=0,$j=1; $i<count($months); $i++,$j++) {
		if($months[$i] == $month) {
			return($j);
		}
	}
 }
function sql_date_to_timestamp($dateString) {
	debug($dateString);
	$dateString = str_replace(array("  ",","),array(" ",""),$dateString);
	$dateArray = split(" ", $dateString);
	$dateArray[0] = str_month_to_number($dateArray[0]);
	$datetime = "$dateArray[2]-$dateArray[0]-$dateArray[1]";
	debug($dateString);
	return strtotime($datetime);
}

$clientName = $_POST['clientName'];
$clientId = $_POST['clientId'];

?>
<a href="ns_main?pg=ns_listing">Mailing Calendar</a> | <a href="ns_client_schedule">Client Mailing Schedule</a>
<br/><br/><br/>
<form method="post">
	<b>CLIENT NAME:</b>
	<input name="clientName" value="<?=$clientName?>"/><br/><br/>
	<b>CLIENT ID:</b>
	<input name="clientId" value="<?=$clientId?>"/><br/><br/>
	<input type="submit"/>
</form>
<br/><br/>
<?
if($clientName || $clientId) {

$condition = "c.name LIKE '%$clientName%'";
if (!$clientName)
	$condition = "l.clientId = $clientId";

$result = mysql_query("SELECT l.loaId, l.startDate, l.endDate, l.numEmailInclusions, c.clientId, c.name as clientName FROM loa l LEFT JOIN client c ON c.clientId = l.clientId WHERE $condition ORDER BY l.clientId");
while($row = mysql_fetch_array($result)) {
	$LOA[$row['loaId']] = $row;
	$current_clientId = $row['clientId'];
	$startTimeStamp = strtotime($row['startDate']);
	$endTimeStamp = strtotime($row['endDate']);
	$result2 = mysql_query("
		SELECT p.clientId, mailing_timestamp, msched.mailing_schedule_id, mailing_segment_type_description, ms.mailing_segment_type_id
		FROM mailing_segment ms INNER JOIN mailing_schedule msched ON ms.mailing_schedule_id = msched.mailing_schedule_id
		INNER JOIN mailing_segment_product msp ON ms.mailing_segment_id = msp.mailing_segment_id
		INNER JOIN client p ON p.clientId = msp.clientId
		INNER JOIN mailing_segment_type mst ON mst.mailing_segment_type_id = ms.mailing_segment_type_id
		WHERE mailing_timestamp BETWEEN $startTimeStamp AND $endTimeStamp
		AND clientId = $current_clientId
	");	
	if (is_resource($result2)) {
	while($row2 = mysql_fetch_array($result2)) {
		$LOA[$row['loaId']]['mailing'][] = $row2;
	}
	}
}

if($LOA) {
	?><table border="0" cellpadding="4" cellspacing="0"><tr><th>Client ID</th><th>Client Name</td><th>LOA ID</th><th>Mail Shots</th><th>LOA Start Date</th><th>LOA End Date</th><th>Total Mailed</th></tr><?
	foreach ($LOA as $key => $loa) {
		if(!empty($loa['mailing'])) {
			$mailing = $loa['mailing'][0];
			?><tr><td><?=$loa['clientId']?></td><td><?=$loa['clientName']?></td><td><?=$loa['loaId']?></td><td><?=$loa['numEmailInclusions']?></td><td><?=$loa['startDate']?></td><td><?=$loa['endDate']?></td><td><span style='color:red; font-weight:bold;'><?=count($loa['mailing'])?></span></td></tr><?
			foreach ($loa['mailing'] as $mailing) {
				?><tr class="small"><td></td><td><b>PID:</b> <?=$mailing['clientId']?></td><td colspan="2"></td><td><b>Mailing Type:</b> <?=$mailing['mailing_segment_type_description']?></td><td colspan="2"><b>Date Mailed:</b> <a href="ns_view_html?mailing_schedule_id=<?=$mailing['mailing_schedule_id']?>&preview_html=1" target="_blank"><?=date("r",$mailing['mailing_timestamp'])?></a></td></tr><?
			}
		} else {
			?><tr><td><?=$loa['clientId']?></td><td><?=$loa['clientName']?></td><td><?=$loa['loaId']?></td><td><?=$loa['numEmailInclusions']?></td><td><?=$loa['startDate']?></td><td><?=$loa['endDate']?></td><td><span style='color:red; font-weight:bold;'>0</span></td></tr><?
		}
		?><tr><td colspan="7" style="padding:0px; height:1px; line-height:1px; background:#E0E0E0;">&nbsp;</td></tr><?
	}
} else {
	echo "<br/><b>No Data Returned.</b>";
}
?>
</table>
<?
}
?>

</body>
</html>