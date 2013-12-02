<div class="refundRequests form">
	<?php echo $form->create('RefundRequest', array('url'=> "/refund_requests/add/{$refundInfo['ticket']['Ticket']['ticketId']}", 'type'=> 'post'));?>
	<?= $form->hidden('ticketId'); ?>
	
	<fieldset>
	<legend><?php __('Add Refund Request');?></legend>

	<?php echo $this->renderElement('../refund_requests/_top_info'); ?>
	<?php echo $this->renderElement('../refund_requests/_edit_form'); ?>

	<?php echo $form->end('Submit');?>
	</fieldset>
	
</div>
