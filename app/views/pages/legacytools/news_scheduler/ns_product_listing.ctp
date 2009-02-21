<?php

// Need date to get valid LOA based on mailing date
$mailing_schedule_id = $_REQUEST['mailing_schedule_id'];
$result = mysql_query("SELECT     s.mailing_schedule_id, s.mailing_type_id, s.mailing_timestamp, s.mailing_subject, n.mailing_name, mailing_type.mailing_type_description
FROM         mailing_schedule s INNER JOIN
                      mailing_type ON s.mailing_type_id = mailing_type.mailing_type_id LEFT OUTER JOIN
                      mailing_name n ON s.mailing_schedule_id = n.mailing_schedule_id
WHERE s.mailing_schedule_id = '$mailing_schedule_id'");
$mailing_schedule_timestamp = mysql_result($result, 0, 'mailing_timestamp') + 0; // adding 0 gets rid of .0
$mailing_schedule_date = date('Y-m-d', $mailing_schedule_timestamp);

if($_POST) {
	include('ns_process_segment.inc.ctp');
	$filter_product_name = $_POST['filter_product_name'];
	$filter_score = $_POST['filter_score'];
}elseif($_GET['mailing_schedule_id']) {
	$mailing_schedule_id = $_GET['mailing_schedule_id'];
	$filter_product_name = @$_GET['filter_product_name'];
	$filter_score = @$_GET['filter_score'];
}




$query = "SELECT p.clientId
					FROM mailing_segment_product p
					INNER JOIN mailing_segment s ON p.mailing_segment_id = s.mailing_segment_id
					WHERE s.mailing_schedule_id = '$mailing_schedule_id'";
$result = mysql_query($query);

$products_scheduled = array();
while($row = mysql_fetch_assoc($result)) {
	$products_scheduled[] = $row['clientId'];
}
$params = array();
$params['filter_product_name'] = !empty($filter_product_name) ? $filter_product_name : null;
$params['filter_score'] = !empty($filter_score) ? $filter_score : null;
$params['mailing_schedule_date'] = $mailing_schedule_date;

//TODO: The following block of code is in a sproc llsp_sel_mailing_products, use mysqli to call the sproc
mysql_query("CREATE TEMPORARY TABLE temp_products(
clientId INT, 
product_name VARCHAR(255), 
LOA_start_date DATETIME, 
LOA_end_date DATETIME, 
LOA_mail_shots INT, 
mail_shots_remaining DECIMAL(6,3))");
$sql = "INSERT INTO temp_products
SELECT MAX(p.clientId) AS clientId, p.name AS product_name, l.startDate AS LOA_start_date, l.endDate AS LOA_end_date, l.numEmailInclusions AS LOA_mail_shots,
(l.numEmailInclusions - SUM(ct.mailing_segment_credit_type_value))
AS mailing_shots_remaining
FROM client p 
LEFT JOIN loa l ON p.clientId = l.clientId
LEFT JOIN mailing_segment_credit c ON l.loaId = c.LOA_id
LEFT JOIN mailing_segment_credit_type ct ON c.mailing_segment_credit_type_id = ct.mailing_segment_credit_type_id
WHERE (l.inactive = 0) AND '$mailing_schedule_date' BETWEEN l.startDate AND l.endDate AND (l.numEmailInclusions > 0)
GROUP BY p.name, l.startDate, l.endDate, l.numEmailInclusions";
mysql_query($sql);
echo $sql;
if(!empty($filter_product_name)):
	$filter_product_name = $filter_product_name.'%';
	$result = mysql_query("SELECT *, (mail_shots_remaining / (DATEDIFF(NOW(), LOA_end_date)+1) * 100) AS mailing_score
		FROM temp_products
		WHERE product_name LIKE '$filter_product_name'
		ORDER BY mailing_score DESC
		LIMIT 50;");
elseif (!empty($filter_score)):
	$result = mysql_query("SELECT *, (mail_shots_remaining / (DATEDIFF(NOW(), LOA_end_date)+1)) * 100 AS mailing_score
		FROM temp_products
		WHERE mail_shots_remaining / DATEDIFF(NOW(), LOA_end_date) * 100 <= '$filter_score'
		ORDER BY mailing_score DESC
		LIMIT 50;");
else:
	$result = mysql_query("SELECT *, (mail_shots_remaining / (DATEDIFF(NOW(), LOA_end_date)+1)) * 100 AS mailing_score
		FROM temp_products
		ORDER BY mailing_score DESC
		LIMIT 50;");
endif;
//end TODO

while($row = mysql_fetch_assoc($result)) {
	$products[$row['clientId']] = $row;
	$products[$row['clientId']]['products_scheduled'] = (in_array($row['clientId'], $products_scheduled)) ? 1 : 0;
}

foreach($products as $clientId=>$product_row) {
	$result = mysql_query("SELECT Client.clientId, Loa.loaId, Loa.startDate, Loa.endDate FROM client AS Client JOIN loa AS Loa ON (Client.clientId = Loa.clientId)
		WHERE '$mailing_schedule_date' BETWEEN Loa.startDate AND Loa.endDate AND Client.clientId = '$clientId'");
	$loa_id = mysql_result($result, 0, 'loaId');

	$result = mysql_query("SELECT p.clientId as clientId, p.name as product_name, ap.packageName as auction_name, ap.openingBid as auction_minimum_bid, ap.retailValue as auction_retail, MAX(ap.validityEndDate) AS auction_validity_end, ap.offerTypeId as auction_type_id, MAX(ap.packageId) AS auction_id,
		MAX(l.startDate) AS loa_start_date, MAX(l.endDate) AS loa_end_date, MAX(a.endDate) AS auction_date_close, offerType.offerTypeName
	FROM         client p INNER JOIN
						  clientLoaPackageRel clp ON (clp.clientId = p.clientId) INNER JOIN
	                      schedulingMaster ap ON (ap.packageId = clp.packageId) INNER JOIN
	                      schedulingInstance a ON ap.schedulingMasterId = a.schedulingMasterId INNER JOIN loa l ON clp.loaId = l.loaId
						  LEFT JOIN offerType ON (offerType.offerTypeId = ap.offerTypeId)
	WHERE     (a.startDate >= l.startDate) AND (a.endDate <= l.endDate)
	AND clp.loaId = '$loa_id' AND clp.clientId = '$clientId'
	GROUP BY p.clientId, p.name, ap.packageName, ap.openingBid, ap.retailValue, ap.packageId
	HAVING      (p.clientId = '$clientId')");
	
	while($row = mysql_fetch_assoc($result)) {
		$offer_info = $row['auction_id'] . ' - ' . ($row['offerTypeName']) . ' - $' . $row['auction_retail'] . ' - ' . date('M', strtotime($row['auction_validity_end'])) . ' - ' . $row['auction_date_close'];
		
		$products[$clientId]['packages'][$row['auction_id']] = $offer_info;
	}
}

/* Build arrays for drop downs */
// Segment types
$result= mysql_query('SELECT mailing_segment_type_id,mailing_segment_type_description AS mailing_segment_type_desc FROM mailing_segment_type');
while($row = mysql_fetch_assoc($result)) {
	$segment_types_select[$row['mailing_segment_type_id']] = $row['mailing_segment_type_desc'];
}
// Segment positions
$result = mysql_query('SELECT mailing_segment_position_id,mailing_segment_position_description AS mailing_segment_position_desc FROM mailing_segment_position');
while($row = mysql_fetch_assoc($result)) {
	$segment_positions_select[$row['mailing_segment_position_id']] = $row['mailing_segment_position_desc'];
}
// Credit types
$result = mysql_query('SELECT mailing_segment_credit_type_id AS credit_type_id, mailing_segment_credit_type_value AS credit_type_value FROM mailing_segment_credit_type');
while($row = mysql_fetch_assoc($result)) {
	$segment_product_types_select[$row['credit_type_id']] = $row['credit_type_value'];
}

?>

<a href="ns_main.php?pg=ns_maint&mailing_schedule_id=<?=$mailing_schedule_id?>">Mailing schedule</a> | <a href="../Auction/AuctionScheduler.phtml" target="_blank">Auction scheduler</a>
<br><br>

<table cellspacing="0" cellpadding="2" border="1">
	<tr class="table_header_row">
	<form action="ns_main" method="get">
		<input type="hidden" name="pg" value="ns_product_listing">
		<input type="hidden" name="mailing_schedule_id" value="<?=$mailing_schedule_id?>">
		<td width="200"><input type="text" name="filter_product_name" value="<?=@$filter_product_name?>" class="input100p"></td>
		<td width="60">&nbsp;</td>
		<td width="80">&nbsp;</td>
		<td width="40"><input type="text" name="filter_score" value="<?=@$filter_score?>" class="input100p"></td>
		<td width="160"><input type="submit" value="Filter by"></td>
		<td width="150" colspan="6">&nbsp;</td>
	</form>
	</tr>
	<tr class="table_header_row">
		<td>Product name</td>
		<td>Emails total</td>
		<td>Emails remaining</td>
		<td>Score</td>
		<td>LOA end date</td>
		<td>Position</td>
		<td>Promo type</td>
		<td>Credit</td>
		<td>Offers</td>
		<td>&nbsp;</td>
	</tr>
			
<? foreach($products as $clientId=>$product_row) { ?>
<form name="mailing_segment_form_<?=$clientId?>" action="ns_main" method="post">
<input type="hidden" name="mailing_schedule_id" value="<?=@$mailing_schedule_id?>">
<input type="hidden" name="mailing_segment_sort_order" value="<?=@$mailing_segment_sort_order?>">
<input type="hidden" name="pg" value="<?=$pg?>">
<input type="hidden" id="clientId" name="clientId" required='' label="Product id" value="<?=$clientId?>">

<input type="hidden" name="filter_product_name" value="<?=@$filter_product_name?>">
<input type="hidden" name="filter_score" value="<?=@$filter_score?>">

	<tr bgcolor="<?=($product_row['products_scheduled']) ? '#66FF33' : '#FFFFFF'?>">
		<td><?=$product_row['product_name']?></td>
		<td><?=$product_row['LOA_mail_shots']?></td>
		<td><?=$product_row['mail_shots_remaining'] + 0?></td>
		<td><?=round($product_row['mailing_score'])?></td>
		<td><?=$product_row['LOA_end_date']?></td>
		<td>
			<select name="mailing_segment_position_id" required='1' label="Segment position" style="width: 100px;">
				<option value="">Segment postion</option>
				<?=html_options(@$segment_positions_select)?>
			</select>
		</td>
		<td>
			<select name="mailing_segment_type_id" required='1' label="Segment type" style="width: 100px;" onChange="set_field_enable(this.form, this.value)">
				<?=html_options(@$segment_types_select)?>
			</select>
		</td>
		<td>
			<select id="mailing_segment_credit_type_id" name="mailing_segment_credit_type_id" required='' label="Credit" class="select_numeric">
				<option value="">Credit</option>
				<?=html_options(@$segment_product_types_select)?>
			</select>
		</td>
		<td>
			<select id="offer_id" name="offer_id" required='' label="Offer id">
				<option value=""><?=(count(@$products[$clientId]['packages'])) ? 'Offers' : 'None'?></option>
				<?=html_options(@$products[$clientId]['packages'])?>
			</select>
		</td>
		<td><input type="button" onClick="validate_form(this.form)" value="Add"></td>	
	</tr>
	<tr>
		<td colspan="11"></td>
	</tr>
</form>

<? } ?>

</table>