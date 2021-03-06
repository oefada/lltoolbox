<div class="paymentDetails form">
<?php echo $form->create('PaymentDetail');?>
	<fieldset>
 		<legend><?php __('Edit PaymentDetail');?></legend>
	<?php
		echo $form->input('paymentDetailId');
		echo $form->input('ticketId');
		echo $form->input('creditCardNum');
		echo $form->input('expirationDate');
		echo $form->input('cvv2Value');
		echo $form->input('creditCardFirstName');
		echo $form->input('billingAddress1');
		echo $form->input('billingCity');
		echo $form->input('billingState');
		echo $form->input('billingZip');
		echo $form->input('billingCountry');
		echo $form->input('billingAmount');
		echo $form->input('applyToLOA');
		echo $form->input('applyLoaAuthUsername');
		echo $form->input('paymentTypeId');
		echo $form->input('paymentDate');
		echo $form->input('refundWholeTicket');
		echo $form->input('paymentProcessorId');
		echo $form->input('ppResponseDate');
		echo $form->input('ppTransactionId');
		echo $form->input('ppApprovalStatus');
		echo $form->input('ppApprovalCode');
		echo $form->input('ppAvsCode');
		echo $form->input('ppResponseText');
		echo $form->input('ppReasonCode');
		echo $form->input('autoProcessed');
		echo $form->input('successfulCharge');
		echo $form->input('chargedByUsername');
		echo $form->input('creditCardLastName');
		echo $form->input('ppResponseSubcode');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('PaymentDetail.paymentDetailId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('PaymentDetail.paymentDetailId'))); ?></li>
		<li><?php echo $html->link(__('List PaymentDetails', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Tickets', true), array('controller'=> 'tickets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket', true), array('controller'=> 'tickets', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Payment Types', true), array('controller'=> 'payment_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Type', true), array('controller'=> 'payment_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
