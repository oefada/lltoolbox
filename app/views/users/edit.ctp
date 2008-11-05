<?php  $this->pageTitle = $user['User']['firstName'].' '.$user['User']['lastName'].$html2->c($user['User']['userId'], 'User Id:');?>
<div class="users form">
<?php echo $form->create('User');?>
<?php if ($user['User']['inactive']): ?>
	<div class='icon-yellow'>This user is inactive</div>
<?php endif ?>
	<fieldset>
	<?php
		echo $form->input('userId');
		echo $form->input('salutationId');
		echo $form->input('title');
		echo $form->input('firstName');
		echo $form->input('lastName');		
		echo $form->input('email');
	?>
		<fieldset>
			<legend class="collapsible"><span class="handle">Contact Details</span></legend>
			<div class="collapsibleContent">
			<?php
				echo $form->input('workPhone');
				echo $form->input('mobilePhone');
				echo $form->input('homePhone');
				echo $form->input('otherPhone');
				echo $form->input('fax');
				?>
			</div>
		</fieldset>
		<fieldset>
			<legend class="collapsible"><span class="handle">Web Account</span></legend>
				<?php
					$userSiteExtended = $user['UserSiteExtended'];
					?>
				<div class="collapsibleContent">
				<?php if (!empty($userSiteExtended['username'])):?>
					<div class="input text"><label>Username</label> <strong><?php echo $userSiteExtended['username'];?></strong></div>
					<div class="input text"><label>Initial Subscribe Date</label> <?php echo $userSiteExtended['initialSubscribeDate'];?></div>
					<div class="input text"><label>Registration Date</label> <?php echo $userSiteExtended['registrationDate'];?></div>
					<div class="input text"><label>Last Login</label> <?php echo $userSiteExtended['lastLogin'];?></div>
					<div class="input text"><label>&nbsp;</label><?php echo $html->link('Reset Password', '/users/resetPassword/'.$userSiteExtended['userSiteExtendedId'],
					array(
						'title' => 'Reset Password',
						'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
						'complete' => 'closeModalbox()'
						), null, false); ?>
					</div>
			<?php else: ?>
				<h3>This user does not have a web account</h3>
			<?php endif; ?>
				</div>
		</fieldset>
		<fieldset>
			<legend class="collapsible"><span class="handle">Preferences</span></legend>
			<div class="collapsibleContent">
			<?php
				echo $form->input('userAcquisitionSourceId');
				echo $form->input('registrationDate', array('disabled' => true));
				echo $form->input('initialSignUpDate', array('disabled' => true));
			?>
			<div class="controlset">
			<?php
				echo $form->input('doNotContact');
				echo $form->input('inactive');
				echo $form->input('clientNotificationEmailsActive');
			?>
			</div>
			<?php
				echo $form->input('notes');
			?>
			</div>
		</fieldset>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related User Mail Optins');?></span> <?=$html2->c($user['UserMailOptin']);?></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserMailOptin'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('MailingListId'); ?></th>
		<th><?php __('Optin'); ?></th>
		<th><?php __('Optin Date'); ?></th>
		<th><?php __('Optout Date'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['UserMailOptin'] as $userMailOptin):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $userMailOptin['mailingListId'];?></td>
			<td><?php echo $html->image($userMailOptin['optin'] ? 'tick.png' : 'cross.png');?></td>
			<td><?php echo $userMailOptin['optinDate'];?></td>
			<td><?php echo $userMailOptin['optoutDate'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View Details', true), array('controller'=> 'user_mail_optins', 'action'=>'edit', $userMailOptin['userMailOptinId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>
	</div>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related User Payment Settings');?></span> <?=$html2->c($user['UserPaymentSetting'])?></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserPaymentSetting'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Payment Type'); ?></th>
		<th><?php __('Cc Number'); ?></th>
		<th><?php __('Name On Card'); ?></th>
		<th><?php __('Exp Year'); ?></th>
		<th><?php __('Exp Month'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['UserPaymentSetting'] as $userPaymentSetting):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $paymentTypes[$userPaymentSetting['paymentTypeId']];?></td>
			<td><?php echo $userPaymentSetting['ccNumber'];?></td>
			<td><?php echo $userPaymentSetting['nameOnCard'];?></td>
			<td><?php echo $userPaymentSetting['expYear'];?></td>
			<td><?php echo $userPaymentSetting['expMonth'];?></td>
			<td class="actions">
				<?php
				echo $html->link('Edit Expiration Date',
								'/users/'.$user['User']['userId'].'/userPaymentSettings/edit/'.$userPaymentSetting['userPaymentSettingId'],
								array(
									'title' => 'Edit Credit Card Expiration Date',
									'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
									'complete' => 'closeModalbox()'
									),
								null,
								false
								);
				?>
				<?php echo $html->link(__('Delete', true), '/users/'.$user['User']['userId'].'/user_payment_settings/delete/'.$userPaymentSetting['userPaymentSettingId']); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link('Add User Payment Setting', '/users/'.$user['User']['userId'].'/user_payment_settings/add',
			array(
				'title' => 'New User Payment Setting',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				), null, false); ?> </li>
		</ul>
	</div>
	</div>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related Contests');?> (<?=count($user['Contest'])?>)</span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['Contest'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('ContestId'); ?></th>
		<th><?php __('ContestName'); ?></th>
		<th><?php __('DescriptionText'); ?></th>
		<th><?php __('Url'); ?></th>
		<th><?php __('StartDate'); ?></th>
		<th><?php __('EndDate'); ?></th>
		<th><?php __('DisplayText'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Contest'] as $contest):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $contest['contestId'];?></td>
			<td><?php echo $contest['contestName'];?></td>
			<td><?php echo $contest['descriptionText'];?></td>
			<td><?php echo $contest['url'];?></td>
			<td><?php echo $contest['startDate'];?></td>
			<td><?php echo $contest['endDate'];?></td>
			<td><?php echo $contest['displayText'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'contests', 'action'=>'view', $contest['contestId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'contests', 'action'=>'edit', $contest['contestId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'contests', 'action'=>'delete', $contest['contestId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $contest['contestId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Contest', true), array('controller'=> 'contests', 'action'=>'add'));?> </li>
		</ul>
	</div>
	</div>
</div>