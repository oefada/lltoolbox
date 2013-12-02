<?php

require("../Include/smarty_setup.php");

ini_set('error_reporting', 2039);


/* temp connection to luxurymasterDEV */
mssql_select_db('luxurymasterDEV');

$smarty = new Smarty_LuxuryLink_Admin();
$smarty->caching = false;
$smarty->force_compile = true;

$result = ll_execute_sproc('llsp_sel_mailing_schedule', array('mailing_schedule_id'=>$_GET['mailing_schedule_id']));
$day_index = date('w', mssql_result($result, 0, 'mailing_timestamp'));
$mailing_days = array(1=>'Mon',4=>'Thurs');
$mailing_day = $mailing_days[date('w', mssql_result($result, 0, 'mailing_timestamp'))];
$smarty->assign('mailing_day', $mailing_day);

$result = ll_execute_sproc('llsp_sel_mailing_segments', array('maling_schedule_id'=>$_GET['mailing_schedule_id']));
while($row = mssql_fetch_array($result)) {
	$segments[$row['mailing_segment_id']] = $row;
}

// Get products/offers for segments
$query = "
			SELECT s.mailing_segment_id, s.mailing_segment_type_id, s.mailing_segment_position_id, o.offer_id,a.auction_name, sp.clientId,a.auction_subtitle,a.short_blurb,
			pm.product_name,pm.product_blurb
			FROM mailing_segment s
			INNER JOIN mailing_segment_position p ON s.mailing_segment_position_id = p.mailing_segment_position_id
			INNER JOIN mailing_segment_product sp ON s.mailing_segment_id = sp.mailing_segment_id
			INNER JOIN product_mstr pm ON sp.clientId = pm.clientId
			LEFT OUTER JOIN mailing_segment_offer o ON s.mailing_segment_id = o.mailing_segment_id
			LEFT OUTER JOIN auction_mstr a ON o.offer_id = a.auction_id
			WHERE     (mailing_schedule_id = $mailing_schedule_id)
			ORDER BY  p.mailing_segment_position_sort_order, s.mailing_segment_sort_order";

$result = mssql_query($query);
if(mssql_num_rows($result)) {
	while($row = mssql_fetch_array($result)) {
		//$row['product_city']
		
		$segments[$row['mailing_segment_id']]['title'] = ($row['mailing_segment_type_id'] == 2 && $row['mailing_segment_position_id'] != 1) ? $row['auction_name'] : $row['product_name'];
		$segments[$row['mailing_segment_id']]['blurb'] = ($row['mailing_segment_type_id'] == 2) ? $row['auction_subtitle'] : $product_location;
		$segments[$row['mailing_segment_id']]['copy'] = ($row['mailing_segment_type_id'] == 2) ? $row['short_blurb'] : $row['product_blurb'];
		$segments[$row['mailing_segment_id']]['link_label'] = ($row['mailing_segment_type_id'] == 2) ? 'Bid or Buy' : 'Read more';
		$clientIds[] = $row['clientId'];
	}
}

$smarty->assign('segments', $segments);

$html = $smarty->fetch('news_scheduler/ns_view_segment_html.tpl');

?>


<?
if($_GET['preview_html']) {
	echo '<p style="font-weight: bold;"><a href="ns_view_segment_html.php?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&mailing_segment_id=' . $_GET['mailing_segment_id'] . '">Generate HTML</a></p>';
	echo $html;
}else{
	echo '<p><a href="ns_view_segment_html.php?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&mailing_segment_id=' . $_GET['mailing_segment_id'] . '&preview_html=1">Preview HTML</a></p>';
	echo '<textarea style="width: 640px;  height: 320px;">' . $html . '</textarea>';
}