<div class="ticketCancellations form">
<?php echo $form->create('TicketCancellation');?>
	<fieldset>
 		<legend><?php __('Edit TicketCancellation');?></legend>
	<?php
		echo $form->input('ticketCancellationId');
		echo $form->input('cancellationReasonId');
		echo $form->input('ticketId');
		echo $form->input('dateCancelled');
		echo $form->input('cancellationNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('TicketCancellation.ticketCancellationId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('TicketCancellation.ticketCancellationId'))); ?></li>
		<li><?php echo $html->link(__('List TicketCancellations', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Cancellation Reasons', true), array('controller'=> 'cancellation_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Cancellation Reason', true), array('controller'=> 'cancellation_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
