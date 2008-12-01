<fieldset id="chooseItems" class="collapsible">
	<h3 class="handle">Items/Inclusions</h3>
	<div class="collapsibleContent">
		<p>The LOA items shown in this list are the eligible items that can be added to this package. 
			The currency for each item must match the currency for this package for it to be eligible.</p>
			<? $fakeExchangeRates = array(1=>1,2=>1.2,3=>0.5) ?>
	<table>
		<tr>
			<th>&nbsp;</th>
			<th>Order</th>
			<th>Description</th>
			<th>Override Display Name</th>
			<th>Base Price<br/>
				<? foreach($clientLoaDetails[0]['LoaItem'] as $loaItem): ?>
				(base currency <?=$currencyCodes[$loaItem['currencyId']]?>
				<br/>Exchange rate to USD = <?=$fakeExchangeRates[$loaItem['currencyId']]?>)
				<? break;
					endforeach;
				?>
			</th>
			<th>Quanity</th>
		</tr>
	<?php 
		$loaItemCount = 0;
		foreach($clientLoaDetails as $clientLoaDetail): ?>
		<? foreach($clientLoaDetail['LoaItem'] as $k => $loaItem): ?>
			<tr>
				<td><input type="checkbox" name="data[Package][CheckedLoaItems][]" value="<?=$loaItem['loaItemId']?>"<? if (isset($this->data['Package']['CheckedLoaItems']) && in_array($loaItem['loaItemId'], $this->data['Package']['CheckedLoaItems'])) { echo ' checked="checked"'; } ?> /></td>
				<td><?=$form->input('PackageLoaItemRel.'.$loaItem['loaItemId'].'.weight', array('label' => false)) ?></td>
				<td><?=$loaItem['itemName']?></td>
				<td><?=$form->input('PackageLoaItemRel.'.$loaItem['loaItemId'].'.overrideDisplayName', array('label' => false)) ?></td>
				<td><div style="text-align: right"><span style="text-align:left"><?=$number->currency($loaItem['itemBasePrice'], $currencyCodes[$loaItem['currencyId']]) ?></span>&nbsp;&nbsp;<span style="text-align: right"><?=$number->currency($loaItem['itemBasePrice']*$fakeExchangeRates[$loaItem['currencyId']], 'USD') ?></span></td>
				<td><?= $form->input('PackageLoaItemRel.'.$loaItem['loaItemId'].'.quantity', array('label' => false)) ?></td>
			</tr>
		<?
		$loaItemCount++;
		endforeach; ?>
	<?php endforeach;?>
	<?php if ($loaItemCount == 0): ?>
		<tr>
			<td colspan='5'><div class='icon-yellow'>There are no LOA Items for the selected LOAs. Add some items to the LOA(s), then return to creating the package.</div></td>
		</tr>	
	<?php endif; ?>
	</table>
	</div>
</fieldset>
<?=$ajax->observeForm('PackageAddForm', array('url' => "/clients/$clientId/packages/carveRatePeriodsForDisplay", 'update' => 'ratePeriods', 'frequency' => 0.5, 'indicator' => 'spinner'))?>