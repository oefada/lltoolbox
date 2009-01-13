<?php

require("../Include/smarty_setup.php");

ini_set('error_reporting', 2039);

$smarty = new Smarty_LuxuryLink_Admin();
$smarty->caching = false;
$smarty->force_compile = true;

// Set template id for smarty and to determine webtrends mc_id
$templateId = ($_GET['temp']) ? $_GET['temp'] : 'c';

// google tracking
$utm_qs = ($templateId == 'b') ? 'utm_source=email&utm_medium=news&utm_content=monday' : 'utm_source=email&utm_medium=news&utm_content=thursday';
$smarty->assign('utm_qs', $utm_qs);

$result = ll_execute_sproc('llsp_sel_mailing_schedule', array('mailing_schedule_id'=>$_GET['mailing_schedule_id']));
$mailing_timestamp = mssql_result($result, 0, 'mailing_timestamp');
$smarty->assign('mailing_timestamp', $mailing_timestamp);

$mailing_day = date('D', $mailing_timestamp);

$result = ll_execute_sproc('llsp_sel_mailing_segments', array('maling_schedule_id'=>$_GET['mailing_schedule_id']));
while($row = mssql_fetch_array($result)) {
	$segments[$row['mailing_segment_id']] = $row;
}

// Get products/offers for segments
$query = "
			SELECT s.mailing_segment_id, s.mailing_segment_type_id, s.mailing_segment_position_id, o.offer_id,a.auction_name,a.auction_minimum_bid,a.auction_retail,a.auction_type_id,
			sp.clientId,a.auction_subtitle,a.short_blurb,pm.product_title,pm.product_blurb,pm.product_desc,pm.product_city,
			pm.product_state,pm.product_country,pm.product_url
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
		$product_location = array();
		$product_location[] = preg_replace("/\(.*\)/", '', $row['product_city']);
		if(!strstr($row['product_state'], '(') && $row['product_state']) {
			$product_location[] = $row['product_state'];
		}
		$product_location[] = $row['product_country'];
		
		$segments[$row['mailing_segment_id']]['title'] = ($row['mailing_segment_type_id'] == 2) ? $row['auction_name'] : $row['product_title'];
		$segments[$row['mailing_segment_id']]['title_header'] = $row['product_desc'];
		$segments[$row['mailing_segment_id']]['subtitle'] = $row['auction_subtitle'];
		$segments[$row['mailing_segment_id']]['blurb'] = ($row['mailing_segment_type_id'] == 2) ? $row['auction_subtitle'] : $row['product_desc'] . ', ' .  implode(', ', $product_location);
		$segments[$row['mailing_segment_id']]['copy'] = ($row['mailing_segment_type_id'] == 2) ? $row['short_blurb'] : $row['product_blurb'];
		$segments[$row['mailing_segment_id']]['link_label'] = ($row['mailing_segment_type_id'] == 2) ? 'Bid or Buy' : 'Read more';
		$segments[$row['mailing_segment_id']]['alt_tag'] = $row['product_name'];
		$segments[$row['mailing_segment_id']]['url'] = ($row['mailing_segment_type_id'] == 3) ? $row['product_url'] : 'http://www.luxurylink.com/portfolio/por_offer_redirect.php?id=' . $row['offer_id'] . '&productId=' . $row['clientId'];
		$segments[$row['mailing_segment_id']]['offer_retail'] =  number_format($row['auction_retail'], 2);
		$segments[$row['mailing_segment_id']]['offer_minimum_bid'] =  number_format($row['auction_minimum_bid'], 2);
		$segments[$row['mailing_segment_id']]['offer_saving'] =  number_format(($row['auction_retail']) - ($row['auction_minimum_bid']), 2);
		$segments[$row['mailing_segment_id']]['offer_type_id'] = $row['auction_type_id'];
		$segments[$row['mailing_segment_id']]['offer_type'] = (in_array($row['auction_type_id'],array(3,4))) ? 'fp' : 'auc';

		$clientIds[] = $row['clientId'];
	}
	foreach($segments as $segment) {
		$segmentsIndexed[] = $segment;
	}
	$clientIds = implode(';', $clientIds);
	$smarty->assign('clientIds', $clientIds);
}

$smarty->assign('segments', $segmentsIndexed);

$html = $smarty->fetch('news_scheduler/ns_view_html_' . $templateId . '.tpl');
$text = $smarty->fetch('news_scheduler/ns_view_text.tpl');

if($_GET['preview_html']) {
	echo $html;
	echo '<p style="font-weight: bold;"><a href="ns_view_html.php?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&amp;temp=' . $templateId . '">Generate HTML</a></p>';
	echo '<p><a href="ns_view_html.php?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&preview_html=1&temp=a">Preview Template A</p>';
	echo '<p><a href="ns_view_html.php?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&preview_html=1&temp=b">Preview Template B</p>';
	echo '<p><a href="ns_view_html.php?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&preview_html=1&temp=c">Preview Template C</p></body></html>';
}else{
	echo '<p><a href="ns_view_html.php?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&amp;temp=' . $templateId . '&amp;preview_html=1">Preview HTML</a></p>';
	echo '<textarea style="width: 640px;  height: 320px;">' . $html . '</body></html></textarea>';
	echo '<textarea style="width: 640px;  height: 320px;">' . $text . '</textarea>';
}
