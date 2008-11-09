<h3>Package Rate Periods</h3>
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
		foreach ($b['Periods'] as $index => $price) {
			echo '<td>$' . $price . '</td>';
		}
		echo '</tr>';
	}
	?>

	<?php
	echo '<tr>';
	echo '<td>Overall Price</td>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<td><strong>$' . $v['packageRatePeriodPrice'] . '</strong></td>';
	}
	echo '</tr>';
	?>
	
	</table>
</div>
