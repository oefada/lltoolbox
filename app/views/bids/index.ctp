<div class="bids index">
<h2><?php __('Bids');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('bidId');?></th>
	<th><?php echo $paginator->sort('offerId');?></th>
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('bidDateTime');?></th>
	<th><?php echo $paginator->sort('bidAmount');?></th>
	<th><?php echo $paginator->sort('autoRebid');?></th>
	<th><?php echo $paginator->sort('bidInactive');?></th>
	<th><?php echo $paginator->sort('maxBid');?></th>
	<th><?php echo $paginator->sort('note');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($bids as $bid):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $bid['Bid']['bidId']; ?>
		</td>
		<td>
			<?php echo $html->link($bid['Offer']['offerId'], array('controller'=> 'offers', 'action'=>'view', $bid['Offer']['offerId'])); ?>
		</td>
		<td>
			<?php echo $bid['Bid']['userId']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['bidDateTime']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['bidAmount']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['autoRebid']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['bidInactive']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['maxBid']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['note']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $bid['Bid']['bidId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $bid['Bid']['bidId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $bid['Bid']['bidId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $bid['Bid']['bidId'])); ?>
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
		<li><?php echo $html->link(__('New Bid', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Offers', true), array('controller'=> 'offers', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer', true), array('controller'=> 'offers', 'action'=>'add')); ?> </li>
	</ul>
</div>
