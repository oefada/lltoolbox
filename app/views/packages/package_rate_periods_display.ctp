<h3>Package Rate Period Preview</h3>
<div class="mB mT">
	<table cellpadding="2" cellspacing="0">
	
	<?php
	echo '<tr>';
	echo '<th>Range</th>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<th>' . $v['rangeStart'] . '<br />to<br />' . $v['rangeEnd'] . "</th>\n";
	}
	echo '</tr>';
	?>	

	<?php
	foreach ($packageRatePeriods['IncludedItems'] as $a => $b) {
		echo '<tr>';
		echo '<td>' . $b['itemName'] . '</td>';
		foreach($b['PackageRatePeriod'] as $itemRatePeriod) {
			$currencyCode = $currencyCodes[$b['currencyId']];
			echo '<td>' . $number->currency($itemRatePeriod['ratePeriodPrice'], $currencyCode) . '</td>';
		}
		echo '</tr>';
	}
	?>

	<?php
	echo '<tr>';
	echo '<td>Overall Price</td>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<td><strong>' . $number->currency($v['rangeSum'], $currencyCode) . '</strong></td>';
	}
	echo '</tr>';
	?>
	<?php
	echo '<tr>';
	echo '<td style="border-top: 1px solid #ccc">Exchange Rate Today (1.2)</td>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<td style="border-top: 1px solid #ccc"><strong>' . $number->currency($v['rangeSum']*1.2, 'USD') . '</strong></td>';
	}
	echo '</tr>';
	?>
	<?php
	echo '<tr>';
	echo '<td>7 Day Average (1.5)</td>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<td><strong>' . $number->currency($v['rangeSum']*1.5, 'USD') . '</strong></td>';
	}
	echo '</tr>';
	?>
	<?php
	echo '<tr>';
	echo '<td>28 Day Average (0.8)</td>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<td><strong>' . $number->currency($v['rangeSum']*0.8, 'USD') . '</strong></td>';
	}
	echo '</tr>';
	?>
	
	</table>
</div>
