<div class="userPaymentSettings form">
<?php echo $form->create('UserPaymentSetting');?>
	<fieldset>
 		<legend><?php __('Add UserPaymentSetting');?></legend>
	<?php
		echo $form->input('userPaymentSettingId');
		echo $form->input('ccNumber');
		echo $form->input('userId');
		echo $form->input('ccExpiration');
		echo $form->input('cvv2');
		echo $form->input('nameOnCard');
		echo $form->input('routingNumber');
		echo $form->input('accountNumber');
		echo $form->input('nameOnAccount');
		echo $form->input('paymentTypeId');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List UserPaymentSettings', true), array('action'=>'index'));?></li>
	</ul>
</div>
