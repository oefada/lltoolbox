<div class="offers view">
<h2><?php  __('Offer');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('OfferId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $offer['Offer']['offerId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SchedulingInstanceId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $offer['Offer']['schedulingInstanceId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Offer Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($offer['OfferStatus']['offerStatusName'], array('controller'=> 'offer_statuses', 'action'=>'view', $offer['OfferStatus']['offerStatusId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CurrencyExchangeRateId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $offer['Offer']['currencyExchangeRateId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CurrencyExchangeRateField'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $offer['Offer']['currencyExchangeRateField']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CreateDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $offer['Offer']['createDate']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Offer', true), array('action'=>'edit', $offer['Offer']['offerId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Offer', true), array('action'=>'delete', $offer['Offer']['offerId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $offer['Offer']['offerId'])); ?> </li>
		<li><?php echo $html->link(__('List Offers', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Offer Statuses', true), array('controller'=> 'offer_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer Status', true), array('controller'=> 'offer_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Bids', true), array('controller'=> 'bids', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Bid', true), array('controller'=> 'bids', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Bids');?></h3>
	<?php if (!empty($offer['Bid'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('BidId'); ?></th>
		<th><?php __('OfferId'); ?></th>
		<th><?php __('UserId'); ?></th>
		<th><?php __('BidDateTime'); ?></th>
		<th><?php __('BidAmount'); ?></th>
		<th><?php __('AutoRebid'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($offer['Bid'] as $bid):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $bid['bidId'];?></td>
			<td><?php echo $bid['offerId'];?></td>
			<td><?php echo $bid['userId'];?></td>
			<td><?php echo $bid['bidDateTime'];?></td>
			<td><?php echo $bid['bidAmount'];?></td>
			<td><?php echo $bid['autoRebid'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'bids', 'action'=>'view', $bid['bidId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'bids', 'action'=>'edit', $bid['bidId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'bids', 'action'=>'delete', $bid['bidId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $bid['bidId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Bid', true), array('controller'=> 'bids', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
