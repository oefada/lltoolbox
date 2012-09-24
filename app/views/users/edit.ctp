<?php  $this->pageTitle = $user['User']['firstName'].' '.$user['User']['lastName'].$html2->c($user['User']['userId'], 'User Id:');?>

<script type="text/javascript">
	/***
	 * Script added by martin to allow for client notes
	 */
	jQuery(function($){
		
		$(window).ready(function(){
			load_clientNotes(<?= $user['User']['userId']; ?>);
		});
	});
	
	load_clientNotes = function( i_userId ){
		var $=jQuery;
		
		// gets clientId 
		var v_url = "/clientNotes/viewUserNotes/" + i_userId;
		
		// calls clientNotes/view to load clientNote module
		$.ajax({
			url: v_url,
			success: function(data) {
				$("#clientNoteModule").html(data);
				scrollWindow(); // auto scrolls to bottom of the clientNoteDisplay div
				document.onkeyup = KeyCheck; // watches for 'enter' keypress on the clientNoteDisplay div
				$("#clientNoteInput").focus(function(){ noteCheck(); });
			}
		});
	};
	
</script>
<div id="clientNoteModule" style="position: absolute; top: 140px; left: 850px;"></div>




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
		/*
		echo "<div style='margin-left:170px;'>";
		echo "Number of accounts associated with email: ";
		echo "<a href='/users/email/$userId?email=$email'><b>$numAccountsWithEmail</b></a>";
		echo "</div>";
		*/
	?>
	<div class='controlset'>
		<?php echo $form->input('inactive'); ?><br>
	</div>
	<div style="font-size: 14px; color: #990000; font-weight: bold; margin-left: 174px; padding: 15px 15px 13px 20px; background-color: #fdcab9; width: 280px; border-radius: 4px;">Credit on File Balance:</strong> <a href="/credit_trackings/index/query:<?= $this->data['User']['userId'] ?>">$<? if (!isset($this->data['CreditTracking'])): ?>0.00<? else: ?><?= number_format($this->data['CreditTracking']['balance'],2) ?><? endif; ?></a></div>
	
	
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
	
		<fieldset class="collapsible">
			<legend class="handle">Contact Details</legend>
			<div class="collapsibleContent related">
			<?php
				echo $form->input('workPhone');
				echo $form->input('mobilePhone');
				echo $form->input('homePhone');
				echo $form->input('otherPhone');
				echo $form->input('fax');
				?>
			</div>
		</fieldset>
		<fieldset class="collapsible">
			<legend class="handle">Web Account</legend>
				<?php
					$userSiteExtended = $user['UserSiteExtended'];
					?>
				<div class="collapsibleContent">
				<?php if (!empty($userSiteExtended['username'])):?>
					<div class="input text"><label>Username</label> <strong><?php echo $userSiteExtended['username'];?></strong></div>
					<div class="input text"><label>Registration Date</label> <?= isset($userSiteExtended['registrationDatetime']) ? $userSiteExtended['registrationDatetime'] : 'N/A'; ?></div>
					<div class="input text"><label>Last Login</label> <?php echo $userSiteExtended['lastLogin'];?></div>
					<div class="input text"><label>&nbsp;</label><?php echo $html->link('Reset Password', '/users/resetPassword/'.$userSiteExtended['userSiteExtendedId'],
					array(
						'title' => 'Reset Password',
						'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
						'complete' => 'closeModalbox()'
						), null, false); ?>
					</div>
			<?php else: ?>
				<div class='icon-yellow'>This user does not have a web account</div>
			<?php endif; ?>
				</div>
		</fieldset>
		<fieldset class="collapsible">
			<legend class="handle">Preferences</legend>
			<div class="collapsibleContent related">
			<?php
				echo $form->input('userAcquisitionSourceId');
				echo $form->input('registrationDate', array('disabled' => true));
				echo $form->input('initialSignUpDate', array('disabled' => true));
			?>
			<div class="controlset">
			<?php
				echo $form->input('doNotContact');
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
<div class="collapsible">
	<h3 class="handle"><?php __('Related User Mail Optins');?> <?=$html2->c($user['UserMailOptin']);?></h3>
	<div class="collapsibleContent related">
	<?php if (!empty($user['UserMailOptin'])):?>

	<?php 
	
	echo $form->create('User', array("action"=>"unsub"));
	echo $form->hidden("email");
	echo $form->hidden("userId");

	?>
	<br><br>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Mailing List'); ?></th>
		<th><?php __('Optin'); ?></th>
		<th><?php __('Optin Date'); ?></th>
		<th><?php __('Optout Date'); ?></th>
		<th class="actions"><?php __('Unsubscribe');?></th>
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
			<td><?php 

			$mailingListId=$userMailOptin['mailingListId'];
			$arr=NewsletterManager::getNewsletterData();
			$siteId=isset($arr[1][$mailingListId])?1:2;
			$name=$arr[$siteId][$mailingListId]['name'];
			echo $name;

			$optinDatetime=$userMailOptin['optinDatetime'];

			?></td>
			<td><?php echo $html->image($userMailOptin['optin'] ? 'tick.png' : 'cross.png');?></td>
			<td><?php echo $optinDatetime;?></td>
			<td><?php echo $userMailOptin['optoutDatetime'];?></td>
			<td><?
			if ($userMailOptin['optin']){
				$str=$mailingListId.'~'.$siteId.'~'.$optinDatetime;
				echo "<input type='checkbox' name='data[User][mailingListData][]' value='".$str."' checked>";
			}


			?> &nbsp; </td>
		</tr>
	<?php endforeach; 
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
			?>
		<tr <?=$class?>>
		<td colspan='5' align='center'>
		<?=$form->end("Unsubscribe ".$user['User']['email']." From Checked Newsletters");?>
		</td>
	</table>
<?php else: ?>
	This User has no mail opt-ins
<?php endif; ?>
	</div>
</div>
<div class="collapsible">
	<h3 class="handle"><?php __('Related User Payment Settings');?> <?=$html2->c($user['UserPaymentSetting'])?></h3>
	<div class="collapsibleContent related">
	<?php if (!empty($user['UserPaymentSetting'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Card Type'); ?></th>
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
			<td><?php echo $userPaymentSetting['ccType'];?></td>
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
<div class="collapsible">
	<h3 class="handle"><?php __('Related Contests');?> (<?=count($user['Contest'])?>)</h3>
	<div class="collapsibleContent related">
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

	<br>
	<hr>



	
</div>
