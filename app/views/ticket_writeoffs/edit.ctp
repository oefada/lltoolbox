<div class="ticketWriteoffs form">
<?php echo $form->create('TicketWriteoff');?>
	<fieldset>
 		<legend><?php __('Edit TicketWriteoff');?></legend>
	<?php
		echo $form->input('ticketWriteoffId');
		echo $form->input('ticketWriteoffReasonId');
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('dateRequested');
		echo $form->input('writeoffNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
