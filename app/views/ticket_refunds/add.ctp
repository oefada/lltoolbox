<div class="ticketRefunds form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'ticket_refunds/add'))); ?>
	<fieldset>
 		<legend><?php __('Add TicketRefund');?></legend>
	<?php
		echo $form->input('ticketRefundTypeId');
		echo $form->input('refundReasonId');
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('dateRequested');
		echo $form->input('amountRefunded');
		echo '<div class="input text"><label>Refund Amount for Credit On File (if applicable)</label><input id="cofAmount" name="data[cofAmount]" type="text" maxlength="50" value=""/></div>';
		echo $form->input('refundNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
