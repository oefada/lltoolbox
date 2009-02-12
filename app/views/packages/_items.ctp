<fieldset id="chooseItems" class="collapsible">
	<h3 class="handle">Items/Inclusions</h3>
	<div class="collapsibleContent">
		<p>The LOA items shown in this list are the eligible items that can be added to this package. 
			The currency for each item must match the currency for this package for it to be eligible.</p>
			<? $fakeExchangeRates = array(1=>1,2=>1.2,3=>0.5) ?>
	<?php 
		foreach($clientLoaDetails as $k => $c):
		
		if (count($clientLoaDetails) > 1) :
	?>
		<h3>Items for <?=$c['Client']['name']?></h3>
	<?php endif; ?>
	<table>
		<tr>
			<th>&nbsp;</th>
			<th style="width: 70px">Order</th>
			<th style="width: 100px">Type</th>
			<th>Description</th>
			<th>Base Price<br/>
				<? foreach($clientLoaDetails[0]['LoaItem'] as $loaItem): ?>
				(base currency <?=$currencyCodes[$loaItem['currencyId']]?>
				<br/>Exchange rate to USD = <?=$fakeExchangeRates[$loaItem['currencyId']]?>)
				<? break;
					endforeach;
				?>
			</th>
			<th style="width: 120px">Quantity</th>
		</tr>
		<? if(empty($c['LoaItem'])): ?>
		<tr><td colspan=5><div class='icon-yellow'>No items for <?=$c['Client']['name']?>. Add some items to the LOA(s), then return to creating the package.</div></td></tr>
		<? endif; ?>
		<? 
		$i = 0;
		foreach($c['LoaItem'] as $k2 => $loaItem):
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			} else {
				$class = '';
			}
		?>
			<tr<?=$class?>>
				<td><input type="checkbox" id="item_<?=$k2*$k?>" name="data[Package][CheckedLoaItems][]" value="<?=$loaItem['loaItemId']?>"<? if (isset($this->data['Package']['CheckedLoaItems']) && in_array($loaItem['loaItemId'], $this->data['Package']['CheckedLoaItems'])) { echo ' checked="checked"'; } ?> /></td>
				<td><?=$form->input('PackageLoaItemRel.'.$loaItem['loaItemId'].'.weight', array('label' => false, 'size' => '2', 'style' => 'width: 50px')) ?></td>
				<td><?=$loaItemTypes[$loaItem['loaItemTypeId']]?></td>
				<td><label for="item_<?=$k2*$k?>"><?=$loaItem['itemName']?></label></td>
				<td><div style="text-align: right"><span style="text-align:left">
				<?=$number->currency($loaItem['itemBasePrice'], $currencyCodes[$loaItem['currencyId']]) ?>
				<? if (!empty($loaItem['Fee']['feePercent'])):
					echo ' + '.$loaItem['Fee']['feePercent'].'%';
				endif; ?>
				<?=$form->input('PackageLoaItemRel.'.$loaItem['loaItemId'].'.basePrice', array('type' => 'hidden', 'disabled' => true, 'value' => $loaItem['itemBasePrice']))?>
				</span>&nbsp;&nbsp;
				
				<?php if ($currencyCodes[$loaItem['currencyId']] != 'USD'): ?>
				<span style="text-align: right">
				<?=$number->currency($loaItem['itemBasePrice']*$fakeExchangeRates[$loaItem['currencyId']], 'USD') ?>
				</span>
				<?php endif; ?>
				
				</td>
				<td>
					<div class="controlset2" style="float: left; clear: none; width: 50px">
					<?
					$attributes = array('label' => false, 'style' => 'width: 30px;');
					if (empty($this->data['PackageLoaItemRel'][$loaItem['loaItemId']]['quantity'])) {
						$attributes['value'] = 1;
					}
					if ($loaItem['loaItemTypeId'] != 1) {
						$attributes['readonly'] = true;
					}
					echo $form->input('PackageLoaItemRel.'.$loaItem['loaItemId'].'.quantity', $attributes);
					?>
					</div>
					<? if($loaItem['loaItemTypeId'] != 1): ?>
					<div class="controlset" style="float: left; margin-left: 3px; clear: none">
						<? $checked = '';
						if( @$this->data['PackageLoaItemRel'][$loaItem['loaItemId']]['quantity'] == $this->data['Package']['numNights'] && $this->data['Package']['numNights'] > 0) {
							$checked = ' checked="checked"';
						}?>
					<input type="checkbox" name="perNight_<?=$loaItem['loaItemId']?>" id="perNight_<?=$loaItem['loaItemId']?>" value="1" onclick="perPersonPerNight(<?=$loaItem['loaItemId']?>)"<?=$checked?> /><label for="perNight_<?=$loaItem['loaItemId']?>">PN</label>
					</div>
					<? endif; ?>
					<?= $form->input('PackageLoaItemRel.'.$loaItem['loaItemId'].'.loaItemTypeId', array('type' => 'hidden', 'value' => $loaItem['loaItemTypeId'])) ?>
				</td>
			</tr>
		<?
		endforeach; ?>
	</table>
	<? endforeach; ?>
	</div>
</fieldset>
<?=$ajax->observeForm('PackageAddForm', array('url' => "/clients/$clientId/packages/carveRatePeriodsForDisplay", 'update' => 'ratePeriods', 'frequency' => 0.5, 'indicator' => 'spinner'))?>