<div class="offers index">
<h2><?php __('Offers');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('offerId');?></th>
	<th><?php echo $paginator->sort('schedulingInstanceId');?></th>
	<th><?php echo $paginator->sort('offerStatusId');?></th>
	<th><?php echo $paginator->sort('currencyExchangeRateId');?></th>
	<th><?php echo $paginator->sort('currencyExchangeRateField');?></th>
	<th><?php echo $paginator->sort('createDate');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($offers as $offer):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $offer['Offer']['offerId']; ?>
		</td>
		<td>
			<?php echo $offer['Offer']['schedulingInstanceId']; ?>
		</td>
		<td>
			<?php echo $html->link($offer['OfferStatus']['offerStatusName'], array('controller'=> 'offer_statuses', 'action'=>'view', $offer['OfferStatus']['offerStatusId'])); ?>
		</td>
		<td>
			<?php echo $offer['Offer']['currencyExchangeRateId']; ?>
		</td>
		<td>
			<?php echo $offer['Offer']['currencyExchangeRateField']; ?>
		</td>
		<td>
			<?php echo $offer['Offer']['createDate']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $offer['Offer']['offerId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $offer['Offer']['offerId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $offer['Offer']['offerId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $offer['Offer']['offerId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Offer', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Offer Statuses', true), array('controller'=> 'offer_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer Status', true), array('controller'=> 'offer_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Bids', true), array('controller'=> 'bids', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Bid', true), array('controller'=> 'bids', 'action'=>'add')); ?> </li>
	</ul>
</div>
