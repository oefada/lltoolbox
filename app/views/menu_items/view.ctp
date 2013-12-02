<div class="menuItems view">
<h2><?php  __('MenuItem');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MenuItemId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menuItem['MenuItem']['menuItemId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Menu'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($menuItem['Menu']['menuId'], array('controller'=> 'menus', 'action'=>'view', $menuItem['Menu']['menuId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MenuItemName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menuItem['MenuItem']['menuItemName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ExternalLink'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menuItem['MenuItem']['externalLink']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LinkTo'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menuItem['MenuItem']['linkTo']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Parent Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menuItem['MenuItem']['parent_id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Lft'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menuItem['MenuItem']['lft']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Rght'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menuItem['MenuItem']['rght']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit MenuItem', true), array('action'=>'edit', $menuItem['MenuItem']['menuItemId'])); ?> </li>
		<li><?php echo $html->link(__('Delete MenuItem', true), array('action'=>'delete', $menuItem['MenuItem']['menuItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $menuItem['MenuItem']['menuItemId'])); ?> </li>
		<li><?php echo $html->link(__('List MenuItems', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New MenuItem', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Menus', true), array('controller'=> 'menus', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Menu', true), array('controller'=> 'menus', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Menu Items', true), array('controller'=> 'menu_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Menu Item Parent', true), array('controller'=> 'menu_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
