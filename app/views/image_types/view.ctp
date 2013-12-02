<div class="imageTypes view">
<h2><?php  __('ImageType');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ImageTypeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $imageType['ImageType']['imageTypeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ImageTypeName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $imageType['ImageType']['imageTypeName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ImageWidth'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $imageType['ImageType']['imageWidth']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ImageHeight'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $imageType['ImageType']['imageHeight']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit ImageType', true), array('action' => 'edit', $imageType['ImageType']['imageTypeId'])); ?> </li>
		<li><?php echo $html->link(__('Delete ImageType', true), array('action' => 'delete', $imageType['ImageType']['imageTypeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $imageType['ImageType']['imageTypeId'])); ?> </li>
		<li><?php echo $html->link(__('List ImageTypes', true), array('action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New ImageType', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
