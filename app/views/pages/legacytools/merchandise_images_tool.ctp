<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('default_mysql');

$page = 'merchandise_images_tool';
$page_name = 'Merchandise Images Tool';

$year = isset($_REQUEST['year']) ? $_REQUEST['year'] : date('Y');
$month = isset($_REQUEST['month']) ? $_REQUEST['month'] : date('m');
$day = isset($_REQUEST['day']) ? $_REQUEST['day'] : date('d');

$days_in_month = $ndays = date("t", mktime(0, 0, 0, $month, 1, $year));

if ($day > $days_in_month) {
	$day = $days_in_month;
}

$month = str_pad($month, 2,'0',STR_PAD_LEFT);
$day = str_pad($day, 2,'0',STR_PAD_LEFT);

$ts_now = strtotime("$month/$day/$year");
$ts_next_day = $ts_now + (24 * 60 * 60);
$ts_prev_day = $ts_now - (24 * 60 * 60);

$date_string = "$year-$month-$day";

$data = array();

$image_url = array();
$product_id = array();
$link_url = array();

if ($_POST['submit_mei']) {
	
	$z=1;
	for ($i=1; $i<10; $i++) {
		if (!empty($_POST["mei_image_url_$i"])) {
			$image_url[$z] = $_POST["mei_image_url_$i"];
			$product_id[$z] = $_POST["mei_pid_$i"];
			$offer_id[$z] = $_POST["mei_oid_$i"];
			$link_url[$z] = $_POST["mei_link_url_$i"];
			$z++;
		}
	}

	$result = mysql_query( "select * from merchandiseImage where dateLive = '$date_string'");

	if (!$result) {
		die('DATABASE PROBLEM');
	}

	if (mysql_num_rows($result)) {
		$delete_result = mysql_query("delete from merchandiseImage where dateLive = '$date_string'");	
	} 

	foreach ($image_url as $k=>$v) {
		$insert = mysql_query("insert into merchandiseImage (dateLive,slotId,clientId,imageUrl,linkUrl,offerId) VALUES ('$date_string','$k','$product_id[$k]','$v', '$link_url[$k]','$offer_id[$k]')");
	}

}

$select = mysql_query("select * from merchandiseImage where dateLive = '$date_string' order by slotId");
while($row = mysql_fetch_array($select)) {
	$data[$row['slotId']] = $row;
}
?>
<script language="javascript" type="text/javascript">

function daysInMonth(iMonth, iYear)
{
	return 32 - new Date(iYear, iMonth, 32).getDate();
}

function changeDateRefresh() 
{
    var year = document.getElementById('s_year').value;
    var month = document.getElementById('s_month').value;
    var day = document.getElementById('s_day').value;

 	if(year && month && day) {
		window.location.replace('<?=$page;?>?year=' + year + '&month=' + month + '&day=' + day);
	}
}

</script>

</head>
<body>
<div id="container">
<h3 class="hdr"><?=$page_name;?></h3>
<div>
Use this tool to setup the merchandising images on the new homepage.  Double click on the slot id to delete a row.
<br />
</div>

<div class="searchBox">
Select Date: <br /><br />

<select id="s_year" name="year" onchange="changeDateRefresh();">
<?php
for($i=2008; $i<2020; $i++) {
	$selected = ($i==$year) ? 'selected="selected"' : '';
	echo "<option value='$i' $selected>$i</option>\n";
}
?>
</select>

<select id="s_month" name="month" onchange="changeDateRefresh();">
<?php
for($i=1; $i<13; $i++) {
	$selected = ($i==$month) ? 'selected="selected"' : '';
	echo "<option value='$i' $selected>$i</option>\n";
}
?>
</select>

<select id="s_day" name="day" onchange="changeDateRefresh();">
<?php

$num_greg_days = $ndays = date("t", mktime(0, 0, 0, $month, 1, $year));

for($i=1; $i<=$num_greg_days; $i++) {
	$selected = ($i==$day) ? 'selected="selected"' : '';
	echo "<option value='$i' $selected>$i</option>\n";
}
?>
</select>
<br /><br />
<a href="<?=$page;?>?year=<?=date('Y', $ts_prev_day);?>&month=<?=date('m', $ts_prev_day);?>&day=<?=date('d', $ts_prev_day);?>">Prev Day</a>&nbsp;&nbsp;&nbsp;
<a href="<?=$page;?>?year=<?=date('Y', $ts_next_day);?>&month=<?=date('m', $ts_next_day);?>&day=<?=date('d', $ts_next_day);?>">Next Day</a>
</div>

You are editing for: <strong><? echo date('F d, Y (l)', strtotime("$month/$day/$year"));?></strong><br /><br />

<div style="text-align:right;margin-top:5px;margin-bottom:5px;">
	<a href="http://preview.luxurylink.com/?test10=c&date=<?=$date_string;?>" target="_BLANK">Preview (Click Save First)</a>
</div>

<form method="post" action="<?=$page;?>">
<input type="hidden" name="year" value="<?=$year;?>" />
<input type="hidden" name="month" value="<?=$month;?>" />
<input type="hidden" name="day" value="<?=$day;?>" />

<table width="800" id="dest_table" class="werd" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<th width="30">Slot</th>
		<th width="50">Product Id</th>
		<th width="50">Offer Id</th>
		<th>Image Url</th>
		<th>Link Url</th>
	</tr>
	<?php
	for ($i=1; $i<10; $i++) {
		?>
	<tr>
		<td><?=$i;?></td>
		<td><input type="text" style="width:100%;" name="mei_pid_<?=$i;?>" value="<?=@$data[$i]['clientId'];?>" /></td>
		<td><input type="text" style="width:100%;" name="mei_oid_<?=$i;?>" value="<?=@$data[$i]['offerId'];?>" /></td>
		<td><input type="text" style="width:100%;" name="mei_image_url_<?=$i;?>" value="<?=@$data[$i]['imageUrl'];?>" /></td>
		<td><input type="text" style="width:100%;" name="mei_link_url_<?=$i;?>" value="<?=@$data[$i]['linkUrl'];?>" /></td>
	</tr>
		<?php
	}
	?>
</table>

<div id="submit_but" style="display: ''; text-align:right; margin-top:10px;">
	<input type="submit" name="submit_mei" value="Save Changes" />
</div>

</form>

<div id="ajaxLoader" style="display: none; margin: 0px; padding: 0px; width: 126px; height: 22px;"></div>
<div id="updateMsg"></div>
</div>