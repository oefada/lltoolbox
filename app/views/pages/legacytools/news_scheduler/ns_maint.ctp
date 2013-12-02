<?php

if(@$_POST['schedule_form']) {
	
	$mailing_name = $_POST['mailing_name'];

	$mailing_timestamp = strtotime($_POST['mailing_date']);
	$params = array(
					'mailing_type_id'=>$_POST['mailing_type_id'],
					'mailing_timestamp'=>$mailing_timestamp,
					'mailing_name'=>$mailing_name,
					'mailing_subject'=>$_POST['mailing_subject']
				);

	if($_POST['mailing_schedule_id']) { // Update
		$mailing_schedule_id = $_POST['mailing_schedule_id']; // Set global
		$params = array_merge(array('mailing_schedule_id'=>$_POST['mailing_schedule_id']), $params);
		
		extract($params);
		
		mysql_query("UPDATE mailing_schedule
		SET mailing_type_id = '$mailing_type_id', mailing_timestamp = '$mailing_timestamp', mailing_subject = '$mailing_subject'
		WHERE  (mailing_schedule_id = '$mailing_schedule_id')");
		
		if(!empty($mailing_name)) {
			mysql_query("SELECT * FROM mailing_name WHERE (mailing_schedule_id = '$mailing_schedule_id')");

			if(mysql_affected_rows() == 0) {
				mysql_query("INSERT INTO mailing_name(mailing_schedule_id,mailing_name)
				VALUES('$mailing_schedule_id', '$mailing_name')");
			} else {
				mysql_query("UPDATE mailing_name
				SET mailing_name = '$mailing_name'
				WHERE (mailing_schedule_id = '$mailing_schedule_id')");
			}
		} else {
			mysql_query("DELETE FROM mailing_name WHERE (mailing_schedule_id = '$mailing_schedule_id')");
		}			
	}else{ // Create
		extract($params);
		
		mysql_query("INSERT INTO mailing_schedule
					(mailing_type_id, mailing_timestamp, mailing_subject)
					VALUES ('$mailing_type_id','$mailing_timestamp', '$mailing_subject')");
		$mailing_schedule_id = mysql_insert_id();
		if($mailing_name) {
			mysql_query("INSERT INTO mailing_name(mailing_schedule_id,mailing_name)
			VALUES ('$mailing_schedule_id','$mailing_name')");	
		}
	}
}elseif(@$_GET['mailing_schedule_id']) {
	$mailing_schedule_id = $_GET['mailing_schedule_id']; // Set global
} else {
	$mailing_schedule_id = 0;
}

/* Modify segment position and order */
if(@$_POST['mailing_segment_id']) {
	// Right now these arrays have one more dimension than what I'm accessing on lines 58 and 59
	$segment_position = $_POST['segment_position'];
	$segment_sort_order = $_POST['segment_sort_order'];
	$current_segment_sort_order = $_POST['current_segment_sort_order'];
	
	$params = array();
	$params['mailing_schedule_id'] = $mailing_schedule_id;
	$params['current_mailing_segment_sort_order'] = $_POST['current_segment_sort_order'];
	$params['mailing_segment_sort_order'] = $_POST['segment_sort_order'];
	$params['mailing_segment_position'] = $_POST['segment_position'];
	//print_r($params);
	//$result = ll_execute_sproc('llsp_upd_mailing_segment_sort_order', $params);
		
	$query = "UPDATE mailing_segment
						SET mailing_segment_sort_order = $segment_sort_order, mailing_segment_position_id = $segment_position
						WHERE mailing_segment_id = $mailing_segment_id";
	$queryy = "UPDATE mailing_segment
						SET mailing_segment_position_id = $segment_position
						WHERE mailing_segment_id = $mailing_segment_id";

	$result = mssql_query($query);

}

/* End modify segment position and order */

// Get mailing schedule record
$query = 'SELECT     s.mailing_schedule_id, s.mailing_type_id, s.mailing_timestamp, s.mailing_subject, n.mailing_name, mailing_type.mailing_type_description
FROM         mailing_schedule s INNER JOIN
                      mailing_type ON s.mailing_type_id = mailing_type.mailing_type_id LEFT OUTER JOIN
                      mailing_name n ON s.mailing_schedule_id = n.mailing_schedule_id
WHERE s.mailing_schedule_id = "'.$mailing_schedule_id.'"';

$result = mysql_query($query);
while($row = mysql_fetch_array($result)) {
	$mailing_name = $row['mailing_name'];
	$mailing_date = date('D, Y-m-d h:ia', $row['mailing_timestamp']);
	$mailing_type_id = $row['mailing_type_id'];
	$mailing_subject = $row['mailing_subject'];
}

/* Segments */
$segments = array();
if($mailing_schedule_id) {
	// Delete
	if(@$_GET['delete_segment']) {
		$result = llsp_del_mailing_segment(array('mailing_segment_id'=>$_GET['delete_segment']));
	}
	// Update
	
	// Select
	$result = mysql_query('SELECT s.mailing_segment_id, s.mailing_schedule_id, s.mailing_segment_position_id, s.mailing_segment_type_id, s.mailing_segment_sort_order, 
	                      t.mailing_segment_type_description AS mailing_segment_type_desc, p.mailing_segment_position_description, o.offer_id, h.mailing_segment_html AS mailing_segment_html, sp.clientId as clientId
	FROM         mailing_segment s INNER JOIN
	                      mailing_segment_position p ON s.mailing_segment_position_id = p.mailing_segment_position_id LEFT JOIN
	                      mailing_segment_type t ON s.mailing_segment_type_id = t.mailing_segment_type_id LEFT JOIN
	                      mailing_segment_product sp ON s.mailing_segment_id = sp.mailing_segment_id LEFT JOIN
	                      mailing_segment_html h ON s.mailing_segment_id = h.mailing_segment_id LEFT JOIN
	                      mailing_segment_offer o ON s.mailing_segment_id = o.mailing_segment_id
	WHERE     (mailing_schedule_id = "'.$mailing_schedule_id.'")');

	while(is_resource($result) && $row = mysql_fetch_assoc($result)) {
		$segments[$row['mailing_segment_id']] = $row;
		@$segment_positions[$row['mailing_segment_position_id']]++;
	}
	
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
				LEFT OUTER JOIN client pm ON sp.clientId = pm.clientId #change this to clientId at one point or another
				WHERE     (mailing_schedule_id = $mailing_schedule_id)
				ORDER BY  p.mailing_segment_position_sort_order, s.mailing_segment_sort_order";

	$result = mysql_query($query);
	echo mysql_error();
	while(is_resource($result) && $row = mysql_fetch_array($result)) {
		if($row['clientId']) {
			$segments[$row['mailing_segment_id']]['products'][$row['clientId']] = $row;
		}
		if($row['offer_id'] && $row['offer_id'] != @$test_offer_id) { // probably don't need to test on offer_id because clientId will always need to exist
			@$segments[$row['mailing_segment_id']]['offers'] .= substr($row['auction_name'], 0 ,16) . ' (' . $row['offer_id'] . ')<br>';
		}
		$test_offer_id = $row['offer_id'];
		$test_clientId = $row['clientId'];
	}
}
/* End segments */

/* Build arrays for drop downs */
// Mailing types
$result = mysql_query('SELECT mailing_type_id,mailing_type_description FROM mailing_type');
while($row = mysql_fetch_array($result)) {
	$mailing_types_select[$row['mailing_type_id']] = $row['mailing_type_description'];
}
$time_of_day_select = get_times_of_day(12,8);

$result = mysql_query('SELECT mailing_segment_position_id,mailing_segment_position_description AS mailing_segment_position_desc FROM mailing_segment_position');
while($row = mysql_fetch_array($result)) {
	$segment_positions_select[$row['mailing_segment_position_id']] = $row['mailing_segment_position_desc'];
}

?>

<p>Mailing</p>

<form action="ns_main" method="post">
<input type="hidden" name="mailing_schedule_id" value="<?=$mailing_schedule_id?>">
<input type="hidden" name="pg" value="<?=$pg?>">
<input type="hidden" name="schedule_form" value="1">
<table cellspacing="0" cellpadding="4" border="1">
	<tr>
		<td>Theme/Description:</td>
		<td><input type="text" name="mailing_name" value="<?=@$mailing_name?>" class="input260"></td>
	</tr>
	<tr>
		<td>Subject:</td>
		<td><input type="text" name="mailing_subject" value="<?=@$mailing_subject?>" class="input260"></td>
	</tr>
	<tr>
		<td>Mailing type:</td>
		<td>
			<select name="mailing_type_id" class="input260">
				<option value="">Choose mailing type</option>
				<?=html_options($mailing_types_select, $mailing_type_id)?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Mailing date:</td>
		<td><input type="text" class="input260" name="mailing_date" value="<?=@$mailing_date?>">
			<a href="javascript:displayDatePicker('mailing_date');">Choose</a>
		</td>
	</tr>
	

	
	<tr>
		<td>Mailing Time:</td>
		<td>
			<select name="mailing_date_time" onChange="mailing_date.value += ' ' + mailing_date_time.value">
				<?=html_options($time_of_day_select, $mailing_time_of_day)?>
			</select>
		</td>
	</tr>
	

	
	<tr>
		<td>&nbsp;</td>
		<td align="right"><input type="submit" name="mailing" value="<?=($mailing_schedule_id) ? 'Edit' : 'Create'?>"></td>
	</tr>
</table>
</form>

<br>
<a href="ns_main?pg=ns_segment_maint&mailing_schedule_id=<?=$mailing_schedule_id?>">Add segment</a> | <a href="ns_main?pg=ns_product_listing&mailing_schedule_id=<?=$mailing_schedule_id?>">Product lizard</a> | <a href="ns_view_html?mailing_schedule_id=<?=$mailing_schedule_id?>&preview_html=1" target="_blank">Preview HTML</a>

<p>Segments</p>

<table cellspacing="0" cellpadding="4" border="1">
	<tr class="table_header_row">
		<td>Position</td>
		<td>Sort order</td>
		<td>Html</td>
		<td>Offer(s)</td>
		<td>Product(s)</td>
		<td>Segment type</td>
		<td>View html</td>
		<!--td>Product list</td-->
		<td>Edit</td>
		<td>Remove</td>
	</tr>
	
<?
foreach($segments as $segment) { ?>
	<form action="ns_main" name="segment_form_<?=$segment['mailing_segment_id']?>" method="post">
		<input type="hidden" name="mailing_schedule_id" value="<?=$mailing_schedule_id?>">
		<input type="hidden" name="pg" value="<?=$pg?>">
		<input type="hidden" name="mailing_segment_id" value="<?=$segment['mailing_segment_id']?>">
		<input type="hidden" name="current_segment_sort_order" value="<?=$segment['mailing_segment_sort_order']?>">
	<tr>
		<td>
			<select name="segment_position" onChange="document.segment_form_<?=$segment['mailing_segment_id']?>.submit();">
				<?=html_options($segment_positions_select, $segment['mailing_segment_position_id'])?>
			</select>
		</td>
		<td>
			<select name="segment_sort_order" onChange="document.segment_form_<?=$segment['mailing_segment_id']?>.submit();">
				<option value="-1">Select</option>
			<?=html_options_numeric(count($segments), $segment['mailing_segment_sort_order'])?>	
		</td>
		<td><?=(@$segment['mailing_segment_html']) ? 'custom' : 'auto'?></td>
		<td><?=$segment['offers']?></td>
		<td><?=display_segment_product_data($segment['products'])?></td>
		<td><?=$segment['mailing_segment_type_desc']?></td>
		<td><a href="ns_view_segment_html?mailing_schedule_id=<?=$mailing_schedule_id?>&mailing_segment_id=<?=$segment['mailing_segment_id']?>&preview_html=1" target="_ blank">View html</a></td>
		<!--td><a href="ns_main?pg=ns_product_listing&mailing_schedule_id=<?=$mailing_schedule_id?>">Product listing</a></td-->
		<td><a href="ns_main?pg=ns_segment_maint&mailing_schedule_id=<?=$mailing_schedule_id?>&mailing_segment_id=<?=$segment['mailing_segment_id']?>">Edit</a></td>
		<td><a href="ns_main?pg=ns_maint&mailing_schedule_id=<?=$mailing_schedule_id?>&delete_segment=<?=$segment['mailing_segment_id']?>">Remove<a></td>
	</tr>
</form>
	
	
<? } ?>

</table>