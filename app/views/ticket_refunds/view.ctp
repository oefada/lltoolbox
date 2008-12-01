<div class="ticketRefunds view">
<h2><?php  __('TicketRefund');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketRefundId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketRefund['TicketRefund']['ticketRefundId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Refund Reason'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($ticketRefund['RefundReason']['refundReasonName'], array('controller'=> 'refund_reasons', 'action'=>'view', $ticketRefund['RefundReason']['refundReasonId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketRefund['TicketRefund']['ticketId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateRefunded'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketRefund['TicketRefund']['dateRefunded']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AmountRefunded'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketRefund['TicketRefund']['amountRefunded']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit TicketRefund', true), array('action'=>'edit', $ticketRefund['TicketRefund']['ticketRefundId'])); ?> </li>
		<li><?php echo $html->link(__('Delete TicketRefund', true), array('action'=>'delete', $ticketRefund['TicketRefund']['ticketRefundId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ticketRefund['TicketRefund']['ticketRefundId'])); ?> </li>
		<li><?php echo $html->link(__('List TicketRefunds', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New TicketRefund', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Refund Reasons', true), array('controller'=> 'refund_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Refund Reason', true), array('controller'=> 'refund_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
