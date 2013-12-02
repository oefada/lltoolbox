<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('default_mysql');

$page = 'merchandise_images_tool_styles';
$page_name = 'Merchandise Images Tool (Styles)';

$styleId = isset($_REQUEST['styleId']) ? $_REQUEST['styleId'] : 0;

if ($_POST['submit_mei'] && $styleId) {
	
	$z=1;
	for ($i=1; $i<10; $i++) {
		if (!empty($_POST["mei_image_url_$i"])) {
			$image_url[$z] = $_POST["mei_image_url_$i"];
			$product_id[$z] = $_POST["mei_product_id_$i"];
			$link_url[$z] = $_POST["mei_link_url_$i"];
			$z++;
		}
	}

	$result = mysql_query( "select * from merchandiseStyleImage where styleId = $styleId");
	if (!$result) {
		die('DATABASE PROBLEM');
	}

	if (mysql_num_rows($result)) {
		$delete_result = mysql_query("delete from merchandiseStyleImage where styleId = $styleId");	
	} 

	foreach ($image_url as $k=>$v) {
		$product_desc = '';
		$insert = mysql_query("insert into merchandiseStyleImage (styleId,slotId,clientId,imageUrl,linkUrl,caption) VALUES ($styleId, $k, '$product_id[$k]','$v', '$link_url[$k]', '$product_desc')");
		if ($insert) {
			$data_saved = '<div class="greenBox">Data has been saved.</div>';
		}
	}
}

// retreive all styles
$data = array();
$result = mysql_query("SELECT * FROM style_mstr");
while ($row = mysql_fetch_array($result)) {
	$data[$row['styleId']] = $row['styleName'];
}
asort($data);

// retrieve all rows with a style
if ($styleId) {
	$mer_style_data = array();
	$result = mysql_query("SELECT * from merchandiseStyleImage WHERE styleId = $styleId ORDER BY slotId");
	$i=0;
	while ($row = mysql_fetch_array($result)) {
		$i++;
		$mer_style_data[$i] = $row;
	}
}

?>
<script language="Javascript">
function changeStyle(styleId) {
	window.location.replace('<?=$page;?>?styleId=' + styleId);
}
</script>

<link rel="stylesheet" type="text/css" href="../Include/admin_css.css" />

</head>
<body>
<div id="container">
<h3 class="hdr"><?=$page_name;?></h3>
<div>
Use this tool to setup the merchandising images for the style landing pages.  Double click on the slot id to delete a row.<br /><br />
If you do not set a product id, the image caption won't be set.
<br />
</div>

<div class="searchBox">
	<select name="styleId" onchange="changeStyle(this.value);">
		<option value="0">Please select a style</option>
	<?php
	foreach ($data as $sid => $styleName) {
		$selected = ($styleId == $sid) ? 'selected="selected"' : '';
		echo "<option value=\"$sid\" $selected>$styleName</option>";
	}
	?>
	</select>
</div>

<?php 
	if (!$styleId) {
		echo '<div class="redBox"><strong>Please choose a style</strong></div>';
	} else {
		echo 'You are editing for : <strong>' . $data[$styleId] . '</strong>';
	}
?>

<div style="text-align:right;margin-top:5px;margin-bottom:5px;">&nbsp;
	<a href="http://preview.luxurylink.com/destinations/index.php?style=<?php echo $styleId;?>" target="_BLANK">Preview (Save First)</a>
</div>

<form method="post" action="<?=$page;?>">
<input type="hidden" name="styleId" value="<?php echo $styleId;?>" />
<table width="800" id="dest_table" class="werd" cellspacing="0" cellpadding="0" border="0">
	<tr>
		<th width="30">Slot</th>
		<th width="50">Product ID</th>
		<th>Image Url</th>
		<th>Link Url</th>
	</tr>
	<?php
	for ($i=1; $i<10; $i++) {
		?>
	<tr>
		<td><?=$i;?></td>
		<td><input type="text" style="width:100%;" name="mei_product_id_<?=$i;?>" value="<?=@$mer_style_data[$i]['clientId'];?>" /></td>
		<td><input type="text" style="width:100%;" name="mei_image_url_<?=$i;?>" value="<?=@$mer_style_data[$i]['imageUrl'];?>" /></td>
		<td><input type="text" style="width:100%;" name="mei_link_url_<?=$i;?>" value="<?=@$mer_style_data[$i]['linkUrl'];?>" /></td>
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
<div id="updateMsg">
	<?php
		if ($data_saved) {
			echo $data_saved;
		}
	?>
</div>
</div>
