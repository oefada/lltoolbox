<style>
table {
	border: 1px solid black;
	border-collapse: collapse;
}
th {
	background: #ccc;
	border: 1px solid black;
}
td {
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	padding: 3px 2px 3px 2px;
	font-size: 10pt;
}
</style>

<h1>Weekly Scorecard</h1>
<br />
<h2>1. Total</h2>
<table>
	<tr>
		<th>week number</th>
		<th>week beginning Sunday</th>
		<th>Packages Sold</th>
		<th>YoY</th>
		<th>Revenue Collected</th>
		<th>YoY</th>
		<th>ASP</th>
		<th>YoY</th>
	</tr>
<?php foreach($tot as $row):
$row = $row['data'];

?>
<tr>
	<?php for($i = 1; $i <= 8; $i++):
		
		$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);

	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$row['revenuetarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=round(($row['col5']/$row['revenuetarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$tot[0][0]['quarterRevenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=round(($totals['col5']/$tot[0][0]['quarterRevenueTarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right">QTR</td>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$qtr[0][0]['revenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
<p style="page-break-before: always"> </p>
<h2>2. Auctions</h2>
<table>
	<tr>
		<th>week number</th>
		<th>week beginning Sunday</th>
		<th>Revenue (p)</th>
		<th>YoY</th>
		<th>Listings</th>
		<th>YoY</th>
		<th>Conversion Rate</th>
		<th>YoY</th>
		<th>Successful Listings</th>
		<th>YoY</th>
		<th>Total Tickets (p)</th>
		<th>YoY</th>
		<th>Revenue Collected</th>
		<th>YoY</th>
		<th>% Retail</th>
		<th>YoY</th>
		<th>Collection Rate</th>
		<th>YoY</th>
		<th>Tickets Collected</th>
		<th>YoY</th>
		<th>ASP</th>
		<th>YoY</th>
	</tr>
<?php 
$totals = array();
foreach($auc as $row):
$row = $row['data'];
?>
<tr>
	<?php for($i = 1; $i <= 22; $i++): 
			$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=22>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 22; $i++): ?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td><?=$row['revenuetarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td><?=round(($row['col5']/$row['revenuetarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=22>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 22; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td><?=$auc[0][0]['quarterRevenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td><?=round(($totals['col3']/$auc[0][0]['quarterRevenueTarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=22>&nbsp;</td></tr>
<tr>
	<td style="text-align:right">QTR</td>
	<td style="text-align:right">target</td>
	<td><?=$aucqtr[0][0]['revenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
<p style="page-break-before: always"> </p>
<? for($k=0; $k < 3; $k++):
switch($k) {
	case 0:
		$rows = $fp;
		$title = '';
		$qtr = $fpqtr;
		break;
	case 1:
		$rows = $fpSponsored;
		$title = ' Sponsor';
		$qtr = $fpSponsoredQtr;
		break;
	case 2:
		$rows = $fpWholesale;
		$title = ' Wholesale';
		$qtr = $fpWholesaleQtr;
		break;
}
?>
<h2><?=$k+3?>. Fixed Price<?=$title?></h2>
<table>
	<tr>
		<th>week number</th>
		<th>week beginning</th>
		<th>Listings</th>
		<th>YoY</th>
		<th>number requests</th>
		<th>YoY</th>
		<th>number collected</th>
		<th>YoY</th>
		<th>collection rate</th>
		<th>YoY</th>
		<th>revenue collected</th>
		<th>YoY</th>
		<th>average sale price</th>
		<th>YoY</th>
	</tr>
<?php
$totals = array();
foreach($rows as $row):
$row = $row['data'];
?>
<tr>
	<?php for($i = 1; $i <= 14; $i++):
		$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=14>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 14; $i++): ?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$row['revenuetarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=round(($row['col11']/$row['revenuetarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=14>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 14; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$rows[0][0]['quarterRevenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=round(($totals['col11']/$rows[0][0]['quarterRevenueTarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=14>&nbsp;</td></tr>
<tr>
	<td style="text-align:right">QTR</td>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$qtr[0][0]['revenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
<p style="page-break-before: always"> </p>
<? endfor; ?>
<h2>6. Cruises</h2>
<table>
	<tr>
		<th>week number</th>
		<th>week beginning</th>
		<th>Offers</th>
		<th>Tickets Collected</th>
		<th>YoY</th>
		<th>Revenue Collected</th>
		<th>YoY</th>
		<th>ASP</th>
		<th>YoY</th>
	</tr>
<?php
$totals = array();
foreach($cruises as $row):
$row = $row['data'];
?>
<tr>
	<?php for($i = 1; $i <= 9; $i++): 
	$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=9>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$row['revenuetarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=round(($row['col6']/$row['revenuetarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=9>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 9; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$cruises[0][0]['quarterRevenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=round(($totals['col6']/$cruises[0][0]['quarterRevenueTarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=9>&nbsp;</td></tr>
<tr>
	<td style="text-align:right">QTR</td>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$cruisesQtr[0][0]['revenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
<p style="page-break-before: always"> </p>
<h2>7. Sponsorship</h2>
<table>
	<tr>
		<th>week number</th>
		<th>week beginning</th>
		<th>Tickets Collected</th>
		<th>YoY</th>
		<th>Revenue Collected</th>
		<th>YoY</th>
		<th>ASP</th>
		<th>YoY</th>
	</tr>
<?php
$totals = array();
foreach($sponsorship as $row):
$row = $row['data'];
?>
<tr>
	<?php for($i = 1; $i <= 8; $i++): 
	$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++): ?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$row['revenuetarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=round(($row['col5']/$row['revenuetarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$sponsorship[0][0]['quarterRevenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=round(($totals['col5']/$sponsorship[0][0]['quarterRevenueTarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right">QTR</td>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$sponsorshipQtr[0][0]['revenueTarget']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
<p style="page-break-before: always"> </p>
<h2>8. Buyers</h2>
<table>
	<tr>
		<th>week number</th>
		<th>week beginning</th>
		<th>New</th>
		<th>YoY</th>
		<th>Returning</th>
		<th>YoY</th>
		<th>Total</th>
		<th>YoY</th>
	</tr>
<?php 
$totals = array();
foreach($buyers as $row):
$row = $row['data'];
?>
<tr>
	<?php for($i = 1; $i <= 8; $i++):
	$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++): ?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td><?=$row['newBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$row['returningBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$row['totalBuyerTarget']?></td
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td><?=round(($row['col3']/$row['newBuyerTarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td><?=round(($row['col5']/$row['returningBuyerTarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td><?=round(($row['col7']/$row['totalBuyerTarget']-1)*100)?></td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=3>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<tr>
	<td style="text-align:right">target</td>
	<td><?=$buyers[0][0]['quarterNewBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$buyers[0][0]['quarterReturningBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$buyers[0][0]['quarterTotalBuyerTarget']?></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">variance</td>
	<td><?=round(($totals['col3']/$buyers[0][0]['quarterNewBuyerTarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td><?=round(($totals['col5']/$buyers[0][0]['quarterReturningBuyerTarget']-1)*100)?></td>
	<td>&nbsp;</td>
	<td><?=round(($totals['col7']/$buyers[0][0]['quarterTotalBuyerTarget']-1)*100)?></td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right">QTR</td>
	<td style="text-align:right">target</td>
	<td><?=$buyerQtr[0][0]['quarterNewBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$buyerQtr[0][0]['quarterReturningBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$buyerQtr[0][0]['quarterTotalBuyerTarget']?></td>
	<td>&nbsp;</td>
</tr>
</table>