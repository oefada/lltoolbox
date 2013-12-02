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