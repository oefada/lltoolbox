<div class="ticketWriteoffs form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'ticketWriteoffs/add'))); ?>
	<fieldset>
 		<legend><?php __('Add Ticket Write Off');?></legend>
	<?php
		echo $form->input('ticketWriteoffReasonId');
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('dateRequested');
		echo $form->input('writeoffNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
