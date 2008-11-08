<h3>Package Rate Periods</h3>
<div class="mB mT">
	<table cellpadding="2" cellspacing="0">
	
	<?php
	echo '<tr>';
	echo '<th>Range</th>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<th>' . $v['prp']['startDate'] . '<br />to<br />' . $v['prp']['endDate'] . "</th>\n";
	}
	echo '</tr>';
	?>	

	<?php
	foreach ($package['PackageLoaItemRel'] as $a => $b) {
		echo '<tr>';
		echo '<td>' . $b['LoaItem']['itemName'] . '</td>';
		foreach ($b['PackageRatePeriodItemRel'] as $ratePeriodItem) {
			echo '<td>$' . $ratePeriodItem['ratePeriodPrice'] . '</td>';
		}
		echo '</tr>';
	}
	?>

	<?php
	echo '<tr>';
	echo '<td>Overall Price</td>';
	foreach ($packageRatePeriods as $k => $v) {
		echo '<td><strong>$' . $v['prp']['approvedRetailPrice'] . '</strong></td>';
	}
	echo '</tr>';
	?>
	
	</table>
</div>
