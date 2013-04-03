<?php echo $javascript->link('news_scheduler/date_picker.js'); ?>
<?php echo $javascript->link('loa_items.js'); ?>

<?php
$loa = $this->data;
echo $this->element("loas_subheader", array("loa"=>$loa,"client"=>$client));
$this->searchController = 'Clients';
$this->set('clientId', $this->data['Client']['clientId']);

?>

<script>var currencyCode = '<?=$loa['Currency']['currencyCode'];?>';</script>

<h2 class="title"><?php __('Current LOA Items (Inclusions)');?> <?=$html2->c($loa['Loa']['loaId'], 'LOA Id:')?></h2>
<div><strong>Start Date:</strong> <?=date('M d, Y', strtotime($loa['Loa']['startDate']));?></div>
<div><strong>End Date:</strong> <?=date('M d, Y', strtotime($loa['Loa']['endDate']));?></div>
<div><strong>Selected Currency:  <?=$currencyCodes[$loa['Loa']['currencyId']];?></strong></div>
<br />
<div>
	<select id="switchLoa" style="font-size:11px;" onchange="if(this.value!='0') {window.location=this.value;}">
		<option value='0'>Switch to different LOA:</option>
		<?php foreach ($loa['Client']['Loa'] as $loas) {
			if ($loas['loaId'] != $loa['Loa']['loaId']) {
				echo "<option style='font-size:11px;' value='/loas/items/$loas[loaId]'>[$loas[loaId]] " . date('Y-m-d', strtotime($loas['startDate'])) . ' to ' . date('Y-m-d', strtotime($loas['endDate'])) . '</option>';
			}
		}
		?>
	</select>
</div>

<div style="margin-top:5px;margin-bottom:10px;text-align:right;">
    <div style="float:right;">
	<?php
	echo $html->link('<span><b class="icon"></b>Add New Item Group</span>',
					'/loas/'.$loa['Loa']['loaId'].'/loa_items/add_group',
					array(
						'title' => 'Add Loa Item Group',
						'onclick' => 'Modalbox.show(this.href, {title: this.title, width:900});return false',
						'complete' => 'closeModalbox()',
						'class' => 'button add'
						),
					null,
					false
					);

	?>
    </div>
    <div style="float:right;">
	<?php
	echo $html->link('<span><b class="icon"></b>Clone Existing LOA Item(s)</span>',
					'/loas/'.$loa['Loa']['loaId'].'/loa_items/clone_items',
					array(
						'title' => 'Clone Existing LOA Item(s)',
						'onclick' => 'Modalbox.show(this.href, {title: this.title, width:900, height:659});return false',
						'complete' => 'closeModalbox()',
						'class' => 'button add'
						),
					null,
					false
					);

	?>
	</div>
    <div style="float:right;">
	<?php
	echo $html->link('<span><b class="icon"></b>Add New Item</span>',
					'/loas/'.$loa['Loa']['loaId'].'/loa_items/add',
					array(
						'title' => 'Add Loa Item',
						'onclick' => 'Modalbox.show(this.href, {title: this.title, width:900});return false',
						'complete' => 'closeModalbox()',
						'class' => 'button add'
						),
					null,
					false
					);

	?>
	</div>
	<div style="clear:both;"></div>
</div>

<?php if (!empty($loa['LoaItem'])):?>
<div class="related">
	<table cellpadding="0" cellspacing="0" class="loaItems">
	<tr>
		<th>Type</th>
		<th>Name</th>
		<th>Live Site Description</th>
		<th width="150">Price</th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;		
		$group = false;
		foreach ($loa['LoaItem'] as $loaItem):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			$edit_mode = isset($loaItem['isGroup']) ? 'edit_group' : 'edit';
		?>
		<?php if (!$group && $edit_mode == 'edit_group'):?>
			<tr><td colspan="5" style="height:3px;background-color:silver;"></td></tr>
			<?php $group = true;?>
		<?php endif;?>
		<tr<?php echo $class;?>>
			<td>
				<?php if ($group) : ?><strong>Group:</strong><br /><?php endif;?>
				<?php echo $loaItem['LoaItemType']['loaItemTypeName'];?>
			</td>
			<td><?php echo $loaItem['itemName'];?></td>
			<td><?php echo $text->excerpt($loaItem['merchandisingDescription'], 100);?></td>
			<td align="left">
				<?php if(empty($loaItem['LoaItemRatePeriod'])): ?>
						<span>
							<span style="margin-right:5px;font-size:11px;"><strong><?=$currencyCodes[$loaItem['currencyId']];?></strong></span>
							<?php echo money_format('%i', $loaItem['itemBasePrice']); ?>
						<?php if (!empty($loaItem['Fee'])) {
							echo ' + fee(s)';
						}?>	
						</span>
				<?php else:?>
					<div id="rp-collapsible-<?=$loaItem['loaItemId'];?>" class="rp-collapsible-closed" style="text-align:center;">
						<span class="handle" onclick="toggleLoaItemRatePeriods(<?=$loaItem['loaItemId'];?>);">Rate Periods</span> <?=$html2->c($loaItem['LoaItemRatePeriod'])?>
					</div>
				<?php endif;?>
			</td>
			<td class="actions">
				<?php echo $html->link('Edit',
								"/loa_items/$edit_mode/".$loaItem['loaItemId'],
								array(
									'title' => 'Edit Loa Item',
									'onclick' => 'Modalbox.show(this.href, {title: this.title, width:900});return false',
									'complete' => 'closeModalbox()'
									),
								null,
								false
								); ?>
                <?php if (empty($loaItem['PackageLoaItemRel'])): ?>
                    <?php echo $html->link(__('Delete', true), array('controller'=> 'loa_items', 'action'=>'delete', $loaItem['loaItemId']), null, sprintf(__('Are you sure you want to delete this item?', true), $loaItem['loaItemId'])); ?>
                <?php endif; ?>
			</td>
		</tr>
		<?php if(!empty($loaItem['LoaItemRatePeriod'])): ?>
		<tr id="rp_row_<?=$loaItem['loaItemId'];?>" style="display:none;">
			<td id="rp_cell_<?=$loaItem['loaItemId'];?>" colspan="5" style="margin:0px;padding:0px;">
				<?= $this->renderElement('loa_item_rate_periods/rate_periods', array('loaItem' => $loaItem, 'closed' => true))?>
			</td>
		</tr>
		<?php endif;?>
	<?php endforeach; ?>
	</table>
</div>
<?php endif; ?>

