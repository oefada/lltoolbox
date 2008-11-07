<fieldset id="chooseItems" class="collapsible">
	<legend class="handle">Step 4 - Choose Items</legend>
	<div class="collapsibleContent">
		<p>The LOA items shown in this list are the eligible items that can be added to this package. 
			The currency for each item must match the currency for this package for it to be eligible.</p>
	<table>
		<tr>
			<th>&nbsp;</th>
			<th>Description</th>
			<th>Base Price</th>
			<th>Quanity</th>
		</tr>
	<?php 
		$loaItemCount = 0;
		foreach($clientLoaDetails as $clientLoaDetail): ?>
		<? foreach($clientLoaDetail['LoaItem'] as $k => $loaItem): ?>
			<tr>
				<td><input type="checkbox" name="data[Package][CheckedLoaItems][]" value="<?=$loaItem['loaItemId']?>"  /></td>
				<td><?=$loaItem['itemName']?></td>
				<td><?=$loaItem['itemBasePrice']?></td>
				<td><?= $form->input('PackageLoaItemRel.'.$loaItem['loaItemId'].'.quantity') ?></td>
			</tr>
		<?
		$loaItemCount++;
		endforeach; ?>
	<?php endforeach;?>
	<?php if ($loaItemCount == 0): ?>
		<tr>
			<td colspan='4'><div class='icon-yellow'>There are no LOA Items for the selected LOAs. Add some items to the LOA(s), then return to creating the package.</div></td>
		</tr>	
	<?php endif; ?>
	</table>
	</div>
</fieldset>