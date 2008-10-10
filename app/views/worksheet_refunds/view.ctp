<div class="worksheetRefunds view">
<h2><?php  __('WorksheetRefund');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetRefundId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheetRefund['WorksheetRefund']['worksheetRefundId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Refund Reason'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($worksheetRefund['RefundReason']['refundReasonName'], array('controller'=> 'refund_reasons', 'action'=>'view', $worksheetRefund['RefundReason']['refundReasonId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheetRefund['WorksheetRefund']['worksheetId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateRefunded'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheetRefund['WorksheetRefund']['dateRefunded']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AmountRefunded'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheetRefund['WorksheetRefund']['amountRefunded']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit WorksheetRefund', true), array('action'=>'edit', $worksheetRefund['WorksheetRefund']['worksheetRefundId'])); ?> </li>
		<li><?php echo $html->link(__('Delete WorksheetRefund', true), array('action'=>'delete', $worksheetRefund['WorksheetRefund']['worksheetRefundId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $worksheetRefund['WorksheetRefund']['worksheetRefundId'])); ?> </li>
		<li><?php echo $html->link(__('List WorksheetRefunds', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New WorksheetRefund', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Refund Reasons', true), array('controller'=> 'refund_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Refund Reason', true), array('controller'=> 'refund_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
