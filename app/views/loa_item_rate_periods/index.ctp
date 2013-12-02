<div class="loaItemRatePeriods index">
<h2><?php __('LoaItemRatePeriods');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('loaItemRatePeriodId');?></th>
	<th><?php echo $paginator->sort('loaItemId');?></th>
	<th><?php echo $paginator->sort('loaItemRatePeriodName');?></th>
	<th><?php echo $paginator->sort('startDate');?></th>
	<th><?php echo $paginator->sort('endDate');?></th>
	<th><?php echo $paginator->sort('approvedRetailPrice');?></th>
	<th><?php echo $paginator->sort('approved');?></th>
	<th><?php echo $paginator->sort('approvedBy');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($loaItemRatePeriods as $loaItemRatePeriod):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>
		</td>
		<td>
			<?php echo $html->link($loaItemRatePeriod['LoaItem']['itemName'], array('controller'=> 'loa_items', 'action'=>'view', $loaItemRatePeriod['LoaItem']['loaItemId'])); ?>
		</td>
		<td>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodName']; ?>
		</td>
		<td>
			<?php echo $html2->date($loaItemRatePeriod['LoaItemRatePeriod']['startDate']); ?>
		</td>
		<td>
			<?php echo $html2->date($loaItemRatePeriod['LoaItemRatePeriod']['endDate']); ?>
		</td>
		<td>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['approvedRetailPrice']; ?>
		</td>
		<td>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['approved']; ?>
		</td>
		<td>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['approvedBy']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'])); ?>
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
		<li><?php echo $html->link(__('New LoaItemRatePeriod', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
