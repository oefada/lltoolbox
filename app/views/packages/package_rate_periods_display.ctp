<h3>Package Rate Period Preview</h3>
<div class="mB mT">
	<table cellpadding="2" cellspacing="0">
	
	<?php
	echo '<tr>';
	echo '<th>Range</th>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<th>' . $v['startDate'] . '<br />to<br />' . $v['endDate'] . "</th>\n";
	}
	echo '</tr>';
	?>	

	<?php
	foreach ($loas as $a => $b) {
		echo '<tr>';
		echo '<td>' . $b['itemName'] . '</td>';
		for($i = 0; $i < count($packageRatePeriods); $i++){
			echo '<td>' . (isset($b['Periods'][$i]) ? $number->currency($b['Periods'][$i], $b['currencyCode']) : $b['itemBasePrice']) . '</td>';
		}
		echo '</tr>';
	}
	?>

	<?php
	echo '<tr>';
	echo '<td>Overall Price</td>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<td><strong>' . $number->currency($v['packageRatePeriodPrice'], $currencyCode) . '</strong></td>';
	}
	echo '</tr>';
	?>
	<?php
	echo '<tr>';
	echo '<td style="border-top: 1px solid #ccc">Exchange Rate Today (1.2)</td>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<td style="border-top: 1px solid #ccc"><strong>' . $number->currency($v['packageRatePeriodPrice']*1.2, 'USD') . '</strong></td>';
	}
	echo '</tr>';
	?>
	<?php
	echo '<tr>';
	echo '<td>7 Day Average (1.5)</td>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<td><strong>' . $number->currency($v['packageRatePeriodPrice']*1.5, 'USD') . '</strong></td>';
	}
	echo '</tr>';
	?>
	<?php
	echo '<tr>';
	echo '<td>28 Day Average (0.8)</td>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<td><strong>' . $number->currency($v['packageRatePeriodPrice']*0.8, 'USD') . '</strong></td>';
	}
	echo '</tr>';
	?>
	
	</table>
</div>
