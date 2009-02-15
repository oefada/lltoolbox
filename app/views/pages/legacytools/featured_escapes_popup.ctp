<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('default_mysql');

$Page_Name = "featured_escapes_popup";

$do_ajax = isset($_GET['do_ajax']) ? true : false;
$do_ajax2 = isset($_GET['do_ajax2']) ? true : false;
$auto_fill = isset($_GET['auto_fill']) ? true : false;
$pid = isset($_GET['pid']) && !empty($_GET['pid']) && is_numeric($_GET['pid']) ? $_GET['pid'] : false;
$oid = isset($_GET['oid']) && !empty($_GET['oid']) && is_numeric($_GET['oid']) ? $_GET['oid'] : false;
if ($do_ajax2 && !$pid) {
	echo "<div class='redBox'>Product ID must be an integer.</div>";
	die();
}

if ($do_ajax2) {
	$result = mysql_query('select name from client where clientId = ' . $pid);
	if (!$result) {
		echo "<div class='redBox'>There has been a database problem.  Please contact your local developer. Werd.</div>";
	} elseif (!mysql_num_rows($result)) {
		echo "<div class='redBox'>That product id may not exist.  Please try again.</div>";
	} else {
		echo $pid . '@@' . $oid . '@@' . mysql_result($result,0,'name');
	}
	die();
}

if ($do_ajax && $auto_fill) {
	$style_ids = $_GET['style_ids'];
	$current_slot = $_GET['current_slot'];
	$max_slots = 5;

	if ($current_slot == $max_slots) {
		echo "<div class='redBox'>You have filled out the max of 5 slots already.</div>";
		die();
	}

	if (!$style_ids) {
		echo "<div class='redBox'>Please select a destination and style.</div>";
		die();
	}

	$result = mysql_query("SELECT * FROM style_mstr WHERE styleId = '{$style_ids}'");
	if (!$result) {
		echo "<div class='redBox'>There has been a database problem.  Please contact your local developer. Werd.</div>";
		die();
	}

	$data_to_send = $tmp = '';
	$current_slot++;
	while($row = mysql_fetch_array($result)) {
		if ($current_slot <= $max_slots) {
			$current_slot++;
			$tmp.= $row['product_id'] . '@@' . $row['product_desc'] . '%%';
		}
	}

	echo $tmp;
	die();
}

$style_dest = $_GET['style_dest'];
$style_life = $_GET['style_life'];

$result = mysql_query("SELECT * FROM style_mstr WHERE styleId = '{$style_dest}'");
$row = mysql_fetch_array($result);
$style_dest_name = $row['style_name'];

$result = mysql_query("SELECT * FROM style_mstr WHERE styleId = '{$style_life}'");
$row = mysql_fetch_array($result);
$style_life_name = $row['style_name'];


$data = array();
$result = ll_execute_sproc('llsp_sel_live_products_by_style', array("$style_dest-$style_life"));
while($row = mysql_fetch_array($result)) {
	$data[] = $row;
}

?>
	<script language="JavaScript" src="/js/legacy/admin_js.js"></script>
	<script language="JavaScript" src="/js/legacy/admin_ajax.js"></script>

<div id="container">
<h3 style="margin-top: 0px;" class="hdr">Products based on <?=$style_dest_name;?> and <?=$style_life_name;?></h3>
		
Add Featured Escape Product<br /><br />

<table cellpadding="5" cellspacing="0" border="0"> 
<tr>
	<th>Product ID</th>
	<th>Product Name</th>
	<th>Options</th>
	<th disabled="disabled">Number of Offers</th>
</tr>
<?php
foreach($data as $k=>$v) {
	?>
<tr>
	<td><?=$v['product_id'];?></td>
	<td id='ar_<?=$k;?>'><?=$v['product_desc'];?></td>
	<td>
		<input type="button" onclick="appendRow('dest_table','<?=$v['product_id'];?>', document.getElementById('ar_<?=$k;?>').innerHTML); this.style.display='none';" value="Use This Product" />
	</td>
	<td disabled="disabled"><?=$v['num_live_offers'];?></td>
</tr>
	<?
}
?>
</table>
<br /><br />
</div>