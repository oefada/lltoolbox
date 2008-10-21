<div class="ticketRefunds form">
<?php echo $form->create('TicketRefund');?>
	<fieldset>
 		<legend><?php __('Edit TicketRefund');?></legend>
	<?php
		echo $form->input('ticketRefundId');
		echo $form->input('refundReasonId');
		echo $form->input('ticketId');
		echo $form->input('dateRefunded');
		echo $form->input('amountRefunded');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('TicketRefund.ticketRefundId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('TicketRefund.ticketRefundId'))); ?></li>
		<li><?php echo $html->link(__('List TicketRefunds', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Refund Reasons', true), array('controller'=> 'refund_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Refund Reason', true), array('controller'=> 'refund_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
