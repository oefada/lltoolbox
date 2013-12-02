<div class="imageClients index">
<h2><?php __('ImageClients');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('clientImageId');?></th>
	<th><?php echo $paginator->sort('siteId');?></th>
	<th><?php echo $paginator->sort('imageId');?></th>
	<th><?php echo $paginator->sort('imageTypeId');?></th>
	<th><?php echo $paginator->sort('clientId');?></th>
	<th><?php echo $paginator->sort('caption');?></th>
	<th><?php echo $paginator->sort('sortOrder');?></th>
	<th><?php echo $paginator->sort('isHidden');?></th>
	<th><?php echo $paginator->sort('inactive');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($imageClients as $imageClient):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $imageClient['ImageClient']['clientImageId']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['siteId']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['imageId']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['imageTypeId']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['clientId']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['caption']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['sortOrder']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['isHidden']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['inactive']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['created']; ?>
		</td>
		<td>
			<?php echo $imageClient['ImageClient']['modified']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action' => 'view', $imageClient['ImageClient']['clientImageId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action' => 'edit', $imageClient['ImageClient']['clientImageId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action' => 'delete', $imageClient['ImageClient']['clientImageId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $imageClient['ImageClient']['clientImageId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class' => 'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New ImageClient', true), array('action' => 'add')); ?></li>
	</ul>
</div>
