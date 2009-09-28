<div class="loaItems form" style="width:100%;height:500px;overflow:auto;">
	<?php $session->flash(); ?>
<?php echo $ajax->form('group', 'post', array('url' => "/loas/{$this->data['LoaItem']['loaId']}/loa_items/group", 'update' => 'MB_content', 'model' => 'LoaItem', 'complete' => 'closeModalbox()'));?>
	<fieldset>
 		<legend><?php __('Add Loa Items Group');?></legend>
		<p id="error_msg" class="error" style="font-weight:bold;display:none;"></p>
	<?php
		echo $form->input('loaItemTypeId', array('label'=>'Group Type'));
		echo $form->input('loaId', array('type' => "hidden"));
		echo $form->input('itemName', array('label' => 'Group Name'));
		echo $form->input('merchandisingDescription', array('type'=>'text', 'label' => 'Group Lead-in Line'));
	?>

		<!-- PRICE BLOCK -->
		<div id="price" style="margin-top:10px;margin-bottom:10px;">
			<div class="input text">
				<label for="LoaItemItemBasePrice">Enter Price</label>
				<input class="MB_focusable" name="data[LoaItem][itemBasePrice]" value="" id="LoaItemItemBasePrice" type="text" style="width:150px;margin-right:5px;">
				<input type="hidden" name="data[LoaItem][perPerson]" value="" id="LoaItemPerPerson" type="text" />
				<span id="option_rp">Or, <?php echo $html->link('Enter Rate Periods','javascript:void(0);',array('onclick' => 'toggle_rate_period_on();'), null,false);?></span>
			</div>
		</div>
		<!-- END PRICE BLOCK -->
		
		<!-- RATE PERIOD BLOCK -->
		<div id="rate_periods" class="input text" style="margin-top:10px;margin-bottom:10px;display:none;">
			<label>Rate Periods</label>
			<span id="option_price">Or,	<?php echo $html->link('Enter one price',	'javascript:void(0);',	array('onclick' => 'toggle_price_on();'),null,false);?></span>
			<div style="margin-top:10px;margin-bottom:0px;padding:0px;border:4px solid #444444;">
				<table id="loaRpTable" class="loaRp" cellspacing="0" cellpadding="0">
				<tr>
					<th width="160">Name</th>
					<th width="130">Start Date</th>
					<th width="110">End Date</th>
					<th>Price</th>
				</tr>
				</table>
			</div>
			<div style="margin:0px;padding:0px;background-color:#444444;border:4px solid #444444;padding:2px;">
				<a href="javascript:void(0);" style="font-weight:bold;color:#FFFFFF;" onclick="add_rate_period();">Add Another Rate Period</a>
			</div>
		</div>
		<!-- END RATE PERIOD BLOCK -->

<!-- ITEMS GROUP -->
<h3>Items in this Group</h3>
<?php if (!empty($loa['LoaItem'])):?>
<div class="related">
	<table cellpadding="0" cellspacing="0" class="loaItems">
	<tr>
		<th>Quanity</th>
		<th>Type</th>
		<th>Name</th>
		<th>Live Site Description</th>
		<th>Price</th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;		
		foreach ($loa['LoaItem'] as $loaItem):
			$class = ' class="altrow"';
		?>
		<tr<?php echo $class;?> id="group_<?=$loaItem['loaItemId'];?>" style="display:none;">
			<td><input style="width:20px;" type="text" id="" name="" /></td>
			<td><?php echo $loaItemTypeIds[$loaItem['loaItemTypeId']];?></td>
			<td><?php echo $loaItem['itemName'];?></td>
			<td><?php echo $text->excerpt($loaItem['merchandisingDescription'], 100);?></td>
			<td align="center">
				<?php if(!empty($loaItem['LoaItemRatePeriod'])): ?>
					<?= $this->renderElement('loa_item_rate_periods/table_for_loas_page', array('loaItem' => $loaItem, 'closed' => true))?>
				<?php else: ?>
					<?php echo $number->currency($loaItem['itemBasePrice'], $currencyCodes[$loa['Loa']['currencyId']]); ?>
					<?php if (!empty($loaItem['Fee']['feePercent'])) {
						echo '+'.$loaItem['Fee']['feePercent'].'%';
					}?>	
				<?php endif;?>
			</td>
			<td class="actions">
				<a href="javascript:void(0);" onclick="remove_from_group(<?=$loaItem['loaItemId'];?>);">Remove from Group</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>
<!-- END ITEMS GROUP -->

<!-- ITEMS REMAINING GROUP -->
<h3>Other Items for this LOA</h3>
<?php if (!empty($loa['LoaItem'])):?>
<div class="related">
	<table cellpadding="0" cellspacing="0" class="loaItems">
	<tr>
		<th>Type</th>
		<th>Name</th>
		<th>Live Site Description</th>
		<th>Price</th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;		
		foreach ($loa['LoaItem'] as $loaItem):
			$class = ' class="altrow"';
		?>
		<tr<?php echo $class;?> id="pool_<?=$loaItem['loaItemId'];?>">
			<td><?php echo $loaItemTypeIds[$loaItem['loaItemTypeId']];?></td>
			<td><?php echo $loaItem['itemName'];?></td>
			<td><?php echo $text->excerpt($loaItem['merchandisingDescription'], 100);?></td>
			<td align="center">
				<?php if(!empty($loaItem['LoaItemRatePeriod'])): ?>
					<?= $this->renderElement('loa_item_rate_periods/table_for_loas_page', array('loaItem' => $loaItem, 'closed' => true))?>
				<?php else: ?>
					<?php echo $number->currency($loaItem['itemBasePrice'], $currencyCodes[$loa['Loa']['currencyId']]); ?>
					<?php if (!empty($loaItem['Fee']['feePercent'])) {
						echo '+'.$loaItem['Fee']['feePercent'].'%';
					}?>	
				<?php endif;?>
			</td>
			<td class="actions">
				<a href="javascript:void(0);" onclick="add_to_group(<?=$loaItem['loaItemId'];?>);">Add to Group</a>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>
<!-- END ITEMS REMAINING GROUP -->

	</fieldset>
	<div class="submit"><input class="MB_focusable" value="Submit" type="submit" onclick="return checkFormLoaItem();"></div>
</div>

<script>Event.observe("LoaItemLoaItemTypeId", "change", toggle_price);toggle_price();</script>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>
