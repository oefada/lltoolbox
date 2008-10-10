<div class="worksheetCancellations view">
<h2><?php  __('WorksheetCancellation');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetCancellationId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheetCancellation['WorksheetCancellation']['worksheetCancellationId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cancellation Reason'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($worksheetCancellation['cancellationReason']['cancellationReasonName'], array('controller'=> 'cancellation_reasons', 'action'=>'view', $worksheetCancellation['cancellationReason']['cancellationReasonId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheetCancellation['WorksheetCancellation']['worksheetId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateCancelled'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheetCancellation['WorksheetCancellation']['dateCancelled']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CancellationNotes'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheetCancellation['WorksheetCancellation']['cancellationNotes']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit WorksheetCancellation', true), array('action'=>'edit', $worksheetCancellation['WorksheetCancellation']['worksheetCancellationId'])); ?> </li>
		<li><?php echo $html->link(__('Delete WorksheetCancellation', true), array('action'=>'delete', $worksheetCancellation['WorksheetCancellation']['worksheetCancellationId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $worksheetCancellation['WorksheetCancellation']['worksheetCancellationId'])); ?> </li>
		<li><?php echo $html->link(__('List WorksheetCancellations', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New WorksheetCancellation', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cancellation Reasons', true), array('controller'=> 'cancellation_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Cancellation Reason', true), array('controller'=> 'cancellation_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
