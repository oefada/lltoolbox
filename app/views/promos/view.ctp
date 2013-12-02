<h2><?= $promo['promoName']; ?></h2>
<div class="promos">

		<table cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td>Site:</td>
			<td>
				<? if ($promo['siteId'] == 1) {
					echo 'LuxuryLink';
				   } elseif ($promo['siteId'] == 2) {
					echo 'Family';
				   } else {
				    echo 'All';
				   }
				?>
			</td>
		</tr>
		<tr>
			<td>Amount:</td>
			<td>
				<? if ($promo['amountOff'] > 0) {
					echo '$' . $promo['amountOff'];
				   } else {
				    echo $promo['percentOff'] . '%';
				   }
				?>
			</td>
		</tr> 
		<tr>
			<td>Start Date:</td>
			<td><?= $promo['startDate']; ?></td>
		</tr>
		<tr>
			<td>End Date:</td>
			<td><?= $promo['endDate']; ?></td>
		</tr>
		<tr>
			<td>Minimum Purchase:</td>
			<td>$<?= $promo['minPurchaseAmount']; ?></td>
		</tr>
		<tr>
			<td colspan="2"><hr style="height: 1px; border: 0; background-color: #ccc; margin: 5px 0;" /></td>
		</tr>
		<tr>
			<td>Active Codes:</td>
			<td><?= implode(', ', $activeCodes); ?></td>
		</tr>
		<? if ($inactiveCodes) { ?>
			<tr>
				<td colspan="2"><hr style="height: 1px; border: 0; background-color: #ccc; margin: 5px 0;" /></td>
			</tr>
			<tr>
				<td>Inactive Codes:</td>
				<td><?= implode(', ', $inactiveCodes); ?></td>
			</tr>
		<? } ?>
		<tr>
			<td colspan="2"><hr style="height: 1px; border: 0; background-color: #ccc; margin: 5px 0;" /></td>
		</tr>
		<tr>
			<td>Usage Restrictions:</td>
			<td>
				<? if ($promo['oneUsagePerCode'] > 0) {
					echo 'One Usage Per Code<br/>';
				   } 
				   if ($promo['oneUsagePerUser'] > 0) {
					echo 'One Usage Per User<br/>';
				   }
				   if ($promo['newBuyersOnly'] > 0) {
					echo 'New Buyers Only<br/>';
				   }
				?>
			</td>
		</tr>
		<? if ($displayRestrictedClients) { ?>
		<tr>
			<td>Client Restrictions:</td>
			<td>
			<? foreach ($displayRestrictedClients as $c) {
					echo $c . '<br/>';
			   } ?>
			</td>
		</tr>
		<? } ?>
		<? if ($promo['restrictClientType']) { ?>
		<tr>
			<td>Client Type Restrictions:</td>
			<td>
			<? foreach ($promo['restrictClientType'] as $rId) {
					echo $rId . ' : ' . $clientTypes[$rId] . '<br/>';
			   } ?>
			</td>
		</tr>
		<? } ?>
		<? if ($promo['restrictDestination']) { ?>
		<tr>
			<td>Destination Restrictions:</td>
			<td>
			<? foreach ($promo['restrictDestination'] as $rId) {
					echo $rId . ' : ' . $destinations[$rId] . '<br/>';
			   } ?>
			</td>
		</tr>
		<? } ?>
		<? if ($promo['restrictTheme']) { ?>
		<tr>
			<td>Theme Restrictions:</td>
			<td>
			<? foreach ($promo['restrictTheme'] as $rId) {
					echo $rId . ' : ' . $themes[$rId] . '<br/>';
			   } ?>
			</td>
		</tr>
		<? } ?>
		</table>
</div>

