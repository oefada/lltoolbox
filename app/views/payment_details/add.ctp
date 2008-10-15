<div class="paymentDetails form">
<?php echo $form->create('PaymentDetail');?>
	<fieldset>
 		<legend><?php __('Add PaymentDetail');?></legend>
	<?php
		echo $form->input('worksheetId');
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
		echo $form->input('cardProcessorName');
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
		<li><?php echo $html->link(__('List PaymentDetails', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Worksheets', true), array('controller'=> 'worksheets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Worksheet', true), array('controller'=> 'worksheets', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Payment Types', true), array('controller'=> 'payment_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Type', true), array('controller'=> 'payment_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
