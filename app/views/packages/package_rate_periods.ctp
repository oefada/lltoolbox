<? if (isset($packageRatePeriods) && count($packageRatePeriods['Boundaries'])): ?>
<h3><?= (@$packageRatePreview) ? 'New ': 'Saved '?>Package Rate Periods<? if (@$packageRatePreview) echo ' Preview'?></h3>
<div class="mB mT">
	<table cellpadding="2" cellspacing="0">
	
	<?php
	echo '<tr>';
	echo '<th>Range</th>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<th>' . $html2->date($v['rangeStart']) . '<br />to<br />' . $html2->date($v['rangeEnd']) . "</th>\n";
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
		echo '<td rowspan="2" style="vertical-align: middle">' . $b['itemName'] . '</td>';
		foreach($b['PackageRatePeriod'] as $itemRatePeriod) {
			$currencyCode = $currencyCodes[$b['currencyId']];
			echo '<td style="text-align: right;">';
			echo $number->currency($itemRatePeriod['ratePeriodPriceDisplay'], $currencyCode);
			echo '</td>';
		}
		echo '</tr>';
		echo "<tr{$class}>";
		foreach($b['PackageRatePeriod'] as $itemRatePeriod) {
			$currencyCode = $currencyCodes[$b['currencyId']];
			echo '<td style="border-top: 1px solid #e5e5e5;text-align: center;color:#777">' . $itemRatePeriod['quantity'].'@'.$number->currency($itemRatePeriod['ratePeriodPrice'], $currencyCode);
			if ($itemRatePeriod['feePercentDisplay']) {
				echo $itemRatePeriod['feePercentDisplay'];
			}
			echo '</td>';	
		}
		echo '</tr>';
	}
	?>

	<?php
	echo '<tr class="lastRow">';
	echo '<td>Overall Price</td>';
	foreach ($packageRatePeriods['Boundaries'] as $k => $v) {
		echo '<td style="text-align: right;"><strong>' . $number->currency($v['rangeSum'], $currencyCode) . '</strong></td>';
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
<? else: ?>
<p class='icon-yellow'>No package rate periods are available for this package. Check that the correct validity dates are set and that items are checked if you were expecting to see a rate period grid.</p>
<? endif; ?>
