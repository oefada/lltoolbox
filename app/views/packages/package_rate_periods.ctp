<h3><?= (@$packageRatePreview) ? 'New ': 'Saved '?>Package Rate Periods<? if (@$packageRatePreview) echo ' Preview'?></h3>
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
	$i = 0;
	foreach ($packageRatePeriods['IncludedItems'] as $a => $b) {
		$class = '';
		if($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
		echo "<tr{$class}>";
		echo '<td>' . $b['itemName'] . '</td>';
		foreach($b['PackageRatePeriod'] as $itemRatePeriod) {
			$currencyCode = $currencyCodes[$b['currencyId']];
			echo '<td>' . $number->currency($itemRatePeriod['ratePeriodPrice'], $currencyCode).' x '.$itemRatePeriod['quantity'].' = <em>'.$number->currency($itemRatePeriod['ratePeriodPrice']*$itemRatePeriod['quantity'], $currencyCode). '</em></td>';
		}
		echo '</tr>';
	}
	?>

	<?php
	echo '<tr class="lastRow">';
	echo '<td>Overall Price</td>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<td><strong>' . $number->currency($v['rangeSum'], $currencyCode) . '</strong></td>';
	}
	echo '</tr>';
	?>
	
	<?php
	//we only need to show exchange rates if the currency is not USD
	if($currencyCode != 'USD'):
	?>
	<?php
	echo '<tr class="lastRowLight">';
	echo '<td style="border-top: 1px solid #ccc">Exchange Rate Today (1.2)</td>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<td style="border-top: 1px solid #ccc"><strong>' . $number->currency($v['rangeSum']*1.2, 'USD') . '</strong></td>';
	}
	echo '</tr>';
	?>
	<?php
	echo '<tr class="lastRowLight">';
	echo '<td>7 Day Average (1.5)</td>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<td><strong>' . $number->currency($v['rangeSum']*1.5, 'USD') . '</strong></td>';
	}
	echo '</tr>';
	?>
	<?php
	echo '<tr class="lastRowLight">';
	echo '<td>28 Day Average (0.8)</td>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<td><strong>' . $number->currency($v['rangeSum']*0.8, 'USD') . '</strong></td>';
	}
	echo '</tr>';
	?>
	<?php endif; //end currencyCode check?>
	</table>
</div>