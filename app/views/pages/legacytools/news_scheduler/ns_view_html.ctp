<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('mysql_driver_default');
$this->layout = false;
require("smarty_setup.php");
Configure::write('debug', '0');
ini_set('error_reporting', 2039);

$smarty = new Smarty_LuxuryLink_Admin();
$smarty->caching = false;
$smarty->force_compile = true;

// Set template id for smarty and to determine webtrends mc_id
$templateId = ($_GET['temp']) ? $_GET['temp'] : 'c';
$mailing_schedule_id = $_GET['mailing_schedule_id'];
// google tracking
$utm_qs = ($templateId == 'b') ? 'utm_source=email&utm_medium=news&utm_content=monday' : 'utm_source=email&utm_medium=news&utm_content=thursday';
$smarty->assign('utm_qs', $utm_qs);

$result = mysql_query('SELECT  s.mailing_schedule_id, s.mailing_type_id, s.mailing_timestamp, s.mailing_subject, n.mailing_name, mailing_type.mailing_type_description
FROM         mailing_schedule s INNER JOIN
                      mailing_type ON s.mailing_type_id = mailing_type.mailing_type_id LEFT OUTER JOIN
                      mailing_name n ON s.mailing_schedule_id = n.mailing_schedule_id
WHERE s.mailing_schedule_id = '.$_GET['mailing_schedule_id']);

$mailing_timestamp = mysql_result($result, 0, 'mailing_timestamp');
$smarty->assign('mailing_timestamp', $mailing_timestamp);

$mailing_day = date('D', $mailing_timestamp);

$result = mysql_query('SELECT     s.mailing_segment_id, s.mailing_schedule_id, s.mailing_segment_position_id, s.mailing_segment_type_id, s.mailing_segment_sort_order, 
                      t.mailing_segment_type_description AS mailing_segment_type_desc, p.mailing_segment_position_description, o.offer_id, h.mailing_segment_html AS mailing_segment_html, sp.clientId
FROM         mailing_segment s INNER JOIN
                      mailing_segment_position p ON s.mailing_segment_position_id = p.mailing_segment_position_id INNER JOIN
                      mailing_segment_type t ON s.mailing_segment_type_id = t.mailing_segment_type_id LEFT OUTER JOIN
                      mailing_segment_product sp ON s.mailing_segment_id = sp.mailing_segment_id LEFT OUTER JOIN
                      mailing_segment_html h ON s.mailing_segment_id = h.mailing_segment_id LEFT OUTER JOIN
                      mailing_segment_offer o ON s.mailing_segment_id = o.mailing_segment_id
WHERE     (mailing_schedule_id = "'.$mailing_schedule_id.'")');
while($row = mysql_fetch_assoc($result)) {
	$segments[$row['mailing_segment_id']] = $row;
}

// Get products/offers for segments
$query = "SELECT s.mailing_segment_id, o.offer_id,schedulingMaster.packageName as auction_name, sp.clientId,schedulingMaster.subtitle as auction_subtitle,package.shortBlurb as short_blurb,
			pm.name as product_name,pm.blurb as product_blurb,
			s.mailing_segment_type_id, s.mailing_segment_position_id, o.offer_id,schedulingMaster.openingBid  as 'auction_minimum_bid',
			package.approvedRetailPrice as 'auction_retail',
			schedulingMaster.offerTypeId as 'auction_type_id',
			sp.product_id,schedulingMaster.subtitle as 'auction_subtitle',package.shortBlurb as 'short_blurb',
			pm.name as 'product_title',pm.longDesc as 'product_blurb',
			pm.blurb as 'product_desc',pm.city as 'product_city',
			pm.state as 'product_state',pm.country as 'product_country',
			pm.url as 'product_url'
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
if(mysql_num_rows($result)) {
	while($row = mysql_fetch_assoc($result)) {
		$product_location = array();
		$product_location[] = preg_replace("/\(.*\)/", '', $row['product_city']);
		if(!strstr($row['product_state'], '(') && $row['product_state']) {
			$product_location[] = $row['product_state'];
		}
		$product_location[] = $row['product_country'];
		
		$segments[$row['mailing_segment_id']]['product_id'] = $row['product_id']; //TODO: change this to clientId at some point
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
$htmlview = $smarty->fetch(APP.'views/pages/legacytools/news_scheduler/ns_view_html_' . $templateId . '.tpl');
$text = $smarty->fetch(APP.'views/pages/legacytools/news_scheduler/ns_view_text.tpl');

if($_GET['preview_html']) {
	echo $htmlview;
	echo '<p style="font-weight: bold;"><a href="ns_view_html?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&amp;temp=' . $templateId . '">Generate HTML</a></p>';
	echo '<p><a href="ns_view_html?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&preview_html=1&temp=a">Preview Template A</p>';
	echo '<p><a href="ns_view_html?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&preview_html=1&temp=b">Preview Template B</p>';
	echo '<p><a href="ns_view_html?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&preview_html=1&temp=c">Preview Template C</p></body></html>';
}else{
	echo '<p><a href="ns_view_html?mailing_schedule_id=' . $_GET['mailing_schedule_id'] . '&amp;temp=' . $templateId . '&amp;preview_html=1">Preview HTML</a></p>';
	echo '<textarea style="width: 640px;  height: 320px;">' . $htmlview . '</body></html></textarea>';
	echo '<textarea style="width: 640px;  height: 320px;">' . $text . '</textarea>';
}
