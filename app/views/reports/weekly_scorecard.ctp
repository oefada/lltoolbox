<style>
table {
	border: 1px solid black;
	border-collapse: collapse;
	text-align: center;
	margin-bottom: 80px;
}
th {
	background: #ccc;
	border: 1px solid black;
	padding: 0 5px;
	text-align: center;
}
td {
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	padding: 3px 4px 3px 4px;
	font-size: 10pt;
}
#auctions td {
	border-right: 1px solid #ccc;
	border-bottom: 1px solid #ccc;
	padding: 3px 2px 3px 2px;
	font-size: 10pt;
}
td.highlight {
	background-color: #f5f2e2;
}

</style>
<?
function &number() {
    static $obj;

    if (!isset($obj)) {
        // Assign the object to the static variable
        $obj = new NumberHelper;
    }
    return $obj;
}
/* echo cannot be used as a variable function because it's a language construct
   so we need a wrapper function */
function efunc($string) {
	echo($string);
}

function percentage($string) {
	echo number()->toPercentage($string*100, 1);
}

function currency($string) {
	echo number()->currency($string, 'USD', array('places' => 0));
}
//set the default function to use to output stuff in this report
$echo = 'efunc'; ?>
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
		if (in_array($i, array(4,6,8))) {
			$echo = 'percentage';
		} else if(in_array($i, array(5,7))) {
			$echo = 'currency';
		} else {
			$echo = 'efunc';
		}
		
		//overwrite ASP
		if ($i == 7) {
			$totals['col'.$i] = $totals['col5']/$totals['col3'];
		} else {
			$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
		}
		
	?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=8>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):
	if (in_array($i, array(4,6,8))) {
		$echo = 'percentage';
	} else if(in_array($i, array(5,7))) {
		$echo = 'currency';
	} else {
		$echo = 'efunc';
	}
	?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($row['revenuetarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?percentage(($row['col5']/$row['revenuetarget']-1))?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=4>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):
		if (in_array($i, array(4,6,8))) {
			$echo = 'percentage';
		} else if(in_array($i, array(5,7))) {
			$echo = 'currency';
		} else {
			$echo = 'efunc';
		}
		$skip = array(1,2,4,6,8);
	?>
	<td><?in_array($i, $skip) ? print('&nbsp;') : $echo($totals['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($tot[0][0]['quarterRevenueTarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?percentage(($totals['col5']/$tot[0][0]['quarterRevenueTarget']-1))?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">last year</td>
	<td><?=$totLastYear['packagesSoldPrevious']?></td>
	<td>&nbsp;</td>
	<td><?currency($totLastYear['revenueCollectedPrevious'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=2>QTR</td>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($qtr[0][0]['revenueTarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">last year</td>
	<td><?=$totLastYear['qtr_packagesSoldPrevious']?></td>
	<td>&nbsp;</td>
	<td><?currency($totLastYear['qtr_revenueCollectedPrevious'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
</table>
<p style="page-break-before: always"> </p>
<h2>2. Auctions</h2>
<table class="auctions">
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
		if (($i % 2 == 0 && $i > 3) || in_array($i, array(7, 15, 17))) {
			$echo = 'percentage';
		} else if(in_array($i, array(3,13,21))) {
			$echo = 'currency';
		} else {
			$echo = 'efunc';
		}
		
		$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td<?=($i == 13 || $i == 19) ? ' class="highlight"': ''?>><?$echo($row['col'.$i])?></td>	
	<?php endfor;
	
	$totals['col21'] = $totals['col13']/$totals['col19'];
	$totals['col7'] = $totals['col9']/$totals['col5'];
	$totals['col17'] = $totals['col19']/$totals['col11'];
	?>
</tr>
<?php endforeach; ?>
<tr><td colspan=22>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 22; $i++):
		if (($i % 2 == 0 && $i > 3) || in_array($i, array(7, 15, 17))) {
			$echo = 'percentage';
		} else if(in_array($i, array(3,13,21))) {
			$echo = 'currency';
		} else {
			$echo = 'efunc';
		}
	?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
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
	<td><?currency($row['revenuetarget'])?></td>
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
<tr class="altrow">
	<td style="text-align:right">variance</td>
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
	<td><?percentage(($row['col13']/$row['revenuetarget']-1))?></td>
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
<tr class="altrow">
	<td style="text-align:right" rowspan=4>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 22; $i++):
		if (($i % 2 == 0 && $i > 3) || in_array($i, array(7, 15, 17))) {
			$echo = 'percentage';
		} else if(in_array($i, array(3,13,21))) {
			$echo = 'currency';
		} else {
			$echo = 'efunc';
		}
		$skip = array(4,6,8,10,12,14,15,16,18,20,22);
	?>
	<td><?in_array($i, $skip) ? print('&nbsp;') : $echo($totals['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
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
	<td><?currency($auc[0][0]['quarterRevenueTarget'])?></td>
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
<tr class="altrow">
	<td style="text-align:right">variance</td>
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
	<td><?percentage(($totals['col13']/$auc[0][0]['quarterRevenueTarget']-1))?></td>
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
<tr class="altrow">
	<td style="text-align:right">last year</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$aucLastYear['auctionsListedPrevious']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$aucLastYear['successfulAuctionsPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$aucLastYear['auctionTicketsPotentialPrevious']?></td>
	<td>&nbsp;</td>
	<td><?currency($aucLastYear['auctionRevenueCollectedPrevious'])?></td>
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
	<td style="text-align:right" rowspan=2>QTR</td>
	<td style="text-align:right">target</td>
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
	<td class="highlight"><?currency($aucqtr[0][0]['revenueTarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="highlight">&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">last year</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$aucLastYear['qtr_auctionsListedPrevious']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?=$aucLastYear['qtr_successfulAuctionsPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$aucLastYear['qtr_auctionTicketsPotentialPrevious']?></td>
	<td>&nbsp;</td>
	<td class="highlight"><?currency($aucLastYear['qtr_auctionRevenueCollectedPrevious'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td class="highlight">&nbsp;</td>
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
		$title = ' (total)';
		$qtr = $fpqtr;
		$lastyear = $fpLastYear;
		break;
	case 1:
		$rows = $fpSponsored;
		$title = ' Sponsor';
		$qtr = $fpSponsoredQtr;
		$lastyear = $fpSponsoredLastYear;
		break;
	case 2:
		$rows = $fpWholesale;
		$title = ' Wholesale';
		$qtr = $fpWholesaleQtr;
		$lastyear = $fpWholesaleLastYear;
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
		if (($i % 2 == 0 && $i > 3) || $i == 9) {
			$echo = 'percentage';
		} else if(in_array($i, array(11,13))) {
			$echo = 'currency';
		} else {
			$echo = 'efunc';
		}
		if ($i == 13) {
			$totals['col'.$i] = $totals['col11']/$totals['col7'];
		} elseif ($i == 9) {
			$totals['col'.$i] = $totals['col7']/$totals['col5'];
		} else {
			$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
		}
	?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=14>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 14; $i++):
	if (($i % 2 == 0 && $i > 3) || $i == 9) {
		$echo = 'percentage';
	} else if(in_array($i, array(11,13))) {
		$echo = 'currency';
	} else {
		$echo = 'efunc';
	}
	?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($row['revenuetarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?percentage(($row['col11']/$row['revenuetarget']-1))?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=14>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=4>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 14; $i++):
		if (($i % 2 == 0 && $i > 3) || $i == 9) {
			$echo = 'percentage';
		} else if(in_array($i, array(11,13))) {
			$echo = 'currency';
		} else {
			$echo = 'efunc';
		}

		if ($i == 13) {
			$totals['col'.$i] = $totals['col11']/$totals['col7'];
		} elseif ($i == 9) {
			$totals['col'.$i] = $totals['col7']/$totals['col5'];
		} else {
			$totals['col'.$i] = (!isset($totals['col'.$i]) ? $totals['col'.$i] : $totals['col'.$i] + $totals['col'.$i]);
		}
		$skip = array(4,6,8,10,12,14);
	?>
	<td><?in_array($i, $skip) ? print('&nbsp;') : $echo($totals['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($rows[0][0]['quarterRevenueTarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?percentage(($totals['col11']/$rows[0][0]['quarterRevenueTarget']-1))?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">last year</td>
	<td><?=$lastyear['buyNowOffersPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$lastyear['numberRequestsPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$lastyear['packagesSoldPrevious']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($lastyear['revenueCollectedPrevious'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=14>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=2>QTR</td>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($qtr[0][0]['revenueTarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">last year</td>
	<td><?=$lastyear['qtr_buyNowOffersPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$lastyear['qtr_numberRequestsPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$lastyear['qtr_packagesSoldPrevious']?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($lastyear['qtr_revenueCollectedPrevious'])?></td>
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
	if (($i % 2 != 0 && $i > 3) || $i == 9) {
		$echo = 'percentage';
	} else if(in_array($i, array(6,8))) {
		$echo = 'currency';
	} else {
		$echo = 'efunc';
	}

	if ($i == 8) {
		$totals['col'.$i] = $totals['col6']/$totals['col4'];
	} else {
		$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
	}
	?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=9>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 9; $i++):
		if (($i % 2 != 0 && $i > 3) || $i == 9) {
			$echo = 'percentage';
		} else if(in_array($i, array(6,8))) {
			$echo = 'currency';
		} else {
			$echo = 'efunc';
		}
		?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($row['revenuetarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?percentage(@($row['col6']/$row['revenuetarget']-1))?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=9>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=4>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 9; $i++):
	if (($i % 2 != 0 && $i > 3) || $i == 9) {
		$echo = 'percentage';
	} else if(in_array($i, array(6,8))) {
		$echo = 'currency';
	} else {
		$echo = 'efunc';
	}
		$skip = array(5,7,9);
	?>
	<td><?in_array($i, $skip) ? print('&nbsp;') : $echo($totals['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($cruises[0][0]['quarterRevenueTarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?percentage(@($totals['col6']/$cruises[0][0]['quarterRevenueTarget']-1))?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">last year</td>
	<td><?=$cruisesLastYear['numberCruiseOffersPrevious']?></td>
	<td><?=$cruisesLastYear['packagesSoldPrevious']?></td>
	<td>&nbsp;</td>
	<td><?currency($cruisesLastYear['revenueCollectedPrevious'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=9>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=2>QTR</td>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($cruisesQtr[0][0]['revenueTarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">last year</td>
	<td><?=$cruisesLastYear['qtr_numberCruiseOffersPrevious']?></td>
	<td><?=$cruisesLastYear['qtr_packagesSoldPrevious']?></td>
	<td>&nbsp;</td>
	<td><?currency($cruisesLastYear['qtr_revenueCollectedPrevious'])?></td>
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
	if (($i % 2 == 0 && $i > 3)) {
		$echo = 'percentage';
	} else if(in_array($i, array(5,7))) {
		$echo = 'currency';
	} else {
		$echo = 'efunc';
	}
	if ($i == 7) {
		$totals['col'.$i] = $totals['col5']/$totals['col3'];
	} else {
		$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
	}
	?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=8>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):
	if (($i % 2 == 0 && $i > 3)) {
		$echo = 'percentage';
	} else if(in_array($i, array(5,7))) {
		$echo = 'currency';
	} else {
		$echo = 'efunc';
	}?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($row['revenuetarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?percentage(($row['col5']/$row['revenuetarget']-1))?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=4>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):
		if (($i % 2 == 0 && $i > 3)) {
			$echo = 'percentage';
		} else if(in_array($i, array(5,7))) {
			$echo = 'currency';
		} else {
			$echo = 'efunc';
		}
		$skip = array(4,6,8);
	?>
	<td><?in_array($i, $skip) ? print('&nbsp;') : $echo($totals['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($sponsorship[0][0]['quarterRevenueTarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?percentage(($totals['col5']/$sponsorship[0][0]['quarterRevenueTarget']-1))?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">last year</td>
	<td><?=$sponsorshipLastYear['packagesSoldPrevious']?></td>
	<td>&nbsp;</td>
	<td><?currency($sponsorshipLastYear['revenueCollectedPrevious'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=2>QTR</td>
	<td style="text-align:right">target</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td><?currency($sponsorshipQtr[0][0]['revenueTarget'])?></td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">last year</td>
	<td><?=$sponsorshipLastYear['qtr_packagesSoldPrevious']?></td>
	<td>&nbsp;</td>
	<td><?currency($sponsorshipLastYear['qtr_revenueCollectedPrevious'])?></td>
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
		if (($i % 2 == 0 && $i > 3)) {
			$echo = 'percentage';
		} else {
			$echo = 'efunc';
		}
	
	if ($i == 21) {
		$totals['col'.$i] = $totals['col19']/$totals['col3'];
	} else {
		$totals['col'.$i] = (!isset($totals['col'.$i]) ? $row['col'.$i] : $totals['col'.$i] + $row['col'.$i]);
	}
	?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr><td colspan=8>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=3>current week</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++): 
	if (($i % 2 == 0 && $i > 3)) {
		$echo = 'percentage';
	} else {
		$echo = 'efunc';
	}
	?>
	<td><?$echo($row['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td><?=$row['newBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$row['returningBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$row['totalBuyerTarget']?></td
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td><?percentage(($row['col3']/$row['newBuyerTarget']-1))?></td>
	<td>&nbsp;</td>
	<td><?percentage(($row['col5']/$row['returningBuyerTarget']-1))?></td>
	<td>&nbsp;</td>
	<td><?percentage(($row['col7']/$row['totalBuyerTarget']-1))?></td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr class="altrow">
	<td style="text-align:right" rowspan=4>QTD</td>
	<td style="text-align:right">actual</td>
	<?php for($i = 3; $i <= 8; $i++):
		if (($i % 2 == 0 && $i > 3)) {
			$echo = 'percentage';
		} else {
			$echo = 'efunc';
		}
		$skip = array(4,6,8);
	?>
	<td><?in_array($i, $skip) ? print('&nbsp;') : $echo($totals['col'.$i])?></td>	
	<?php endfor; ?>
</tr>
<tr class="altrow">
	<td style="text-align:right">target</td>
	<td><?=$buyers[0][0]['quarterNewBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$buyers[0][0]['quarterReturningBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$buyers[0][0]['quarterTotalBuyerTarget']?></td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">variance</td>
	<td><?percentage(($totals['col3']/$buyers[0][0]['quarterNewBuyerTarget']-1))?></td>
	<td>&nbsp;</td>
	<td><?percentage(($totals['col5']/$buyers[0][0]['quarterReturningBuyerTarget']-1))?></td>
	<td>&nbsp;</td>
	<td><?percentage(($totals['col7']/$buyers[0][0]['quarterTotalBuyerTarget']-1))?></td>
	<td>&nbsp;</td>
</tr>
<tr class="altrow">
	<td style="text-align:right">last year</td>
	<td><?=$buyersLastYear['newBuyerActivityPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$buyersLastYear['returningBuyerActivityPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$buyersLastYear['totalBuyerActivityPrevious']?></td>
	<td>&nbsp;</td>
</tr>
<tr><td colspan=8>&nbsp;</td></tr>
<tr>
	<td style="text-align:right" rowspan=2>QTR</td>
	<td style="text-align:right">target</td>
	<td><?=$buyerQtr[0][0]['quarterNewBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$buyerQtr[0][0]['quarterReturningBuyerTarget']?></td>
	<td>&nbsp;</td>
	<td><?=$buyerQtr[0][0]['quarterTotalBuyerTarget']?></td>
	<td>&nbsp;</td>
</tr>
<tr>
	<td style="text-align:right">last year</td>
	<td><?=$buyersLastYear['qtr_newBuyerActivityPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$buyersLastYear['qtr_returningBuyerActivityPrevious']?></td>
	<td>&nbsp;</td>
	<td><?=$buyersLastYear['qtr_totalBuyerActivityPrevious']?></td>
	<td>&nbsp;</td>
</tr>
</table>