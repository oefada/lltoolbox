<?php  $this->pageTitle = __('User', true);?>
<div class="users form">
<?php echo $form->create('User');?>
<?php if ($user['User']['inactive']): ?>
	<div class='icon-yellow'>This user is inactive</div>
<?php endif ?>
	<fieldset>
 		<legend><?php __('Edit User');?></legend>
	<?php
		echo $form->input('userId');
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
<div class="related">
	<h3 class="collapsible"><b class="handle"></b><span class="handle"><?php __('Related User Mail Optins');?>  (<?=count($user['UserMailOptin'])?>)</span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserMailOptin'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('UserMailOptinId'); ?></th>
		<th><?php __('MailingListId'); ?></th>
		<th><?php __('Optin'); ?></th>
		<th><?php __('OptinDate'); ?></th>
		<th><?php __('OptoutDate'); ?></th>
		<th><?php __('UserId'); ?></th>
		<th><?php __('StatNumPurchases'); ?></th>
		<th><?php __('StatLastPurchase'); ?></th>
		<th><?php __('StatLastUpdated'); ?></th>
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
			<td><?php echo $userMailOptin['userMailOptinId'];?></td>
			<td><?php echo $userMailOptin['mailingListId'];?></td>
			<td><?php echo $userMailOptin['optin'];?></td>
			<td><?php echo $userMailOptin['optinDate'];?></td>
			<td><?php echo $userMailOptin['optoutDate'];?></td>
			<td><?php echo $userMailOptin['userId'];?></td>
			<td><?php echo $userMailOptin['statNumPurchases'];?></td>
			<td><?php echo $userMailOptin['statLastPurchase'];?></td>
			<td><?php echo $userMailOptin['statLastUpdated'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'user_mail_optins', 'action'=>'view', $userMailOptin['userMailOptinId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'user_mail_optins', 'action'=>'edit', $userMailOptin['userMailOptinId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'user_mail_optins', 'action'=>'delete', $userMailOptin['userMailOptinId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $userMailOptin['userMailOptinId'])); ?>
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
	<h3 class="collapsible"><b class="handle"></b><span class="handle"><?php __('Related User Site Extendeds');?>  (<?=count($user['UserSiteExtended'])?>)</span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserSiteExtended'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('UserSiteExtendedId'); ?></th>
		<th><?php __('UserId'); ?></th>
		<th><?php __('Username'); ?></th>
		<th><?php __('Password'); ?></th>
		<th><?php __('InitialSubscribeDate'); ?></th>
		<th><?php __('RegistrationDate'); ?></th>
		<th><?php __('LastLogin'); ?></th>
		<th><?php __('LastBidDate'); ?></th>
		<th><?php __('NumPurchases'); ?></th>
		<th><?php __('NumBids'); ?></th>
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
			<td><?php echo $userSiteExtended['userSiteExtendedId'];?></td>
			<td><?php echo $userSiteExtended['userId'];?></td>
			<td><?php echo $userSiteExtended['username'];?></td>
			<td><?php echo $userSiteExtended['password'];?></td>
			<td><?php echo $userSiteExtended['initialSubscribeDate'];?></td>
			<td><?php echo $userSiteExtended['registrationDate'];?></td>
			<td><?php echo $userSiteExtended['lastLogin'];?></td>
			<td><?php echo $userSiteExtended['lastBidDate'];?></td>
			<td><?php echo $userSiteExtended['numPurchases'];?></td>
			<td><?php echo $userSiteExtended['numBids'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'user_site_extendeds', 'action'=>'view', $userSiteExtended['userSiteExtendedId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'user_site_extendeds', 'action'=>'edit', $userSiteExtended['userSiteExtendedId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'user_site_extendeds', 'action'=>'delete', $userSiteExtended['userSiteExtendedId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $userSiteExtended['userSiteExtendedId'])); ?>
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
	<h3 class="collapsible"><b class="handle"></b><span class="handle"><?php __('Related User Payment Settings');?>  (<?=count($user['UserPaymentSetting'])?>)</span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserPaymentSetting'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('UserPaymentSettingId'); ?></th>
		<th><?php __('CcNumber'); ?></th>
		<th><?php __('UserId'); ?></th>
		<th><?php __('CcExpiration'); ?></th>
		<th><?php __('Cvv2'); ?></th>
		<th><?php __('NameOnCard'); ?></th>
		<th><?php __('RoutingNumber'); ?></th>
		<th><?php __('AccountNumber'); ?></th>
		<th><?php __('NameOnAccount'); ?></th>
		<th><?php __('PaymentTypeId'); ?></th>
		<th><?php __('ExpYear'); ?></th>
		<th><?php __('ExpMonth'); ?></th>
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
			<td><?php echo $userPaymentSetting['userPaymentSettingId'];?></td>
			<td><?php echo $userPaymentSetting['ccNumber'];?></td>
			<td><?php echo $userPaymentSetting['userId'];?></td>
			<td><?php echo $userPaymentSetting['ccExpiration'];?></td>
			<td><?php echo $userPaymentSetting['cvv2'];?></td>
			<td><?php echo $userPaymentSetting['nameOnCard'];?></td>
			<td><?php echo $userPaymentSetting['routingNumber'];?></td>
			<td><?php echo $userPaymentSetting['accountNumber'];?></td>
			<td><?php echo $userPaymentSetting['nameOnAccount'];?></td>
			<td><?php echo $userPaymentSetting['paymentTypeId'];?></td>
			<td><?php echo $userPaymentSetting['expYear'];?></td>
			<td><?php echo $userPaymentSetting['expMonth'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'user_payment_settings', 'action'=>'view', $userPaymentSetting['userPaymentSettingId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'user_payment_settings', 'action'=>'edit', $userPaymentSetting['userPaymentSettingId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'user_payment_settings', 'action'=>'delete', $userPaymentSetting['userPaymentSettingId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $userPaymentSetting['userPaymentSettingId'])); ?>
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
	<h3 class="collapsible"><b class="handle"></b><span class="handle"><?php __('Related User Preferences');?>  (<?=count($user['UserPreference'])?>)</span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['UserPreference'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('UserPreferenceId'); ?></th>
		<th><?php __('PreferenceTypeId'); ?></th>
		<th><?php __('UserId'); ?></th>
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
			<td><?php echo $userPreference['userPreferenceId'];?></td>
			<td><?php echo $userPreference['preferenceTypeId'];?></td>
			<td><?php echo $userPreference['userId'];?></td>
			<td><?php echo $userPreference['preferenceValue'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'user_preferences', 'action'=>'view', $userPreference['userPreferenceId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'user_preferences', 'action'=>'edit', $userPreference['userPreferenceId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'user_preferences', 'action'=>'delete', $userPreference['userPreferenceId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $userPreference['userPreferenceId'])); ?>
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
	<h3 class="collapsible"><b class="handle"></b><span class="handle"><?php __('Related Bids');?>  (<?=count($user['Bid'])?>)</span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['Bid'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('BidId'); ?></th>
		<th><?php __('OfferId'); ?></th>
		<th><?php __('UserId'); ?></th>
		<th><?php __('BidDateTime'); ?></th>
		<th><?php __('BidAmount'); ?></th>
		<th><?php __('MaxBid'); ?></th>
		<th><?php __('AutoRebid'); ?></th>
		<th><?php __('BidInactive'); ?></th>
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
			<td><?php echo $bid['userId'];?></td>
			<td><?php echo $bid['bidDateTime'];?></td>
			<td><?php echo $bid['bidAmount'];?></td>
			<td><?php echo $bid['maxBid'];?></td>
			<td><?php echo $bid['autoRebid'];?></td>
			<td><?php echo $bid['bidInactive'];?></td>
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
	<h3 class="collapsible"><b class="handle"></b><span class="handle"><?php __('Related Addresses');?>  (<?=count($user['Address'])?>)</span></h3>
	<div class="collapsibleContent">
	<?php if (!empty($user['Address'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('AddressId'); ?></th>
		<th><?php __('ClientId'); ?></th>
		<th><?php __('UserId'); ?></th>
		<th><?php __('AddressTypeId'); ?></th>
		<th><?php __('CityId'); ?></th>
		<th><?php __('StateId'); ?></th>
		<th><?php __('CountryId'); ?></th>
		<th><?php __('Address1'); ?></th>
		<th><?php __('Address2'); ?></th>
		<th><?php __('Address3'); ?></th>
		<th><?php __('City'); ?></th>
		<th><?php __('StateName'); ?></th>
		<th><?php __('CountryName'); ?></th>
		<th><?php __('PostalCode'); ?></th>
		<th><?php __('DefaultAddress'); ?></th>
		<th><?php __('Latitude'); ?></th>
		<th><?php __('Longitude'); ?></th>
		<th><?php __('Countrytext'); ?></th>
		<th><?php __('StateCode'); ?></th>
		<th><?php __('CountryCode'); ?></th>
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
			<td><?php echo $address['addressId'];?></td>
			<td><?php echo $address['clientId'];?></td>
			<td><?php echo $address['userId'];?></td>
			<td><?php echo $address['addressTypeId'];?></td>
			<td><?php echo $address['cityId'];?></td>
			<td><?php echo $address['stateId'];?></td>
			<td><?php echo $address['countryId'];?></td>
			<td><?php echo $address['address1'];?></td>
			<td><?php echo $address['address2'];?></td>
			<td><?php echo $address['address3'];?></td>
			<td><?php echo $address['city'];?></td>
			<td><?php echo $address['stateName'];?></td>
			<td><?php echo $address['countryName'];?></td>
			<td><?php echo $address['postalCode'];?></td>
			<td><?php echo $address['defaultAddress'];?></td>
			<td><?php echo $address['latitude'];?></td>
			<td><?php echo $address['longitude'];?></td>
			<td><?php echo $address['countrytext'];?></td>
			<td><?php echo $address['stateCode'];?></td>
			<td><?php echo $address['countryCode'];?></td>
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
	<h3 class="collapsible"><b class="handle"></b><span class="handle"><?php __('Related User Acquisition Sources');?>  (<?=count($user['UserAcquisitionSource'])?>)</span></h3>
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
	<h3 class="collapsible"><b class="handle"></b><span class="handle"><?php __('Related Contests');?> (<?=count($user['Contest'])?>)</span></h3>
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