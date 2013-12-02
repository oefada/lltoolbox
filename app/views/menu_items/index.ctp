<div class="menuItems index">
<h2><?php __('MenuItems');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('menuItemId');?></th>
	<th><?php echo $paginator->sort('menuId');?></th>
	<th><?php echo $paginator->sort('menuItemName');?></th>
	<th><?php echo $paginator->sort('externalLink');?></th>
	<th><?php echo $paginator->sort('linkTo');?></th>
	<th><?php echo $paginator->sort('parent_id');?></th>
	<th><?php echo $paginator->sort('lft');?></th>
	<th><?php echo $paginator->sort('rght');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($menuItems as $menuItem):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $menuItem['MenuItem']['menuItemId']; ?>
		</td>
		<td>
			<?php echo $html->link($menuItem['Menu']['menuId'], array('controller'=> 'menus', 'action'=>'view', $menuItem['Menu']['menuId'])); ?>
		</td>
		<td>
			<?php echo $menuItem['MenuItem']['menuItemName']; ?>
		</td>
		<td>
			<?php echo $menuItem['MenuItem']['externalLink']; ?>
		</td>
		<td>
			<?php echo $menuItem['MenuItem']['linkTo']; ?>
		</td>
		<td>
			<?php echo $menuItem['MenuItem']['parent_id']; ?>
		</td>
		<td>
			<?php echo $menuItem['MenuItem']['lft']; ?>
		</td>
		<td>
			<?php echo $menuItem['MenuItem']['rght']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $menuItem['MenuItem']['menuItemId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $menuItem['MenuItem']['menuItemId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $menuItem['MenuItem']['menuItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $menuItem['MenuItem']['menuItemId'])); ?>
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
		<li><?php echo $html->link(__('New MenuItem', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Menus', true), array('controller'=> 'menus', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Menu', true), array('controller'=> 'menus', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Menu Items', true), array('controller'=> 'menu_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Menu Item Parent', true), array('controller'=> 'menu_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
