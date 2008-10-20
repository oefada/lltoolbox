<div class="users index">
<h2><?php __('Users');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('title');?></th>
	<th><?php echo $paginator->sort('salutationId');?></th>
	<th><?php echo $paginator->sort('firstName');?></th>
	<th><?php echo $paginator->sort('lastName');?></th>
	<th><?php echo $paginator->sort('email');?></th>
	<th><?php echo $paginator->sort('workPhone');?></th>
	<th><?php echo $paginator->sort('mobilePhone');?></th>
	<th><?php echo $paginator->sort('homePhone');?></th>
	<th><?php echo $paginator->sort('otherPhone');?></th>
	<th><?php echo $paginator->sort('fax');?></th>
	<th><?php echo $paginator->sort('userAcquisitionSourceId');?></th>
	<th><?php echo $paginator->sort('doNotContact');?></th>
	<th><?php echo $paginator->sort('notes');?></th>
	<th><?php echo $paginator->sort('inactive');?></th>
	<th><?php echo $paginator->sort('clientNotificationEmailsActive');?></th>
	<th><?php echo $paginator->sort('registrationDate');?></th>
	<th><?php echo $paginator->sort('initialSignUpDate');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($users as $user):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $user['User']['userId']; ?>
		</td>
		<td>
			<?php echo $user['User']['title']; ?>
		</td>
		<td>
			<?php echo $html->link($user['Salutation']['salutationText'], array('controller'=> 'salutations', 'action'=>'view', $user['Salutation']['salutationId'])); ?>
		</td>
		<td>
			<?php echo $user['User']['firstName']; ?>
		</td>
		<td>
			<?php echo $user['User']['lastName']; ?>
		</td>
		<td>
			<?php echo $user['User']['email']; ?>
		</td>
		<td>
			<?php echo $user['User']['workPhone']; ?>
		</td>
		<td>
			<?php echo $user['User']['mobilePhone']; ?>
		</td>
		<td>
			<?php echo $user['User']['homePhone']; ?>
		</td>
		<td>
			<?php echo $user['User']['otherPhone']; ?>
		</td>
		<td>
			<?php echo $user['User']['fax']; ?>
		</td>
		<td>
			<?php echo $user['User']['userAcquisitionSourceId']; ?>
		</td>
		<td>
			<?php echo $user['User']['doNotContact']; ?>
		</td>
		<td>
			<?php echo $user['User']['notes']; ?>
		</td>
		<td>
			<?php echo $user['User']['inactive']; ?>
		</td>
		<td>
			<?php echo $user['User']['clientNotificationEmailsActive']; ?>
		</td>
		<td>
			<?php echo $user['User']['registrationDate']; ?>
		</td>
		<td>
			<?php echo $user['User']['initialSignUpDate']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $user['User']['userId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $user['User']['userId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $user['User']['userId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $user['User']['userId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New User', true), array('action'=>'add')); ?></li>
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
