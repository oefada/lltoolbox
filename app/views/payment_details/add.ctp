<div class="paymentDetails form">
<?php echo $form->create('PaymentDetail');?>
	<fieldset>
 		<legend><?php __('Add PaymentDetail');?></legend>
	<?php
		echo '<div style="background-color:whitesmoke; border: 1px solid #a3a3a3; font-weight:bold;">';
			echo $form->input('billingAmount');
		echo '</div>';
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('creditCardNum');
		echo $form->input('expirationDate');
		echo $form->input('cvv2Value');
		echo $form->input('creditCardFirstName');
		echo $form->input('creditCardLastName');
		echo $form->input('billingAddress1');
		echo $form->input('billingCity');
		echo $form->input('billingState');
		echo $form->input('billingZip');
		echo $form->input('billingCountry');
		echo $form->input('applyToLOA', array('checked' => 'checked'));
		echo $form->input('applyLoaAuthUsername');
		echo $form->input('paymentTypeId');
		echo $form->input('paymentDate', array('type' => 'hidden'));
		echo $form->input('refundWholeTicket');
		echo $form->input('paymentProcessorId');
		echo $form->input('chargedByUsername');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List PaymentDetails', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Tickets', true), array('controller'=> 'tickets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket', true), array('controller'=> 'tickets', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Payment Types', true), array('controller'=> 'payment_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Type', true), array('controller'=> 'payment_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
