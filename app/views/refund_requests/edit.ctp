<div class="refundRequests form">
	<?php echo $form->create('RefundRequest');?>

	<fieldset>
	<legend><?php __('Edit Refund Request');?></legend>

	<div class="input text">
		<label>Status</label>
		<?= $this->data['RefundRequestStatus']['description']; ?>
	</div>
		
	<?php echo $this->renderElement('../refund_requests/_top_info'); ?>
	<?php echo $this->renderElement('../refund_requests/_edit_form'); ?>

	<?php echo $form->end('Submit');?>
	</fieldset>
	
</div>

