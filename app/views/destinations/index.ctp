<div class="destinations index">
<h2><?php __('Destinations');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('destinationId');?></th>
	<th><?php echo $paginator->sort('parentId');?></th>
	<th><?php echo $paginator->sort('destinationName');?></th>
	<th><?php echo $paginator->sort('includeInNav');?></th>
	<th><?php echo $paginator->sort('display');?></th>
	<th><?php echo $paginator->sort('leftValue');?></th>
	<th><?php echo $paginator->sort('rightValue');?></th>
	<th><?php echo $paginator->sort('tagId');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($destinations as $destination):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $destination['Destination']['destinationId']; ?>
		</td>
		<td>
			<?php echo $destination['Destination']['parentId']; ?>
		</td>
		<td>
			<?php echo $destination['Destination']['destinationName']; ?>
		</td>
		<td>
			<?php echo $destination['Destination']['includeInNav']; ?>
		</td>
		<td>
			<?php echo $destination['Destination']['display']; ?>
		</td>
		<td>
			<?php echo $destination['Destination']['leftValue']; ?>
		</td>
		<td>
			<?php echo $destination['Destination']['rightValue']; ?>
		</td>
		<td>
			<?php echo $html->link($destination['Tag']['tagId'], array('controller'=> 'tags', 'action'=>'view', $destination['Tag']['tagId'])); ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $destination['Destination']['destinationId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $destination['Destination']['destinationId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $destination['Destination']['destinationId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $destination['Destination']['destinationId'])); ?>
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
		<li><?php echo $html->link(__('New Destination', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
