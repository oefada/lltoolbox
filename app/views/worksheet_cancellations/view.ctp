<div class="ticketCancellations view">
<h2><?php  __('TicketCancellation');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketCancellationId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketCancellation['TicketCancellation']['ticketCancellationId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cancellation Reason'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($ticketCancellation['cancellationReason']['cancellationReasonName'], array('controller'=> 'cancellation_reasons', 'action'=>'view', $ticketCancellation['cancellationReason']['cancellationReasonId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketCancellation['TicketCancellation']['ticketId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateCancelled'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketCancellation['TicketCancellation']['dateCancelled']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CancellationNotes'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketCancellation['TicketCancellation']['cancellationNotes']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit TicketCancellation', true), array('action'=>'edit', $ticketCancellation['TicketCancellation']['ticketCancellationId'])); ?> </li>
		<li><?php echo $html->link(__('Delete TicketCancellation', true), array('action'=>'delete', $ticketCancellation['TicketCancellation']['ticketCancellationId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ticketCancellation['TicketCancellation']['ticketCancellationId'])); ?> </li>
		<li><?php echo $html->link(__('List TicketCancellations', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New TicketCancellation', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cancellation Reasons', true), array('controller'=> 'cancellation_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Cancellation Reason', true), array('controller'=> 'cancellation_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
