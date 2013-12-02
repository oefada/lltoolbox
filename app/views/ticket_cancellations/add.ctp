<div class="ticketCancellations form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'ticketCancellations/add'))); ?>
	<fieldset>
 		<legend><?php __('Add Ticket Write Off');?></legend>
	<?php
		echo $form->input('cancellationReasonId');
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('dateCancelled');
		echo $form->input('cancellationNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
