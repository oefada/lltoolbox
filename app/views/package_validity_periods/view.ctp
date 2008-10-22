<div class="packageValidityPeriods view">
<h2><?php  __('PackageValidityPeriod');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageValidityPeriodId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['packageValidityPeriodId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['packageId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('StartDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['startDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('EndDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['endDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ValidityFlag'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['isBlackout']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit PackageValidityPeriod', true), array('action'=>'edit', $packageValidityPeriod['PackageValidityPeriod']['packageValidityPeriodId'])); ?> </li>
		<li><?php echo $html->link(__('Delete PackageValidityPeriod', true), array('action'=>'delete', $packageValidityPeriod['PackageValidityPeriod']['packageValidityPeriodId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $packageValidityPeriod['PackageValidityPeriod']['packageValidityPeriodId'])); ?> </li>
		<li><?php echo $html->link(__('List PackageValidityPeriods', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New PackageValidityPeriod', true), array('action'=>'add')); ?> </li>
	</ul>
</div>
