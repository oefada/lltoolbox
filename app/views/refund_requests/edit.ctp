<div class="refundRequests form">
	<?php echo $form->create('RefundRequest');?>
	<?= $form->hidden('refundRequestId'); ?>

	<fieldset>
	<legend><?php __('Edit Refund Request');?></legend>

	<div class="input text">
		<label>Status</label>
		<?= $this->data['RefundRequestStatus']['description']; ?>
	</div>
		
	<?php echo $this->renderElement('../refund_requests/_top_info'); ?>
	<?php echo $this->renderElement('../refund_requests/_edit_form'); ?>
	
	<hr class="refundRequestDivider" />
	<div style="color:#880A00; font-weight: bold;">Accounting Purposes Only</div>
	
	<?= $form->input('keepOrRemit', array('label' => 'Keep / Remit', 'options' => $keepOrRemitList, 'empty' => '-- ')); ?>
	<?= $form->input('ccRefundedFlag', array('type' => 'checkbox', 'label' => 'CC Refunded')); ?>
	<?= $form->input('cofPostedFlag', array('type' => 'checkbox', 'label' => 'CC Posted')); ?>
	<?= $form->input('toolboxAllocatedFlag', array('type' => 'checkbox', 'label' => 'Toolbox Allocated')); ?>
	<?= $form->input('caCheckRequestFlag', array('type' => 'checkbox', 'label' => 'CA Check Request')); ?>
	<?= $form->input('caUpdateFlag', array('type' => 'checkbox', 'label' => 'CA Update')); ?>
	<?= $form->input('propertyPaidFlag', array('type' => 'checkbox', 'label' => 'Property Paid')); ?>
	<?= $form->input('propertyPaidDate', array('label' => 'Date Paid',  'empty' => '-- ', 'minYear'=>date('Y')-2, 'maxYear'=>date('Y')+2)); ?>

	<? if ($this->data['RefundRequestStatus']['refundRequestStatusId'] == 2) { ?>
		<?= $form->input('markCompleted', array('type' => 'checkbox', 'label' => 'SET COMPLETED')); ?>
	<? } ?>
	
	<?php echo $form->end('Submit');?>
	</fieldset>
	
</div>

