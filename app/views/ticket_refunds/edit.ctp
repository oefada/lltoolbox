<div class="ticketRefunds form">
    <?php $session->flash();
	$session->flash('error');
	?>
<?php 
// echo $form->create('TicketRefund');
// echo $ajax->form('edit', 'post', array('url' => "/scheduling_masters/edit/{$this->data['SchedulingMaster']['schedulingMasterId']}", 'update' => 'MB_content', 'model' => 'SchedulingMaster', 'complete' => 'Modalbox.activate(); closeModalbox()', 'before' => 'Modalbox.deactivate();', 'indicator' => 'spinner'));
echo $ajax->form('edit', 'post', array('url' => "/ticket_refunds/{$this->data['TicketRefund']['ticketRefundId']}", 'update' => 'MB_content', 'model' => 'TicketRefund', 'complete' => 'Modalbox.activate(); closeModalbox()', 'before' => 'Modalbox.deactivate();', 'indicator' => 'spinner'));
?>
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

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>
