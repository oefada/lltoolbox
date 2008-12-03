<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('live');

$page = "featured_escapes_tool";

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

if(isset($_GET['do_ajax'])) {
	$product_str = rtrim($_GET['product_str'],'-');	
	$tmp_pid = explode('-',$product_str);
	
	$style_dest = isset($_GET['style_d']) && is_numeric($_GET['style_d']) ? $_GET['style_d'] : '0';		
	$style_life = isset($_GET['style_l']) && is_numeric($_GET['style_l']) ? $_GET['style_l'] : '0';		
	
	$styles = "$style_dest-$style_life";

	$title = isset($_GET['title']) ? trim($_GET['title']) : false;
	$hdr_img = isset($_GET['hdr_img']) ? trim($_GET['hdr_img']) : false;
	$offerTitle = isset($_GET['offerTitle']) ? trim($_GET['offerTitle']) : false;
	$offerId = isset($_GET['offerId']) ? trim($_GET['offerId']) : false;

	$result = mysql_query("SELECT * FROM featuredEscape WHERE dateLive = '$date_string'");
	if (mysql_num_rows($result)) {
		$sql = "UPDATE featuredEscape SET title = '$title', styles = '$styles', headerImgSrc = '$hdr_img', offerTitle = '$offerTitle', offerId = '$offerId' WHERE dateLive = '$date_string'";
	} else {
		$sql = "INSERT INTO featuredEscape (dateLive, title, styles, headerImgSrc, offerTitle, offerId) VALUES ('$date_string','$title','$styles','$hdr_img','$offerTitle','$offerId')";
	}

	$result = mysql_query($sql);
	if (!$result) {
		echo "<div class='redBox'>DATABASE QUERY FAILED -- Please contact your local friendly developer.</div>";
		die();
	} else {
		$delete_rows = mysql_query("DELETE FROM featuredEscapeClientOffer WHERE dateLive = '$date_string'");
		$errors = 0;
		$slot = 1;
		foreach ($tmp_pid as $k=>$v) {
			$tmp = explode(':', $v);

			$pid = $tmp[0];
			$oid = $tmp[1];
			
			if(!is_numeric($oid) || $oid <= 0) {
				$oid = '';
			}
			$insert_rows = mysql_query("INSERT INTO featuredEscapeClientOffer (dateLive,clientId,offerId,slotId) VALUES ('$date_string','$pid','$oid','$slot')");
			if ($insert_rows) {
				$slot++;
			} else {
				echo mysql_error();
				$errors++;
			}
		}
		if ($errors) {
			echo "<div class='icon-red'>There has been a problem with the database.  Please double check your previous operation.</div>";
			die();
		}
	}
	
	echo "<div class='greenBox'>SUCCESSFULLY UPDATED!</div>";
	
	die();
}

$data = array();
$data_products = array();
$style_dest = array();
$style_life = array();

$result = mysql_query("SELECT * from featuredEscape WHERE dateLive = '$date_string'");
$data = mysql_fetch_array($result);

if (mysql_num_rows($result)) {
	$result = mysql_query("SELECT fep.*, p.name FROM featuredEscapeClientOffer fep 
							INNER JOIN client p ON fep.clientId = p.clientId 
							WHERE fep.dateLive = '$date_string' ORDER BY fep.slotId");
	while ($row = mysql_fetch_array($result)) {
		$data_products[] = $row;
	}
}

$result = mysql_query("SELECT styleId, styleTypeId, styleName FROM style_mstr ORDER BY styleName");
while($row = mysql_fetch_array($result)) {
	if ($row['styleTypeId'] == 1) {
		$style_dest[$row['styleId']] = $row['styleName'];
	} else {
		$style_life[$row['styleId']] = $row['styleName'];
	}
}

$tmp = explode('-', $data['styles']);
$style_dest_selected = $tmp[0];
$style_life_selected = $tmp[1];

?>
<script language="JavaScript" src="/js/legacy/admin_js.js"></script>
<script language="JavaScript" src="/js/legacy/admin_ajax.js"></script>
<script language="JavaScript" src="/js/legacy/tablednd.js"></script>
<script language="javascript" type="text/javascript">

function daysInMonth(iMonth, iYear)
{
	return 32 - new Date(iYear, iMonth, 32).getDate();
}

function changeDateRefresh() {
    var year = document.getElementById('s_year').value;
    var month = document.getElementById('s_month').value;
    var day = document.getElementById('s_day').value;

 	if(year && month && day) {
		window.location.replace('<?=$page;?>?year=' + year + '&month=' + month + '&day=' + day);
	}
}

function scheduleFeaturedEscapesAuto() {
	dest_id = document.getElementById('fe_style_dest').value;
	styleId = document.getElementById('fe_style_life').value;
	if (!styleId && !dest_id) {
		alert('Please choose a style or destination.');
		return false;
	}
	
	var rows = document.getElementById('dest_table').tBodies[0].rows;

	var styleIds = dest_id + '-' + styleId;
	var link = "featured_escapes_popup?do_ajax=1&auto_fill=1&current_slot=" + (rows.length -1) + "&styleIds=" + styleIds;

	executeAjax(link, updateFeaturedEscapesAuto);				
}

function addManualProduct() {
	var pid = document.getElementById('addManualPID').value;
	var oid = document.getElementById('addManualOID').value;
	if (!pid) {
		return false;
	}
	if(!oid) {
		oid = '';
	}
	var link = "featured_escapes_popup?do_ajax2=1&pid=" + pid +"&oid=" + oid;
	executeAjax(link, updateFeaturedEscapesAuto);
}

function updateFeaturedEscapesAuto() {
	if (xmlHttp.readyState==4){
		var tmp_result = xmlHttp.responseText;
		if (tmp_result.indexOf('redBox') != -1) {
			updateMsg();
			document.getElementById('updateMsg').style.display = '';
			return false;
		}
		var tmp_first = tmp_result.split('%%');
		var tmp_second = '';
		for (i=0; i < tmp_first.length; i++) {
			if (tmp_first[i]) {
				tmp_second = tmp_first[i].split('@@');
				append_row_damnit('dest_table',tmp_second[0], tmp_second[1], tmp_second[2]);
			}
		}
	}
}

function append_row_damnit(tblId, offerId, oid, auctionName, d_open, d_close)
{
	var tbl = document.getElementById(tblId);
	var newRow = tbl.insertRow(tbl.rows.length);
	var newCell = newRow.insertCell(0);

	newCell.innerHTML = tbl.rows.length - 1;
	newCell.setAttribute("onDblClick","deleteRow('" + tblId + "',parseInt(this.innerHTML))");

	var newCell = newRow.insertCell(1);
	newCell.innerHTML =  offerId ;

	var newCell = newRow.insertCell(2);
	newCell.innerHTML = oid;

	var newCell = newRow.insertCell(3);
	newCell.innerHTML = auctionName;
	
	if(tblId == 'dest_table') {
		initDrag(tblId);
		return false;
	}

	var newCell = newRow.insertCell(3);
	newCell.innerHTML = d_open;
	
	var newCell = newRow.insertCell(4);
	newCell.innerHTML = d_close;

	initDrag();
}

function scheduleFeaturedEscapes() {
	dest_id = document.getElementById('fe_style_dest').value;
	styleId = document.getElementById('fe_style_life').value;
	
	if (!styleId && !dest_id) {
		alert('Please choose a style or destination.');
		return false;
	}

	var pop_link = "featured_escapes_popup?";
	pop_link += "style_dest=" + dest_id;
	pop_link += "&style_life=" + styleId;

	var win = window.open(pop_link,"","statusbar=no,scrollbars=yes,resizable=yes,width=650,height=700");
	return true;
}

function processUpdateFeaturedEscapes(year, month, day, tbl_id) {

	var title = document.getElementById('fe_title').value;
	var style_dest = document.getElementById('fe_style_dest').value;
	var style_life = document.getElementById('fe_style_life').value;
	var hdr_img_src = document.getElementById('fe_headerImgSrc').value;
	var offerTitle = document.getElementById('fe_offerTitle').value;
	var offerId = document.getElementById('fe_offerId').value;

	var clientIds = '';
	var rows = document.getElementById(tbl_id).tBodies[0].rows;
    for (var i=1; i<rows.length; i++) {
	    var row = rows[i];
	    var cell = row.cells[1];
	    var oid = row.cells[2].firstChild;

	if(!oid) {
		oid = '';
	} else {
		oid = oid.nodeValue;
	}

		clientIds += cell.firstChild.nodeValue + ':' + oid + '-';
	}

	if (!clientIds) {
		alert('You must select some products.');
		return false;
	}

	document.getElementById('submit_but').style.display = 'none';
	ajaxLoader();
	
	var link = "<?=$page;?>?do_ajax=1";
	link += "&year=" + year;
	link += "&month=" + month;
	link += "&day=" + day;
	link += "&title=" + escape(title);
	link += "&style_d=" + style_dest;
	link += "&style_l=" + style_life;
	link += "&hdr_img=" + escape(hdr_img_src);
	link += "&offerTitle=" + escape(offerTitle);
	link += "&offerId=" + escape(offerId);
	link += "&product_str=" + clientIds;

	executeAjax(link, updateMsg);	

	document.getElementById('submit_but').style.display = '';
	ajaxLoaderClose();
	document.getElementById('updateMsg').innerHTML = '';
	document.getElementById('updateMsg').style.display = '';;
}

</script>
<link rel="stylesheet" type="text/css" href="../Include/admin_css.css" />

</head>
<body>
<div id="container">
<h3 class="hdr">Featured Escapes Tool</h3>
<div>
Use this tool to set the products on the Featured Escapes module on the homepage.  Select the destination style and lifestyle and populate the slots with the desired products.  Double click on the slot number to delete a row.
<br />
</div>

<div class="searchBox">
Select Date: <br /><br />
<form method="POST" action="<?=$page;?>">

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

<input type="submit" name="submit_date" value="Submit" />
</form>
<br />

<a href="<?=$page;?>?year=<?=date('Y', $ts_prev_day);?>&month=<?=date('m', $ts_prev_day);?>&day=<?=date('d', $ts_prev_day);?>">Prev Day</a>&nbsp;&nbsp;&nbsp;
<a href="<?=$page;?>?year=<?=date('Y', $ts_next_day);?>&month=<?=date('m', $ts_next_day);?>&day=<?=date('d', $ts_next_day);?>">Next Day</a>
</div>

You are editing for: <strong><? echo date('F d, Y (l)', strtotime("$month/$day/$year"));?></strong><br /><br />

<div style="width:500px;text-align:left;">
	<table width="800" class="werd" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<th style="background-color: #CCEE88;" width="100">Date</th>
		<th style="background-color: #CCEE88;" width="150">Destination</th>
		<th style="background-color: #CCEE88;" width="150">Style</th>
		<th style="background-color: #CCEE88;" width="250">Title</th>
	</tr>
	<tr>
		<td width="100"><?=$data['dateLive'];?></td>
		<td width="150">
			<select id="fe_style_dest" style="width:100%;">
				<option value="">Select Destination</option>
				<?php
				foreach ($style_dest as $k=>$v) {
					$sel = $style_dest_selected == $k ? "selected=\"selected\"" : '';
					echo "<option value=\"$k\" $sel>$v</option>";
				}
				?>
			</select>
		</td>
		<td width="150">
			<select id="fe_style_life" style="width:100%;">
				<option value="">Select Style</option>
				<?php
				foreach ($style_life as $k=>$v) {
					$sel = $style_life_selected == $k ? "selected=\"selected\"" : '';
					echo "<option value=\"$k\" $sel>$v</option>";
				}
				?>
			</select>
		</td>
		<td width="250"><input id="fe_title" type="text" style="width:100%;" value="<?=$data['title'];?>" /></td>
	</tr>
	<tr> 
		<td style="background-color: #CCEE88; font-weight: bold;" colspan="4">Header Image Source</td>
	</tr>
	<tr>
		<td colspan="4"><input id="fe_headerImgSrc" type="text" style="width:100%;" value="<?=$data['headerImgSrc'];?>" /></td>
	</tr>
	<tr> 
		<td style="background-color: #CCEE88; font-weight: bold;" colspan="2">Gray Box Offer Title</td>
		<td style="background-color: #CCEE88; font-weight: bold;" colspan="2">Offer Id</td>
	</tr>
	<tr> 
		<td style="background-color: #CCEE88; font-weight: bold;" colspan="2"><input id="fe_offerTitle" type="text" style="width:100%;" value="<?=$data['offerTitle'];?>" /></td>
		<td style="background-color: #CCEE88; font-weight: bold;" colspan="2"><input id="fe_offerId" type="text" style="width:100%;" value="<?=$data['offerId'];?>" /></td>
	</tr>
	</table>				
</div>

<br /><br /><br />
<div class="navLink" style="text-align:right;"> 
	<a href="Javascript:void(0);" onclick="return scheduleFeaturedEscapesAuto();">&raquo; Auto Populate</a>
	<a style="margin-left:20px;" href="Javascript:void(0);" onclick="return scheduleFeaturedEscapes();">&raquo; Schedule Featured Escapes</a>
</div>

<form method="post" action="<?=$page;?>">
<table width="800" id="dest_table" class="werd" cellspacing="0" cellpadding="0" border="0">
<tr NoDrag NoDrop>
	<th width="30">Slot</th>
	<th width="100">Product ID</th>
	<th width="100">Offer ID</th>
	<th>Product Name</th>
</tr>
<?
$i=0;
foreach($data_products as $k=>$v) {
	$i++;
?>
<tr>
	<td ondblclick="deleteRow('dest_table',parseInt(this.innerHTML));"><? echo $i;?></td>
	<td><? echo $v['clientId']; ?></td>
	<td><? echo $v['offerId']; ?></td>
	<td><? echo $v['name']; ?></td>
</tr>
<?
}

?>
</table>

<script type="text/javascript">
var table = document.getElementById("dest_table");
var tableDnD = new TableDnD();
tableDnD.init(table);
</script>

<br /><br/ >
Product ID: <input type="text" id="addManualPID" /> <br />
Featured Offer ID: <input type="text" id='addManualOID'>
<input type="button" onclick="addManualProduct();" value="Add Product Id" />

<div id="submit_but" style="display: '';text-align:right;">
<input type="button" onclick="processUpdateFeaturedEscapes('<?=$year;?>','<?=$month;?>','<?=$day;?>','dest_table');" name="update_te" value="Save Changes" />
</div>

<div id="ajaxLoader" style="display: none; margin: 0px; padding: 0px; width: 126px; height: 22px;"></div>
<div id="updateMsg"></div>
</form>
</div>