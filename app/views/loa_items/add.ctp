<div class="loaItems form" style="width:100%;height:500px;overflow:auto;">
	<?php $session->flash(); ?>
<?php 
if ($isGroup) {
	echo $ajax->form('add', 'post', array('url' => "/loas/{$this->data['LoaItem']['loaId']}/loa_items/add_group", 'update' => 'MB_content', 'model' => 'LoaItem', 'complete' => 'closeModalbox()'));
} else {
	echo $ajax->form('add', 'post', array('url' => "/loas/{$this->data['LoaItem']['loaId']}/loa_items/add", 'update' => 'MB_content', 'model' => 'LoaItem', 'complete' => 'closeModalbox()'));
}
?>
	<fieldset>
	<p id="loaItemError" class="error" style="display:none;"></p>
	<?php if ($isGroup) : ?>
 			<legend><?php __('Add LoaItem Group');?></legend>
			<?php 
			echo $form->input('loaItemTypeId', array('label'=>'Group Type'));
			echo $form->input('loaId', array('type' => "hidden"));
			echo $form->input('itemName', array('label' => 'Group Name'));
			echo $form->input('merchandisingDescription', array('type'=>'text', 'label' => 'Group Lead-in Line'));
			?>
	<?php else: ?>
 			<legend><?php __('Add LoaItem');?></legend>
			<?php 
			echo $form->input('loaItemTypeId', array('label'=>'Item Type'));
			echo $form->input('loaId', array('type' => "hidden"));
			echo $form->input('itemName');
			echo $form->input('merchandisingDescription', array('label' => 'Live Site Description'));
			?>
	<?php endif;?>


		<!-- PRICE BLOCK -->
		<div id="price" style="margin-top:10px;margin-bottom:10px;">
			<div class="input text">
				<label for="LoaItemItemBasePrice">Enter Price</label>
				<input class="MB_focusable" name="data[LoaItem][itemBasePrice]" value="" id="LoaItemItemBasePrice" type="text" style="width:150px;margin-right:5px;">
			</div>
			<div class="input text" <?php if ($isGroup) { echo 'style="display:none;"';  };?>>
				<label for="LoaItemPerPerson">Price Per Person</label>
				<input class="MB_focusable" name="data[LoaItem][perPerson]" id="LoaItemPerPerson" type="text" style="width:150px;" />
			</div>
		</div>
		<!-- END PRICE BLOCK -->
		
		<!-- RATE PERIOD BLOCK -->
		<div id="rate_periods" class="input text" style="margin-top:10px;margin-bottom:10px;display:none;">
			<?php echo $form->input('roomGradeId', array('label'=>'Room Grade')); ?>
			<label>Rate Periods</label>
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

		<!-- FEE BLOCK -->
		<div class="input text">
			<label>Fee 1</label>
			<input class="MB_focusable" name="data[Fee][0][feeName]" value="" id="Fee0FeeName" type="text" style="width:150px;">
			<input class="MB_focusable" name="data[Fee][0][feePercent]" value="" id="Fee0FeePercent" type="text" style="width:100px;margin-left:20px;margin-right:5px;"> %
			<input class="MB_focusable" name="data[Fee][0][feeTypeId]" value="1" id="Fee0FeeTypeId" type="hidden" />
			<div style="font-size:11px;padding:0px;margin:0px;margin-left:170px;">(e.g. Taxes, Service Charge)</div>
		</div>
		<div class="input text">
			<label>Fee 2</label>
			<input class="MB_focusable" name="data[Fee][1][feeName]" value="" id="Fee1FeeName" type="text" style="width:150px;">
			<input class="MB_focusable" name="data[Fee][1][feePercent]" value="" id="Fee1FeePercent" type="text" style="width:100px;margin-left:20px;margin-right:5px;"> %
			<input class="MB_focusable" name="data[Fee][1][feeTypeId]" value="1" id="Fee1FeeTypeId" type="hidden" />
			<div style="font-size:11px;padding:0px;margin:0px;margin-left:170px;">(e.g. Taxes, Service Charge)</div>
		</div>
		<div class="input text">
			<label>Fee 3</label>
			<input class="MB_focusable" name="data[Fee][2][feeName]" value="" id="Fee2FeeName" type="text" style="width:150px;">
			<input class="MB_focusable" name="data[Fee][2][feePercent]" value="" id="Fee2FeePercent" type="text" style="width:100px;margin-left:20px;margin-right:5px;">
			<input class="MB_focusable" name="data[Fee][2][feeTypeId]" value="2" id="Fee2FeeTypeId" type="hidden" />
			<strong><?php echo $currencyCode;?></strong>
			<div style="font-size:11px;padding:0px;margin:0px;margin-left:170px;">(e.g. Resort Fees)</div>
		</div>
		<!-- END FEE BLOCK -->
	
	<?php if (!$isGroup) : ?>
	<div class="submit" style="text-align:right;"><input class="MB_focusable" value="Submit" type="submit" onclick="return checkFormLoaItem();"></div>
	<?php endif;?>

	<?php if ($isGroup) : ?>
		<!-- ITEMS GROUP -->
		<h3>Items in this Group</h3>
		<?php if (!empty($loa['LoaItem'])):?>
		<div class="related">
			<table cellpadding="0" cellspacing="0" class="loaItems">
			<tr>
				<th width="15">Qty.</th>
				<th>Type</th>
				<th>Name</th>
				<th>Live Site Description</th>
				<th class="actions"><?php __('Actions');?></th>
			</tr>
			<?php
				$i = 0;		
				foreach ($loa['LoaItem'] as $loaItem):
					if (!empty($loaItem['LoaGroup']) || in_array($loaItem['loaItemTypeId'], array(12,13,14))) {
						continue;
					}
					$class = ' class="altrow"';
				?>
				<tr<?php echo $class;?> id="group_<?=$loaItem['loaItemId'];?>" style="display:none;">
					<td>
						<input style="width:15px;" type="text" id="group_quantity_<?=$loaItem['loaItemId'];?>" name="data[LoaItemGroup][<?=$loaItem['loaItemId'];?>]" disabled="disabled" value="1" />
					</td>
					<td><?php echo $masterLoaItemTypeIds[$loaItem['loaItemTypeId']];?></td>
					<td><?php echo $loaItem['itemName'];?></td>
					<td><?php echo $text->excerpt($loaItem['merchandisingDescription'], 100);?></td>
					<td class="actions">
						<a href="javascript:void(0);" onclick="remove_from_group(<?=$loaItem['loaItemId'];?>);">Remove</a>
					</td>
				</tr>
				<?php if(!empty($loaItem['LoaItemRatePeriod'])): ?>
				<tr id="rp_row_<?=$loaItem['loaItemId'];?>_group" style="display:none;">
					<td id="rp_cell_<?=$loaItem['loaItemId'];?>_group" colspan="6" style="margin:0px;padding:0px;">
						<?= $this->renderElement('loa_item_rate_periods/rate_periods', array('loaItem' => $loaItem, 'closed' => true))?>
					</td>
				</tr>
				<?php endif;?>
			<?php endforeach; ?>
			</table>
		</div>
		<?php endif; ?>
		<!-- END ITEMS GROUP -->
	
		<div class="submit" style="text-align:right;"><input class="MB_focusable" value="Submit" type="submit" onclick="return checkFormLoaItem();"></div>

		<!-- ITEMS REMAINING GROUP -->
		<h3>Other Items for this LOA</h3>
		<?php if (!empty($loa['LoaItem'])):?>
		<div class="related">
			<table cellpadding="0" cellspacing="0" class="loaItems">
			<tr>
				<th>Type</th>
				<th>Name</th>
				<th>Live Site Description</th>
				<th class="actions"><?php __('Actions');?></th>
			</tr>
			<?php
				$i = 0;		
				foreach ($loa['LoaItem'] as $loaItem):
					if (!empty($loaItem['LoaGroup']) || in_array($loaItem['loaItemTypeId'], array(12,13,14))) {
						continue;
					}
					$class = ' class="altrow"';
				?>
				<tr<?php echo $class;?> id="pool_<?=$loaItem['loaItemId'];?>">
					<td><?php echo $masterLoaItemTypeIds[$loaItem['loaItemTypeId']];?></td>
					<td><?php echo $loaItem['itemName'];?></td>
					<td><?php echo $text->excerpt($loaItem['merchandisingDescription'], 100);?></td>
					<td class="actions">
						<a href="javascript:void(0);" onclick="add_to_group(<?=$loaItem['loaItemId'];?>);">Add</a>
					</td>
				</tr>
				<?php if(!empty($loaItem['LoaItemRatePeriod'])): ?>
				<tr id="rp_row_<?=$loaItem['loaItemId'];?>_remain" style="display:none;">
					<td id="rp_cell_<?=$loaItem['loaItemId'];?>_remain" colspan="5" style="margin:0px;padding:0px;">
						<?= $this->renderElement('loa_item_rate_periods/rate_periods', array('loaItem' => $loaItem, 'closed' => true))?>
					</td>
				</tr>
				<?php endif;?>
			<?php endforeach; ?>
			</table>
		</div>
		<?php endif; ?>
		<!-- END ITEMS REMAINING GROUP -->
	<?php endif;?>

		<?php
		/*
		echo '<div class="controlset">';
		echo $form->input('addAnother', array('label' => 'Save and add another', 'type' => 'checkbox'));
		echo '</div>';
		*/
		?>
	</fieldset>
</div>

<script>Event.observe("LoaItemLoaItemTypeId", "change", toggle_price);toggle_price();</script>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>
