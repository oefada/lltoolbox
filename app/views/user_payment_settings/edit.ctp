<div class="userPaymentSettings form">
<?php echo $ajax->form('edit', 'post', array('url' => "/users/{$this->data['UserPaymentSetting']['userId']}/userPaymentSettings/edit/{$this->data['UserPaymentSetting']['userPaymentSettingId']}", 'update' => 'MB_content', 'model' => 'UserPaymentSetting', 	'complete' => 'closeModalbox()'));?>
	<fieldset>
 		<legend><?php __('Edit UserPaymentSetting');?></legend>
	<?php
		echo $form->input('userPaymentSettingId');
		//echo $form->input('nameOnCard');
		//echo $form->input('ccNumber', array('disabled' => 'disabled'));
		echo $form->input('userId', array('type' => 'hidden'));
		echo $form->input('expMonth');
		echo $form->input('expYear');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>