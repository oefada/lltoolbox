<div class="ticketRefunds form">
<?php echo $form->create('TicketRefund');?>
	<fieldset>
 		<legend><?php __('Edit TicketRefund');?></legend>
	<?php
		echo $form->input('ticketRefundId');
		echo $form->input('ticketRefundTypeId');
		echo $form->input('refundReasonId');
		echo $form->input('ticketId');
		echo $form->input('dateRequested');
		echo $form->input('amountRefunded');
		echo $form->input('refundNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>

