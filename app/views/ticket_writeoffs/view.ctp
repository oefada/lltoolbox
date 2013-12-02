<div class="ticketWriteoffs view">
<h2><?php  __('TicketWriteoff');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketWriteoffId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketWriteoff['TicketWriteoff']['ticketWriteoffId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Writeoff Reason'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($ticketWriteoff['ticketWriteoffReason']['ticketWriteoffReasonName'], array('controller'=> 'ticket_writeoff_reasons', 'action'=>'view', $ticketWriteoff['ticketWriteoffReason']['ticketWriteoffReasonId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketWriteoff['TicketWriteoff']['ticketId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateCancelled'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketWriteoff['TicketWriteoff']['dateRequested']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WriteoffNotes'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticketWriteoff['TicketWriteoff']['writeoffNotes']; ?>
			&nbsp;
		</dd>
	</dl>
</div>