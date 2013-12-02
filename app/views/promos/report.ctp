<div class="promos index">
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Promotion Name</th><th>Promotion Code</th><th>Percent Off</th><th>Amount Off</th><th>Date Start</th><th>Date End</th><th># of Codes</th>
</tr>
<tr>
	<td><?php echo $promo['Promo']['promoName']; ?></td>
    <td><? if (count($promo['PromoCode']) == 1) echo $promo['PromoCode'][0]['promoCode']; else echo '-'; ?></td>
    <td><?php echo $promo['Promo']['percentOff']; ?></td>
    <td><?php echo $promo['Promo']['amountOff']; ?></td>
    <td><?php echo $promo['Promo']['startDate']; ?></td>
    <td><?php echo $promo['Promo']['endDate']; ?></td>
    <td><? echo count($promo['PromoCode']); ?></td>
</tr>
</table>
</div>
<?

if ($num_packages > 0) {
	echo "<p><b>Number of Uses:</b> $num_packages Packages | $num_auctions Auctions | $num_buynows Fixed Price Requests</p>";
	echo "<p><b>Average Sale Price:</b> $" . number_format($total_sales / $num_packages, 2) . "</p>";
	echo "<p><b>Total Package Sale:</b> $" . number_format($total_sales, 2) . "</p>";
}

?>
