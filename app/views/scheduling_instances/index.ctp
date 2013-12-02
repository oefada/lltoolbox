<div class="schedulingInstances index">
<h2><?php __('SchedulingInstances');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('schedulingInstanceId');?></th>
	<th><?php echo $paginator->sort('schedulingMasterId');?></th>
	<th><?php echo $paginator->sort('startDate');?></th>
	<th><?php echo $paginator->sort('endDate');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($schedulingInstances as $schedulingInstance):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $schedulingInstance['SchedulingInstance']['schedulingInstanceId']; ?>
		</td>
		<td>
			<?php echo $schedulingInstance['SchedulingInstance']['schedulingMasterId']; ?>
		</td>
		<td>
			<?php echo $schedulingInstance['SchedulingInstance']['startDate']; ?>
		</td>
		<td>
			<?php echo $schedulingInstance['SchedulingInstance']['endDate']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $schedulingInstance['SchedulingInstance']['schedulingInstanceId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $schedulingInstance['SchedulingInstance']['schedulingInstanceId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $schedulingInstance['SchedulingInstance']['schedulingInstanceId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $schedulingInstance['SchedulingInstance']['schedulingInstanceId'])); ?>
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
		<li><?php echo $html->link(__('New SchedulingInstance', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Offers', true), array('controller'=> 'offers', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer', true), array('controller'=> 'offers', 'action'=>'add')); ?> </li>
	</ul>
</div>
