<div class="menus view">
<h2><?php  __('Menu');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MenuId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menu['Menu']['menuId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Menu Title Image'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($menu['MenuTitleImage']['menuTitleImageName'], array('controller'=> 'menu_title_images', 'action'=>'view', $menu['MenuTitleImage']['menuTitleImageId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MenuName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menu['Menu']['menuName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Weight'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $menu['Menu']['weight']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Menu', true), array('action'=>'edit', $menu['Menu']['menuId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Menu', true), array('action'=>'delete', $menu['Menu']['menuId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $menu['Menu']['menuId'])); ?> </li>
		<li><?php echo $html->link(__('List Menus', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Menu', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Menu Title Images', true), array('controller'=> 'menu_title_images', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Menu Title Image', true), array('controller'=> 'menu_title_images', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Menu Items', true), array('controller'=> 'menu_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Menu Item', true), array('controller'=> 'menu_items', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Styles', true), array('controller'=> 'styles', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Style', true), array('controller'=> 'styles', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Menu Items');?></h3>
	<?php if (!empty($menu['MenuItem'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('MenuItemId'); ?></th>
		<th><?php __('MenuId'); ?></th>
		<th><?php __('MenuItemName'); ?></th>
		<th><?php __('ExternalLink'); ?></th>
		<th><?php __('LinkTo'); ?></th>
		<th><?php __('Parent Id'); ?></th>
		<th><?php __('Lft'); ?></th>
		<th><?php __('Rght'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($menu['MenuItem'] as $menuItem):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $menuItem['menuItemId'];?></td>
			<td><?php echo $menuItem['menuId'];?></td>
			<td><?php echo $menuItem['menuItemName'];?></td>
			<td><?php echo $menuItem['externalLink'];?></td>
			<td><?php echo $menuItem['linkTo'];?></td>
			<td><?php echo $menuItem['parent_id'];?></td>
			<td><?php echo $menuItem['lft'];?></td>
			<td><?php echo $menuItem['rght'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'menu_items', 'action'=>'view', $menuItem['menuItemId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'menu_items', 'action'=>'edit', $menuItem['menuItemId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'menu_items', 'action'=>'delete', $menuItem['menuItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $menuItem['menuItemId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Menu Item', true), array('controller'=> 'menu_items', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Styles');?></h3>
	<?php if (!empty($menu['Style'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('StyleId'); ?></th>
		<th><?php __('StyleName'); ?></th>
		<th><?php __('Style Parent Id'); ?></th>
		<th><?php __('Style Type Id'); ?></th>
		<th><?php __('Style Image'); ?></th>
		<th><?php __('Style Inactive'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($menu['Style'] as $style):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $style['styleId'];?></td>
			<td><?php echo $style['styleName'];?></td>
			<td><?php echo $style['style_parent_id'];?></td>
			<td><?php echo $style['style_type_id'];?></td>
			<td><?php echo $style['style_image'];?></td>
			<td><?php echo $style['style_inactive'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'styles', 'action'=>'view', $style['styleId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'styles', 'action'=>'edit', $style['styleId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'styles', 'action'=>'delete', $style['styleId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $style['styleId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

</div>
