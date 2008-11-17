<div class="collapsible<?if(isset($closed) && $closed) echo '-closed'?>">
<span class="handle">Rate Periods</span> <?=$html2->c($loaItem['LoaItemRatePeriod'])?>
<div class="related collapsibleContent<?if(isset($closed) && $closed) echo ' closed'?>" style="padding: 5px; <?if(isset($closed) && $closed) echo ' display: none;"'?>">
<?if($loaItem['LoaItemRatePeriod']): ?>
	<table style="">
	<tr>
		<th>Name</th>
		<th>Price</th>
		<th>Start Date</th>
		<th>End Date</th>
		<th>Approved</th>
		<th class="actions">Actions</th>
	</tr>
<?php foreach ($loaItem['LoaItemRatePeriod'] as $loaItemRatePeriod):
	$trId = 'loaItemRatePeriod_'.$loaItemRatePeriod['loaItemRatePeriodId'];
?>
	<tr id='<?=$trId?>'>
		<td><?=$loaItemRatePeriod['loaItemRatePeriodName']?></td>
		<td><?=$number->currency($loaItemRatePeriod['price'], $currencyCodes[$loaItem['currencyId']]);?></td>
		<td><?=$loaItemRatePeriod['startDate']?></td>
		<td><?=$loaItemRatePeriod['endDate'] ?></td>
		<td><?=$html->image($loaItemRatePeriod['approved'] ? 'tick.png' : 'cross.png') ?>
			<?php if ($loaItemRatePeriod['approved'] && $loaItemRatePeriod['approvedBy']): ?>
				By: <?=$loaItemRatePeriod['approvedBy']?>
			<?php endif ?>
		</td>
		<td class="actions">
			<?php
			echo $html->link('Edit',
							'/loa_item_rate_periods/edit/'.$loaItemRatePeriod['loaItemRatePeriodId'],
							array(
								'title' => 'Edit LOA Item Rate Period',
								'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
								'complete' => 'closeModalbox()'
								),
							null,
							false
							);
			?>
			<?php 
				echo $ajax->link(__('Delete', true),
									array('controller'=> 'loa_item_rate_periods', 'action'=>'delete',
									$loaItemRatePeriod['loaItemRatePeriodId']),
									array('complete' => 'new Effect.Fade("'.$trId.'")'),
									sprintf(__('Are you sure you want to delete # %s?', true), $loaItemRatePeriod['loaItemRatePeriodId'])
									); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
<? else: ?>
	No rate periods for this LOA Item.
<? endif; ?>
<?php
echo $html->link('Add Rate Period',
				'/loa_items/'.$loaItem['loaItemId'].'/loa_item_rate_periods/add',
				array(
					'title' => 'Add LOA Item Rate Period',
					'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
					'complete' => 'closeModalbox()'
					),
				null,
				false
				);
?>
</div>
</div>