<div class="ticketCancellations form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'ticketCancellations/add'))); ?>
	<fieldset>
 		<legend><?php __('Add TicketCancellation');?></legend>
	<?php
		echo $form->input('cancellationReasonId', array('selected=' => '8'));
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('dateCancelled');
		echo $form->input('cancellationNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List TicketCancellations', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Cancellation Reasons', true), array('controller'=> 'cancellation_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Cancellation Reason', true), array('controller'=> 'cancellation_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
