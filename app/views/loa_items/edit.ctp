<div class="loaItems form">
<?php
if ($isGroup) {
	echo $ajax->form('edit', 'post', array('url' => "/loas/{$this->data['LoaItem']['loaId']}/loa_items/edit_group/{$this->data['LoaItem']['loaItemId']}", 'update' => 'MB_content', 'model' => 'LoaItem', 'complete' => 'closeModalbox()'));
} else {
	echo $ajax->form('edit', 'post', array('url' => "/loas/{$this->data['LoaItem']['loaId']}/loa_items/edit/{$this->data['LoaItem']['loaItemId']}", 'update' => 'MB_content', 'model' => 'LoaItem', 'complete' => 'closeModalbox()'));
}
?>
	<fieldset>
	<p id="loaItemError" class="error" style="display:none;"></p>
	<?php if ($isGroup) : ?>
			<legend><?php __('Edit LoaItem Group - ' . $masterLoaItemTypeIds[$this->data['LoaItem']['loaItemTypeId']]);?></legend>
			<?php	
			echo $form->input('loaItemId');
			echo $form->input('loaItemTypeId', array('label' => 'Group Type'));
			echo $form->input('loaId', array('type' => "hidden"));
			echo $form->input('itemName', array('label' => 'Group Name'));
			echo $form->input('merchandisingDescription', array('label' => 'Group Lead-in Line'));
			?>
	<?php else: ?>
			<legend><?php __('Edit LoaItem - ' . $masterLoaItemTypeIds[$this->data['LoaItem']['loaItemTypeId']]);?></legend>
			<?php	
			echo $form->input('loaItemId');
			echo $form->input('loaItemTypeId', array('type'=>'hidden'));
			echo $form->input('loaId', array('type' => "hidden"));
			echo $form->input('itemName');
			echo $form->input('merchandisingDescription', array('label' => 'Live Site Description'));
			?>
	<?php endif;?>
	
	<?php if (empty($this->data['LoaItemRatePeriod'])) :?>
		<!-- PRICE BLOCK -->
		<div id="price" style="margin-top:10px;margin-bottom:10px;">
		<?php echo $form->input('itemBasePrice', array('label' => 'Enter Price', 'style'=> 'width:150px;')); ?>
		<?php 
			if ($isGroup) {
				echo $form->input('perPerson', array('type'=>'hidden')); 
			} else {
				echo $form->input('perPerson', array('type'=>'text', 'label' => 'Price Per Person')); 
			}
		?>
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
	<?php else: ?>
		<!-- PRICE BLOCK -->
		<div id="price" style="margin-top:10px;margin-bottom:10px;display:none;">
		<?php echo $form->input('itemBasePrice', array('label' => 'Enter Price', 'style'=> 'width:150px;')); ?>
		<?php 
			if ($isGroup) {
				echo $form->input('perPerson', array('type'=>'hidden')); 
			} else {
				echo $form->input('perPerson', array('type'=>'text', 'label' => 'Price Per Person')); 
			}
		?>
		</div>
		<!-- END PRICE BLOCK -->
		<!-- RATE PERIOD BLOCK -->
		<div id="rate_periods" class="input text" style="margin-bottom:10px;">
			<?php echo $form->input('roomGradeId', array('label'=>'Room Grade')); ?>
			<div style="margin-bottom:0px;padding:0px;border:4px solid #444444;">
				<table id="loaRpTable" class="loaRp" cellspacing="0" cellpadding="0">
				<tr>
					<th width="160">Name</th>
					<th width="130">Start Date</th>
					<th width="110">End Date</th>
					<th>Price</th>
				</tr>	
				<?php foreach ($this->data['LoaItemRatePeriod'] as $kk => $vv) :?>
				<?php $rpid = $vv['loaItemRatePeriodId']; ?>
				<tr id="<?=$rpid;?>">
					<td id="<?=$rpid;?>_0">
						<input type="text" style="width:160px;" id="rpname_<?=$rpid;?>" name="data[LoaItemRatePeriod][<?=$rpid;?>][loaItemRatePeriodName]" value="<?=$vv['loaItemRatePeriodName'];?>" />
						<input type="hidden" name="data[LoaItemRatePeriod][<?=$rpid;?>][loaItemRatePeriodId]" value="<?=$rpid;?>" />
						<div style="margin-top:5px;padding:0px;">
						<?= $ajax->link("Delete Rate Period",
								array('controller' => 'loa_item_rate_periods', 'action' => 'delete', $rpid),
									array(
										'complete' => "delete_row('{$rpid}',0);"
									),
									'Are you sure you want to permantely delete this rate period?',
									FALSE)
						?>
						</div>
					</td>
					<td id="<?=$rpid;?>_1">
						<div id="rp_<?=$rpid;?>_start" style="padding:0px;margin:0px;">
							<?php foreach ($vv['LoaItemDate'] as $dd):?>
							<?php $dd['startDate'] = date('m/d/Y',strtotime($dd['startDate']));?>
							<div id="rp_date_s_<?=$dd['loaItemDateId'];?>" style="padding:0px;margin:0px;">
								<a href="javascript:void(0);" onclick="remove_date_range('<?=$dd['loaItemDateId'];?>')" style="font-size:11px;">[x]</a>
								<input id="id_rp_date_s_1_<?=$dd['loaItemDateId'];?>" type="hidden" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemDate][<?=$dd['loaItemDateId'];?>][loaItemDateId]" value="<?=$dd['loaItemDateId'];?>" />
								<input id="id_rp_date_s_<?=$dd['loaItemDateId'];?>" type="text" style="width:80px;" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemDate][<?=$dd['loaItemDateId'];?>][startDate]" class="dateformat-m-sl-d-sl-Y fill-grid-no-select MB_focusable rp_date_check" readonly="readonly" value="<?=$dd['startDate'];?>" onchange="setEndDate('<?=$dd['loaItemDateId'];?>');" />
								<script>addDatePicker('id_rp_date_s_<?=$dd['loaItemDateId'];?>');</script>
							</div>
							<?php endforeach;?>
						</div>
						<div style="margin-top:15px;padding:0px;"><a href="javascript:void(0);" onclick="add_date_range('<?=$rpid;?>');">Another Date Range</a></div>
					</td>
					<td id="<?=$rpid;?>_2">
						<div id="rp_<?=$rpid;?>_end" style="padding:0px;margin:0px;">
							<?php foreach ($vv['LoaItemDate'] as $dd):?>
							<?php $dd['endDate'] = date('m/d/Y',strtotime($dd['endDate']));?>
							<div id="rp_date_e_<?=$dd['loaItemDateId'];?>" style="padding:0px;margin:0px;">
								<input id="id_rp_date_e_1_<?=$dd['loaItemDateId'];?>" type="hidden" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemDate][<?=$dd['loaItemDateId'];?>][loaItemDateId]" value="<?=$dd['loaItemDateId'];?>" />
								<input id="id_rp_date_e_<?=$dd['loaItemDateId'];?>" type="text" style="width:80px;" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemDate][<?=$dd['loaItemDateId'];?>][endDate]" class="dateformat-m-sl-d-sl-Y fill-grid-no-select MB_focusable" readonly="readonly" value="<?=$dd['endDate'];?>" onchange="setEndDate('<?=$dd['loaItemDateId'];?>');" />
								<script>addDatePicker('id_rp_date_e_<?=$dd['loaItemDateId'];?>');</script>
							</div>
							<?php endforeach;?>
						</div>
					</td>
					<td id="<?=$rpid;?>_3">
						<div id="<?=$rpid;?>_s" class="rpDates">
									<input type="hidden" id="<?=$rpid;?>_rpd_0" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w0]" value="1" />
									<input type="hidden" id="<?=$rpid;?>_rpd_1" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w1]" value="1" />
									<input type="hidden" id="<?=$rpid;?>_rpd_2" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w2]" value="1" />
									<input type="hidden" id="<?=$rpid;?>_rpd_3" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w3]" value="1" />
									<input type="hidden" id="<?=$rpid;?>_rpd_4" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w4]" value="1" />
									<input type="hidden" id="<?=$rpid;?>_rpd_5" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w5]" value="1" />
									<input type="hidden" id="<?=$rpid;?>_rpd_6" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w6]" value="1" />
									<strong style="font-size:11px;"><?=$currencyCode;?></strong>
									<input type="text" style="width:50px;" class="rp_price" id="<?=$rpid;?>_rpd_7" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][price]" value="<?=$vv['LoaItemRate'][0]['price'];?>" />
									<div style="margin-top:5px;padding:0px;">
										<!--a href="javascript:void(0);" onclick="toggleF('<?=$rpid;?>', 's', 'm');">Different prices for weeknights/weekends</a-->
									</div>

									<?php if ($vv['LoaItemRate'][0]['loaItemRateId']) : ?>
										<input type="hidden" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][loaItemRateId]" id="<?=$rpid;?>_rpd_8" value="<?=$vv['LoaItemRate'][0]['loaItemRateId'];?>" />
									<?php endif; ?>
						</div>
						<div id="<?=$rpid;?>_m" class="rpDates" style="display:none;">
							<div class="rpDates">
								<div id="<?=$rpid;?>_m_0" class="rpDates">
									<input type="checkbox" id="<?=$rpid;?>_rpd_0-0" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w0]" value="1" <?php echo ($vv['LoaItemRate'][0]['w0']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_0-0">Su</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_1-0" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w1]" value="1" <?php echo ($vv['LoaItemRate'][0]['w1']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_1-0">M</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_2-0" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w2]" value="1" <?php echo ($vv['LoaItemRate'][0]['w2']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_2-0">Tu</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_3-0" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w3]" value="1" <?php echo ($vv['LoaItemRate'][0]['w3']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_3-0">W</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_4-0" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w4]" value="1" <?php echo ($vv['LoaItemRate'][0]['w4']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_4-0">Th</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_5-0" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w5]" value="1" <?php echo ($vv['LoaItemRate'][0]['w5']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_5-0">F</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_6-0" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][w6]" value="1" <?php echo ($vv['LoaItemRate'][0]['w6']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_6-0">Sa</label>
									<strong style="font-size:11px;"><?=$currencyCode;?></strong>
									<input type="text" style="width:50px;" class="rp_price" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][price]" id="<?=$rpid;?>_rpd_7_0" value="<?=$vv['LoaItemRate'][0]['price'];?>" />

									<?php if ($vv['LoaItemRate'][0]['loaItemRateId']) : ?>
										<input type="hidden" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][0][loaItemRateId]" id="<?=$rpid;?>_rpd_8_0" value="<?=$vv['LoaItemRate'][0]['loaItemRateId'];?>" />
									<?php endif; ?>
								</div>
								<div id="<?=$rpid;?>_m_1" class="rpDates" style="margin-top:10px;">
									<input type="checkbox" id="<?=$rpid;?>_rpd_0-1" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][1][w0]" value="1" <?php echo ($vv['LoaItemRate'][1]['w0']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_0-1">Su</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_1-1" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][1][w1]" value="1" <?php echo ($vv['LoaItemRate'][1]['w1']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_1-1">M</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_2-1" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][1][w2]" value="1" <?php echo ($vv['LoaItemRate'][1]['w2']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_2-1">Tu</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_3-1" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][1][w3]" value="1" <?php echo ($vv['LoaItemRate'][1]['w3']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_3-1">W</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_4-1" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][1][w4]" value="1" <?php echo ($vv['LoaItemRate'][1]['w4']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_4-1">Th</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_5-1" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][1][w5]" value="1" <?php echo ($vv['LoaItemRate'][1]['w5']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_5-1">F</label>
									<input type="checkbox" id="<?=$rpid;?>_rpd_6-1" onclick="checkDays(this.id);" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][1][w6]" value="1" <?php echo ($vv['LoaItemRate'][1]['w6']) ? 'checked="checked"' : '';?> /> <label for="<?=$rpid;?>_rpd_6-1">Sa</label>
									<strong style="font-size:11px;"><?=$currencyCode;?></strong>
									<input type="text" style="width:50px;" class="rp_price" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][1][price]" id="<?=$rpid;?>_rpd_7_1" value="<?=$vv['LoaItemRate'][1]['price'];?>" />

									<?php if ($vv['LoaItemRate'][1]['loaItemRateId']) : ?>
										<input type="hidden" name="data[LoaItemRatePeriod][<?=$rpid;?>][LoaItemRate][1][loaItemRateId]" id="<?=$rpid;?>_rpd_8_1" value="<?=$vv['LoaItemRate'][1]['loaItemRateId'];?>" />
									<?php endif; ?>
								</div>
								<div style="margin-top:5px;padding:0px;">
									<!--a href="javascript:void(0);" onclick="toggleF('<?=$rpid;?>', 'm','s');">One price for all nights</a-->
								</div>
							</div>
						</div>
						<script>toggleF('<?=$rpid;?>', 'm', 's');</script>
					</td>
				</tr>
				<?php endforeach;?>

				</table>
			</div>
			<div style="margin:0px;padding:0px;background-color:#444444;border:4px solid #444444;padding:2px;">
				<a href="javascript:void(0);" style="font-weight:bold;color:#FFFFFF;" onclick="add_rate_period();">Add Another Rate Period</a>
			</div>
		</div>
		<!-- END RATE PERIOD BLOCK -->
	<?php endif; ?>

		<!-- FEE BLOCK -->
		<?php foreach ($this->data['Fee'] as $k=>$fee): ?>
		<div class="input text">
			<label>Fee</label>
			<input class="MB_focusable" name="data[Fee][<?=$k;?>][feeId]" value="<?=$fee['feeId'];?>" id="Fee<?=$k;?>FeeId" type="hidden" />
			<input class="MB_focusable" name="data[Fee][<?=$k;?>][feeTypeId]" value="<?=$fee['feeTypeId'];?>" id="Fee<?=$k;?>FeeTypeId" type="hidden" />
			<input class="MB_focusable" name="data[Fee][<?=$k;?>][feeName]" value="<?=$fee['feeName'];?>" id="Fee<?=$k;?>FeeName" type="text" style="width:150px;" />
			<input class="MB_focusable" name="data[Fee][<?=$k;?>][feePercent]" value="<?=$fee['feePercent'];?>" id="Fee<?=$k;?>FeePercent" type="text" style="width:100px;margin-left:20px;margin-right:5px;" /> 
			<?php if ($fee['feeTypeId'] == 1) : ?>
				% <div style="font-size:11px;padding:0px;margin:0px;margin-left:170px;">(e.g. Taxes, Service Charge)</div>
			<?php elseif ($fee['feeTypeId'] == 2) : ?>
				<strong><?php echo $currencyCode;?></strong>
				<div style="font-size:11px;padding:0px;margin:0px;margin-left:170px;">(e.g. Resort Fees)</div>
			<?php endif;?>
		</div>
		<?php endforeach;?>
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
					if (isset($groupIds[$loaItem['loaItemId']])) {
						$display_row = '';
						$disable_row = '';
						$q_value = $groupIds[$loaItem['loaItemId']]['quantity'];
					} else {
						$display_row = 'style="display:none;"';
						$disable_row = 'disabled="disabled"';
						$q_value = 1;
					}
				?>
				<tr<?php echo $class;?> id="group_<?=$loaItem['loaItemId'];?>" <?=$display_row;?>>
					<td>
						<input style="width:15px;" type="text" id="group_quantity_<?=$loaItem['loaItemId'];?>" name="data[LoaItemGroup][<?=$loaItem['loaItemId'];?>]" value="<?=$q_value;?>" <?=$disable_row;?> />
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
					if (isset($groupIds[$loaItem['loaItemId']])) {
						$display_row = 'style="display:none;"';
					} else {
						$display_row = '';
					}
				?>
				<tr<?php echo $class;?> id="pool_<?=$loaItem['loaItemId'];?>" <?=$display_row;?>>
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

	</fieldset>
</div>
<script>Event.observe("LoaItemLoaItemTypeId", "change", toggle_price);toggle_price();</script>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>
