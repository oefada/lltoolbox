<div class="paymentDetails form">
<?php echo $form->create('PaymentDetail');?>
	<fieldset>
 		<legend><?php __('Add PaymentDetail');?></legend>
	<?php
		echo $form->input('worksheetId');
		echo $form->input('worksheetTypeId');
		echo $form->input('creditCardNum');
		echo $form->input('expirationDate');
		echo $form->input('cvv2Value');
		echo $form->input('creditCardName');
		echo $form->input('billingAddress1');
		echo $form->input('billingCity');
		echo $form->input('billingState');
		echo $form->input('billingZip');
		echo $form->input('billingCountry');
		echo $form->input('billingAmount');
		echo $form->input('applyToLOA');
		echo $form->input('applyLoaAuthUserId');
		echo $form->input('settlementId');
		echo $form->input('paymentTypeId');
		echo $form->input('paymentDate');
		echo $form->input('wholeRefundId');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List PaymentDetails', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Worksheets', true), array('controller'=> 'worksheets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Worksheet', true), array('controller'=> 'worksheets', 'action'=>'add')); ?> </li>
	</ul>
</div>
