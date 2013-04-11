<div class="userPaymentSettings form">
<?php echo $ajax->form('add', 'post', array('url' => "/users/{$this->data['UserPaymentSetting']['userId']}/userPaymentSettings/add", 'update' => 'MB_content', 'model' => 'UserPaymentSetting', 	'complete' => 'closeModalbox()'));?>
	<fieldset>
 		<legend><?php __('Add UserPaymentSetting');?></legend>
	<?php
		echo $form->input('paymentTypeId');
		echo $form->input('nameOnCard');
		echo $form->input('ccNumber');
		echo $form->input('expYear');
		echo $form->input('expMonth');
		echo $form->input('userId', array('type' => 'hidden'));
		
		echo $form->input('address1');
		echo $form->input('address2');
		echo $form->input('city');
		echo $form->input('state');
		echo $form->input('country', array('options'=> $countries));
		echo $form->input('postalCode');		
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>