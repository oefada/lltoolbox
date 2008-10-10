<div class="tags index">
<h2><?php __('Tags');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('tagId');?></th>
	<th><?php echo $paginator->sort('tagName');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($tags as $tag):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $tag['Tag']['tagId']; ?>
		</td>
		<td>
			<?php echo $tag['Tag']['tagName']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $tag['Tag']['tagId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $tag['Tag']['tagId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $tag['Tag']['tagId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $tag['Tag']['tagId'])); ?>
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
		<li><?php echo $html->link(__('New Tag', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Destinations', true), array('controller'=> 'destinations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Destination', true), array('controller'=> 'destinations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Coordinates', true), array('controller'=> 'coordinates', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Coordinate', true), array('controller'=> 'coordinates', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
	</ul>
</div>
