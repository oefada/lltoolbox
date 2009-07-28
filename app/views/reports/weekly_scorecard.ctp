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
		
		$totals['col'.$i] = (!isset($totals['col'.$i]) ? 0 : $totals['col'.$i] + $row['col'.$i]);

	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr>
	<?php for($i = 1; $i <= 8; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
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
			$totals['col'.$i] = (!isset($totals['col'.$i]) ? 0 : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr>
	<?php for($i = 1; $i <= 22; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
</table>
<p style="page-break-before: always"> </p>
<h2>3. Fixed Price</h2>
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
foreach($fp as $row):
$row = $row['data'];
?>
<tr>
	<?php for($i = 1; $i <= 14; $i++):
		$totals['col'.$i] = (!isset($totals['col'.$i]) ? 0 : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr>
	<?php for($i = 1; $i <= 14; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
</table><p style="page-break-before: always"> </p>
<h2>4. Fixed Price Sponsor</h2>
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
foreach($fpSponsored as $row):
$row = $row['data'];
?>
<tr>
	<?php for($i = 1; $i <= 14; $i++):
	$totals['col'.$i] = (!isset($totals['col'.$i]) ? 0 : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr>
	<?php for($i = 1; $i <= 14; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
</table>
<p style="page-break-before: always"> </p>
<h2>5. Fixed Price Wholesale</h2>
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
foreach($fpWholesale as $row):
$row = $row['data'];
?>
<tr>
	<?php for($i = 1; $i <= 14; $i++):
	$totals['col'.$i] = (!isset($totals['col'.$i]) ? 0 : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr>
	<?php for($i = 1; $i <= 14; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
</table>
<p style="page-break-before: always"> </p>
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
	$totals['col'.$i] = (!isset($totals['col'.$i]) ? 0 : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr>
	<?php for($i = 1; $i <= 9; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
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
	$totals['col'.$i] = (!isset($totals['col'.$i]) ? 0 : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr>
	<?php for($i = 1; $i <= 8; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
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
	$totals['col'.$i] = (!isset($totals['col'.$i]) ? 0 : $totals['col'.$i] + $row['col'.$i]);
	?>
	<td><?=$row['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
<?php endforeach; ?>
<tr>
	<?php for($i = 1; $i <= 8; $i++):
	
		$skip = array(1,2);
	?>
	<td><?=in_array($i, $skip) ? '&nbsp;' : $totals['col'.$i]?></td>	
	<?php endfor; ?>
</tr>
</table>