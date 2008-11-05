<fieldset class="collapsible">
	<legend class="handle">Step 4 - Choose Items</legend>
	<table>
		<tr>
			<th>&nbsp;</th>
			<th>Description</th>
			<th>Base Price</th>
			<th>Quanity</th>
		</tr>
	<?php foreach($clientLoaDetails as $clientLoaDetail): ?>
		<? foreach($clientLoaDetail['LoaItem'] as $k => $loaItem): ?>
			<tr>
				<td><?= $form->checkbox('loaItemIds[]', array('value' => $loaItem['loaItemId'])) ?></td>
				<td><?=$loaItem['itemName']?></td>
				<td><?=$loaItem['itemBasePrice']?></td>
				<td><?= $form->input('PackageLoaItemRel.'.$k.'.quantity') ?></td>
			</tr>
			<? debug($loaItem) ?>
		<? endforeach; ?>
	<?php endforeach;?>
	</table>
</fieldset>