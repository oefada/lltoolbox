<div class="packageLoaItemRels view">
<h2><?php  __('PackageLoaItemRel');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageLoaItemRelId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['packageId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Loa Item'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($packageLoaItemRel['LoaItem']['loaItemId'], array('controller'=> 'loa_items', 'action'=>'view', $packageLoaItemRel['LoaItem']['loaItemId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaItemGroupId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['loaItemGroupId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PriceOverride'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['priceOverride']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Quantity'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['quantity']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NoCharge'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['noCharge']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit PackageLoaItemRel', true), array('action'=>'edit', $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId'])); ?> </li>
		<li><?php echo $html->link(__('Delete PackageLoaItemRel', true), array('action'=>'delete', $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId'])); ?> </li>
		<li><?php echo $html->link(__('List PackageLoaItemRels', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New PackageLoaItemRel', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
