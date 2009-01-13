<?php

if($_POST) {
	include('ns_process_segment.inc.ctp');
}elseif($_GET['mailing_schedule_id']) {
	$mailing_schedule_id = $_GET['mailing_schedule_id'];
	$mailing_segment_id = $_GET['mailing_segment_id']; // May not be set when adding for 1st time
}

// Get segment data
//$result = ll_execute_sproc('llsp_sel_mailing_segments', array('mailing_segment_id'=>$mailing_segment_id));
$result = mysql_query('SELECT     s.mailing_segment_id, s.mailing_schedule_id, s.mailing_segment_position_id, s.mailing_segment_type_id, s.mailing_segment_sort_order, 
                      t.mailing_segment_type_description AS mailing_segment_type_desc, p.mailing_segment_position_description, o.offer_id, h.mailing_segment_html AS mailing_segment_html, sp.clientId
FROM         mailing_segment s INNER JOIN
                      mailing_segment_position p ON s.mailing_segment_position_id = p.mailing_segment_position_id INNER JOIN
                      mailing_segment_type t ON s.mailing_segment_type_id = t.mailing_segment_type_id LEFT OUTER JOIN
                      mailing_segment_product sp ON s.mailing_segment_id = sp.mailing_segment_id LEFT OUTER JOIN
                      mailing_segment_html h ON s.mailing_segment_id = h.mailing_segment_id LEFT OUTER JOIN
                      mailing_segment_offer o ON s.mailing_segment_id = o.mailing_segment_id
WHERE     (mailing_schedule_id = "'.$mailing_schedule_id.'")');

while(is_resource($result) && $row = mysql_fetch_assoc($result)) {
	if($row['mailing_segment_id'] == $mailing_segment_id) {	
		$mailing_segment_html = $row['mailing_segment_html'];
		$mailing_segment_position_id = $row['mailing_segment_position_id'];
		$mailing_segment_sort_order = $row['mailing_segment_sort_order'];
		$mailing_segment_type_id = $row['mailing_segment_type_id'];
		break;
	}
}

// Select
// Get products/offers for segments
$query = "SELECT s.mailing_segment_id, o.offer_id,schedulingMaster.packageName as auction_name, sp.clientId,schedulingMaster.subtitle as auction_subtitle,package.shortBlurb as short_blurb,
			pm.name as product_name,pm.blurb as product_blurb
			FROM mailing_segment s
			INNER JOIN mailing_segment_position p ON s.mailing_segment_position_id = p.mailing_segment_position_id
			LEFT OUTER JOIN mailing_segment_product sp ON s.mailing_segment_id = sp.mailing_segment_id
			LEFT OUTER JOIN mailing_segment_offer o ON s.mailing_segment_id = o.mailing_segment_id
			LEFT OUTER JOIN offer a ON o.offer_id = a.offerId
			LEFT JOIN schedulingInstance ON schedulingInstance.schedulingInstanceId = a.offerId
			LEFT JOIN schedulingMaster ON schedulingMaster.schedulingMasterId = schedulingInstance.schedulingInstanceId
			LEFT JOIN package ON package.packageId = schedulingMaster.packageId
			LEFT OUTER JOIN client pm ON sp.clientId = pm.clientId
			WHERE     (mailing_schedule_id = $mailing_schedule_id)
			AND s.mailing_segment_id = $mailing_segment_id
			ORDER BY  p.mailing_segment_position_sort_order, s.mailing_segment_sort_order";

$result = mysql_query($query);

while(is_resource($result) && $row = mysql_fetch_assoc($result)) {
	//if($row['clientId'] && $row['clientId'] != $test_clientId) {
		$segments[$row['mailing_segment_id']]['products'][$row['clientId']] = $row;
	//}
	if($row['offer_id'] && $row['offer_id'] != $test_offer_id) { // probably don't need to test on offer_id because clientId will always need to exist
		$segments[$row['mailing_segment_id']]['offers'] .= substr($row['auction_name'], 0 ,16) . ' (' . $row['offer_id'] . ')<br>';
	}
	$test_offer_id = $row['offer_id'];
	$test_clientId = $row['clientId'];
}

/* Build arrays for drop downs */
// Segment types
$result= mysql_query('SELECT mailing_segment_type_id,mailing_segment_type_description AS mailing_segment_type_desc FROM mailing_segment_type');
while($row = mysql_fetch_array($result)) {
	$segment_types_select[$row['mailing_segment_type_id']] = $row['mailing_segment_type_desc'];
}
// Segment positions
$result = mysql_query('SELECT mailing_segment_position_id,mailing_segment_position_description AS mailing_segment_position_desc FROM mailing_segment_position');
while($row = mysql_fetch_array($result)) {
	$segment_positions_select[$row['mailing_segment_position_id']] = $row['mailing_segment_position_desc'];
}
// Credit types
$result = mysql_query('SELECT mailing_segment_credit_type_id AS credit_type_id, mailing_segment_credit_type_value AS credit_type_value
FROM mailing_segment_credit_type');
while($row = mysql_fetch_array($result)) {
	$segment_product_types_select[$row['credit_type_id']] = $row['credit_type_value'];
}
?>

<!-- Add segment -->
<p><?=($mailing_segment_id) ? 'Edit' : 'Add'?> segment</p>
<a href="ns_main?pg=ns_maint&mailing_schedule_id=<?=$mailing_schedule_id?>">Mailing schedule</a> | <a href="ns_main?pg=ns_product_listing&mailing_schedule_id=<?=$mailing_schedule_id?>&mailing_segment_id=<?=$mailing_segment_id?>">Product lizard</a> | <a href="ns_view_segment_html?mailing_schedule_id=<?=$mailing_schedule_id?>&mailing_segment_id=<?=$mailing_segment_id?>&preview_html=1" target="_ blank">View html</a>
<br>
<br>
<form name="mailing_segment_form" action="ns_main" method="post">
<input type="hidden" name="mailing_schedule_id" value="<?=$mailing_schedule_id?>">
<input type="hidden" name="mailing_segment_id" value="<?=$mailing_segment_id?>">
<input type="hidden" name="pg" value="<?=$pg?>">
<input type="hidden" name="mailing_segment_sort_order" value="<?=@$mailing_segment_sort_order?>">
<table width="400" cellspacing="0" cellpadding="4" border="1">
	<tr>
		<td width="25%" class="table_header_row">Position</td>
		<td>
			<select name="mailing_segment_position_id" required='1' label="Segment position" class="input260">
				<option value="">Segment postion</option>
				<?=html_options($segment_positions_select, $mailing_segment_position_id)?>
			</select>			
		</td>
	</tr>
	<tr>
		<td class="table_header_row">Segment type</td>
		<td>
			<select name="mailing_segment_type_id" required='1' label="Segment type" class="input260" onChange="set_field_enable(this.form, this.value)">
				<option value="">Segment promo type</option>
				<?=html_options($segment_types_select, $mailing_segment_type_id)?>
			</select>
		</td>
	</tr>
	<tr>
			<td class="table_header_row">Add offer Id</td>
			<td><input id="offer_id" type="text" name="offer_id" required='1' label="Offer id" value="<?=@$offer_id?>"></td>
	</tr>
	<tr>
		<td class="table_header_row">Add product Id</td>
			<td>
				<input id="clientId" type="text" name="clientId" required='1' label="Product id" value="<?=@$clientId?>">
				<select id="mailing_segment_credit_type_id" name="mailing_segment_credit_type_id" required='1' label="Credit" class="select_numeric">
					<option value="">Credit</option>
					<?=html_options($segment_product_types_select)?>
				</select>
				<script language="javascript">
					set_field_enable(document.mailing_segment_form,<?=$mailing_segment_type_id?>);
				</script>
			</td>
	</tr>
	<tr>
		<td class="table_header_row">Offer(s)</td>
		<td><?=@$segments[$mailing_segment_id]['offers']?></td>
	</tr>
	<tr>
		<td class="table_header_row">Product(s)</td>
		<td><?debug($segments)?><?=display_segment_product_data(@$segments[$mailing_segment_id]['products'])?></td>
	</tr>
	<tr>
		<td colspan="2">
			<span  class="table_header_row"><br>Custom HTML<br></span>
			<textarea name="mailing_segment_html" class="htmlTextArea" rows=10 cols=80><?=@$mailing_segment_html?></textarea>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="right"><input type="button" onClick="validate_form(document.mailing_segment_form)" value="<?=($mailing_segment_id) ? 'Edit' : 'Add'?> segment"></td>
	</tr>
</table>
</form>