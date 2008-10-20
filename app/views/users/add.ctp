<div class="users form">
<?php echo $form->create('User');?>
	<fieldset>
 		<legend><?php __('Add User');?></legend>
	<?php
		echo $form->input('title');
		echo $form->input('salutationId');
		echo $form->input('firstName');
		echo $form->input('lastName');
		echo $form->input('email');
		echo $form->input('workPhone');
		echo $form->input('mobilePhone');
		echo $form->input('homePhone');
		echo $form->input('otherPhone');
		echo $form->input('fax');
		echo $form->input('userAcquisitionSourceId');
		echo $form->input('doNotContact');
		echo $form->input('notes');
		echo $form->input('inactive');
		echo $form->input('clientNotificationEmailsActive');
		echo $form->input('registrationDate');
		echo $form->input('initialSignUpDate');
		echo $form->input('Contest');
		echo $form->input('Client');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Users', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Salutations', true), array('controller'=> 'salutations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Salutation', true), array('controller'=> 'salutations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List User Mail Optins', true), array('controller'=> 'user_mail_optins', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User Mail Optin', true), array('controller'=> 'user_mail_optins', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List User Referrals', true), array('controller'=> 'user_referrals', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User Referral', true), array('controller'=> 'user_referrals', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List User Site Extendeds', true), array('controller'=> 'user_site_extendeds', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User Site Extended', true), array('controller'=> 'user_site_extendeds', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List User Payment Settings', true), array('controller'=> 'user_payment_settings', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User Payment Setting', true), array('controller'=> 'user_payment_settings', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List User Preferences', true), array('controller'=> 'user_preferences', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User Preference', true), array('controller'=> 'user_preferences', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Bids', true), array('controller'=> 'bids', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Bid', true), array('controller'=> 'bids', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Addresses', true), array('controller'=> 'addresses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Address', true), array('controller'=> 'addresses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List User Acquisition Sources', true), array('controller'=> 'user_acquisition_sources', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User Acquisition Source', true), array('controller'=> 'user_acquisition_sources', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Contests', true), array('controller'=> 'contests', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Contest', true), array('controller'=> 'contests', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
	</ul>
</div>
