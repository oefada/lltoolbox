<?php  $this->pageTitle = __('User', true);?>
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
			<legend class="collapsible"><span class="handle">Preferences</span></legend>
			<div class="collapsibleContent">
			<?php
				echo $form->input('userAcquisitionSourceId');
				echo $form->input('registrationDate', array('disabled' => true));
				echo $form->input('initialSignUpDate', array('disabled' => true));
				echo $form->input('doNotContact');
				echo $form->input('inactive');
				echo $form->input('clientNotificationEmailsActive');
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
	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New User Mail Optin', true), array('controller'=> 'user_mail_optins', 'action'=>'add'));?> </li>
		</ul>
	</div>
	</div>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related User Referrals');?></span><?=$html2->c($user['UserReferral'])?></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserReferral'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('ReferralDate'); ?></th>
		<th><?php __('ReferredFirstName'); ?></th>
		<th><?php __('ReferredLastName'); ?></th>
		<th><?php __('ReferredEmailAddress'); ?></th>
		<th><?php __('UserEmail'); ?></th>
		<th><?php __('ContestId'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['UserReferral'] as $userReferral):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $userReferral['referralDate'];?></td>
			<td><?php echo $userReferral['referredFirstName'];?></td>
			<td><?php echo $userReferral['referredLastName'];?></td>
			<td><?php echo $userReferral['referredEmailAddress'];?></td>
			<td><?php echo $userReferral['userEmail'];?></td>
			<td><?php echo $userReferral['contestId'];?></td>
			
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'user_referrals', 'action'=>'view', $userReferral['userReferralId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'user_referrals', 'action'=>'edit', $userReferral['userReferralId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'user_referrals', 'action'=>'delete', $userReferral['userReferralId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $userReferral['userReferralId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New User Referral', true), array('controller'=> 'user_referrals', 'action'=>'add'));?> </li>
		</ul>
	</div>
	</div>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related User Site Extendeds');?></span> <?=$html2->c($user['UserSiteExtended'])?></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserSiteExtended'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Username'); ?></th>
		<th><?php __('Initial Subscribe Date'); ?></th>
		<th><?php __('Registration Date'); ?></th>
		<th><?php __('Last Login'); ?></th>
		<th><?php __('Last Bid Date'); ?></th>
		<th><?php __('Num Purchases'); ?></th>
		<th><?php __('Num Bids'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['UserSiteExtended'] as $userSiteExtended):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $userSiteExtended['username'];?></td>
			<td><?php echo $userSiteExtended['initialSubscribeDate'];?></td>
			<td><?php echo $userSiteExtended['registrationDate'];?></td>
			<td><?php echo $userSiteExtended['lastLogin'];?></td>
			<td><?php echo $userSiteExtended['lastBidDate'];?></td>
			<td><?php echo $userSiteExtended['numPurchases'];?></td>
			<td><?php echo $userSiteExtended['numBids'];?></td>
			<td class="actions">
				<?php echo $html->link(__('Edit', true), array('controller'=> 'user_site_extendeds', 'action'=>'edit', $userSiteExtended['userSiteExtendedId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New User Site Extended', true), array('controller'=> 'user_site_extendeds', 'action'=>'add'));?> </li>
		</ul>
	</div>
	</div>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related User Payment Settings');?></span> <?=$html2->c($user['UserPaymentSetting'])?></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserPaymentSetting'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Cc Expiration'); ?></th>
		<th><?php __('Name On Card'); ?></th>
		<th><?php __('Routing Number'); ?></th>
		<th><?php __('Account Number'); ?></th>
		<th><?php __('Name On Account'); ?></th>
		<th><?php __('Payment Type'); ?></th>
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
			<td><?php echo $userPaymentSetting['ccExpiration'];?></td>
			<td><?php echo $userPaymentSetting['nameOnCard'];?></td>
			<td><?php echo $userPaymentSetting['routingNumber'];?></td>
			<td><?php echo $userPaymentSetting['accountNumber'];?></td>
			<td><?php echo $userPaymentSetting['nameOnAccount'];?></td>
			<td><?php echo $paymentTypes[$userPaymentSetting['paymentTypeId']];?></td>
			<td><?php echo $userPaymentSetting['expYear'];?></td>
			<td><?php echo $userPaymentSetting['expMonth'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View Card Number', true), array('controller'=> 'UserPaymentSettings', 'action'=>'view', $userPaymentSetting['userPaymentSettingId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New User Payment Setting', true), array('controller'=> 'user_payment_settings', 'action'=>'add'));?> </li>
		</ul>
	</div>
	</div>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related User Preferences');?></span><?=$html2->c($user['UserPreference'])?></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserPreference'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('PreferenceTypeId'); ?></th>
		<th><?php __('PreferenceValue'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['UserPreference'] as $userPreference):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $userPreference['preferenceTypeId'];?></td>
			<td><?php echo $userPreference['preferenceValue'];?></td>
			<td class="actions">
				<?php echo $html->link(__('Edit', true), array('controller'=> 'user_preferences', 'action'=>'edit', $userPreference['userPreferenceId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New User Preference', true), array('controller'=> 'user_preferences', 'action'=>'add'));?> </li>
		</ul>
	</div>
	</div>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related Bids');?></span> <?=$html2->c($user['Bid'])?></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['Bid'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('BidId'); ?></th>
		<th><?php __('OfferId'); ?></th>
		<th><?php __('Bid Date'); ?></th>
		<th><?php __('Amount'); ?></th>
		<th><?php __('Max Bid'); ?></th>
		<th><?php __('Auto Rebid'); ?></th>
		<th><?php __('Bid Inactive'); ?></th>
		<th><?php __('Note'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Bid'] as $bid):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $bid['bidId'];?></td>
			<td><?php echo $bid['offerId'];?></td>
			<td><?php echo $bid['bidDateTime'];?></td>
			<td><?php echo $bid['bidAmount'];?></td>
			<td><?php echo $bid['maxBid'];?></td>
			<td><?php echo $html->image($bid['autoRebid'] ? 'tick.png' : 'cross.png'); ?></td>
			<td><?php echo $html->image($bid['bidInactive'] ? 'tick.png' : 'cross.png'); ?></td>
			<td><?php echo $bid['note'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'bids', 'action'=>'view', $bid['bidId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'bids', 'action'=>'edit', $bid['bidId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'bids', 'action'=>'delete', $bid['bidId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $bid['bidId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Bid', true), array('controller'=> 'bids', 'action'=>'add'));?> </li>
		</ul>
	</div>
	</div>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related Addresses');?>  (<?=count($user['Address'])?>)</span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['Address'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('AddressTypeId'); ?></th>
		<th><?php __('Address'); ?></th>
		<th><?php __('Default Address?'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Address'] as $address):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $addressTypes[$address['addressTypeId']];?></td>
			<td>
			<?php if ($address['address1']): ?>
				<?php echo $address['address1'];?><br />
			<?php endif ?>
			<?php if ($address['address2']): ?>
				<?php echo $address['address2'];?><br />
			<?php endif ?>
			<?php if ($address['address3']): ?>
				<?php echo $address['address3'];?><br />
			<?php endif ?>
			<?php if ($address['stateName']): ?>
				<?php echo $address['stateName'];?>
			<?php endif ?>
			<?php if ($address['city']): ?>
				<?php echo ', '.$address['city'];?><br />
			<?php endif ?>
			<?php if ($address['countryName'] || $address['countryCode']): ?>
				<?php echo $address['countryName'];?> <?php echo $address['countryCode']?>
			<?php endif ?>
			</td>
			<td><?php echo $html->image($address['defaultAddress'] ? 'tick.png' : 'cross.png'); ?>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'addresses', 'action'=>'view', $address['addressId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'addresses', 'action'=>'edit', $address['addressId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'addresses', 'action'=>'delete', $address['addressId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $address['addressId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Address', true), array('controller'=> 'addresses', 'action'=>'add'));?> </li>
		</ul>
	</div>
	</div>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle"><?php __('Related User Acquisition Sources');?>  (<?=count($user['UserAcquisitionSource'])?>)</span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserAcquisitionSource'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('UserAcquisitionSourceId'); ?></th>
		<th><?php __('UserAqcuisitionSourceName'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['UserAcquisitionSource'] as $userAcquisitionSource):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $userAcquisitionSource['userAcquisitionSourceId'];?></td>
			<td><?php echo $userAcquisitionSource['userAqcuisitionSourceName'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'user_acquisition_sources', 'action'=>'view', $userAcquisitionSource['userAcquisitionSourceId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'user_acquisition_sources', 'action'=>'edit', $userAcquisitionSource['userAcquisitionSourceId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'user_acquisition_sources', 'action'=>'delete', $userAcquisitionSource['userAcquisitionSourceId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $userAcquisitionSource['userAcquisitionSourceId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New User Acquisition Source', true), array('controller'=> 'user_acquisition_sources', 'action'=>'add'));?> </li>
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