<div class="loaItemRatePeriods view">
<h2><?php  __('LoaItemRatePeriod');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaItemRatePeriodId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Loa Item'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($loaItemRatePeriod['LoaItem']['itemName'], array('controller'=> 'loa_items', 'action'=>'view', $loaItemRatePeriod['LoaItem']['loaItemId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaItemRatePeriodName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('StartDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['startDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('EndDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['endDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ApprovedRetailPrice'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['approvedRetailPrice']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Approved'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['approved']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ApprovedBy'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loaItemRatePeriod['LoaItemRatePeriod']['approvedBy']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit LoaItemRatePeriod', true), array('action'=>'edit', $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'])); ?> </li>
		<li><?php echo $html->link(__('Delete LoaItemRatePeriod', true), array('action'=>'delete', $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loaItemRatePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'])); ?> </li>
		<li><?php echo $html->link(__('List LoaItemRatePeriods', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New LoaItemRatePeriod', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
