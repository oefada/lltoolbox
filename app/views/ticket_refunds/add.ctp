<div class="ticketRefunds form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'ticketRefunds/add'))); ?>
	<fieldset>
 		<legend><?php __('Add TicketRefund');?></legend>
	<?php
		echo $form->input('refundReasonId');
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('dateRefunded');
		echo $form->input('amountRefunded');
		echo $form->input('refundNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
