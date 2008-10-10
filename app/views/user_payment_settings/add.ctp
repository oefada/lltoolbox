<div class="userPaymentSettings form">
<?php echo $form->create('UserPaymentSetting');?>
	<fieldset>
 		<legend><?php __('Add UserPaymentSetting');?></legend>
	<?php
		echo $form->input('ccNumber');
		echo $form->input('userId');
		echo $form->input('ccExpiration');
		echo $form->input('cvv2');
		echo $form->input('nameOnCard');
		echo $form->input('routingNumber');
		echo $form->input('accountNumber');
		echo $form->input('nameOnAccount');
		echo $form->input('paymentTypeId');
		echo $form->input('cc_year');
		echo $form->input('cc_month');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List UserPaymentSettings', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Payment Types', true), array('controller'=> 'payment_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Type', true), array('controller'=> 'payment_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
