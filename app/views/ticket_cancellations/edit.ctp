<div class="ticketCancellations form">
<?php echo $form->create('TicketCancellation');?>
	<fieldset>
 		<legend><?php __('Edit TicketCancellation');?></legend>
	<?php
		echo $form->input('ticketCancellationId');
		echo $form->input('cancellationReasonId');
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('dateCancelled');
		echo $form->input('cancellationNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
