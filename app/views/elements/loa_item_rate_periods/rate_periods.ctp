<?if($loaItem['LoaItemRatePeriod']): ?>
	<table class="loaRpInner" cellpadding="0" cellspacing="0">
	<tr>
		<th width="20%">Name</th>
		<th width="15%">Start Date</th>
		<th width="15%">End Date</th>
		<th width="50%">Price</th>
	</tr>
<?php foreach ($loaItem['LoaItemRatePeriod'] as $loaItemRatePeriod):?>
	<tr>
		<td><?=$loaItemRatePeriod['loaItemRatePeriodName']?></td>
		<td align="center">
			<?php foreach ($loaItemRatePeriod['LoaItemDate'] as $date): ?>
				<?=$html2->date($date['startDate'])?><br />
			<?php endforeach;?>
		</td>
		<td align="center">
			<?php foreach ($loaItemRatePeriod['LoaItemDate'] as $date): ?>
				<?=$html2->date($date['endDate'])?><br />
			<?php endforeach;?>
		</td>
		<td align="center">
			<?php foreach ($loaItemRatePeriod['LoaItemRate'] as $rate): ?>
			<table cellspacing="0" cellpadding="0" border="0" style="text-align:center;margin-bottom:0px;border:0px;">
			<tr>
				<td style="width:60%;padding-right:15px;text-align:right;border:0px;">
					<?php 	
					$days = array();
					for ($i = 0; $i < 7; $i++) {
						if ($rate["w$i"]) {
							$days[] = $day_map[$i];
						}
					}
					if (count($days) != 7) { 
						echo implode(', ', $days);
					} 
					?>
					&nbsp;
				</td>
				<td style="width:40%;text-align:left;border:0px;">
					<span style="font-weight:bold;font-size:11px;margin-right:5px;"><?=$currencyCodes[$loaItem['currencyId']];?></span> 
					<?php echo money_format('%i',$rate['price']);?> <?php if (!empty($loaItem['Fee'])) { echo ' + fee(s)';}?><br />
				</td>
			</tr>
			</table>
			<?php endforeach;?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
<? else: ?>
	No rate periods for this LOA Item.
<? endif; ?>
