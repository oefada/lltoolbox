<?php  $this->pageTitle = 'Payment Processor ' . $html2->c($ticket['Ticket']['ticketId'], 'Ticket Id:');?>
<?php $session->flash();
	$session->flash('error');
?>

<script language="javascript">
<!--
function confirmSubmit()
{
	var agree = confirm("Are you sure you want to continue?  Clicking 'Ok' will submit this payment.");
	if (agree)
		return true ;
	else
		return false ;
	}
// -->
</script>

<div class="paymentDetails form">
<?php echo $form->create('PaymentDetail', array('url' => "/tickets/{$ticket['Ticket']['ticketId']}/paymentDetails/add", 'onsubmit' => "return confirmSubmit()"));?>
	
	<?php echo $form->input('ticketId', array('type' => 'hidden', 'value' => $ticket['Ticket']['ticketId']));?>
	<?php echo $form->input('userId', array('type' => 'hidden', 'value' => $ticket['Ticket']['userId']));?>
	<?php echo $form->input('paymentTypeId', array('type' => 'hidden', 'value' => '1'));?>
	
	<h2>Process payment for:</h2>
	<br />
	<table cellspacing="0" cellpadding="0" border="1">
		<tr>
			<td width="200"><strong>Ticket Id</strong></td>
			<td><?php echo $ticket['Ticket']['ticketId'];?></td>
		</tr>
		<tr>
			<td><strong>User</strong></td>
			<td><?php echo $ticket['Ticket']['userFirstName'] . ' ' . $ticket['Ticket']['userLastName'];?></td>
		</tr>
		<tr>
			<td><strong>User Id</strong></td>
			<td><?php echo $ticket['Ticket']['userId'];?></td>
		</tr>
		<tr>
			<td><strong>Package</strong></td>
			<td><?php echo $ticket['Package']['packageName'];?></td>
		</tr>
		<tr>
			<td><strong>Ticket Amount</strong></td>
			<td><?php echo $number->currency($ticket['Ticket']['billingPrice']);?></td>
		</tr>
		<?php if (!empty($ticket['UserPromo']['Promo'])) :?>
		<tr>
			<td><strong>Promo Code Applied:</strong></td>
			<td>
				<h3 style="margin:0px;padding:0px;padding-bottom:5px;">
				<?=$ticket['UserPromo']['Promo']['promoName'];?> - [<?=$ticket['UserPromo']['Promo']['promoCode'];?>] - 
				Amount Off: <?php echo $number->currency($ticket['UserPromo']['Promo']['totalAmountOff']);?>
				</h3>
			</td>
		</tr>	
		<?php endif; ?>
	</table>

	<br />
	<h2>Payment Settings:</h2>
	<br />
	<table cellspacing="0" cellpadding="0" border="1">
		<tr>
			<td width="200"><strong>Payment Type</strong></td>
			<td>Charge</td>
		</tr>
		<tr>
			<td><strong>Payment Processor</strong></td>
			<td>
				<select name="data[PaymentDetail][paymentProcessorId]" id="PaymentDetailPaymentProcessorId">
					<?php foreach ($paymentProcessorIds as $ppId => $ppValue): ?>
					<option value="<?php echo $ppId;?>"><?php echo $ppValue;?></option>
					<?php endforeach;?>
				</select>	
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<strong>User Payment Setting (choose one):</strong><br /><br />
				
				<fieldset class="collapsible">
					<legend class="handle" style="font-size:12px;">Existing Cards (<?php echo count($userPaymentSetting);?>)</legend>
					<div class="collapsibleContent">
					
						<table style="background:whitesmoke;margin:0px;padding:0px;" cellspacing="0" cellpadding="0" border="1">
						<tr>
							<td>&nbsp;</td>
							<td><i>Card Id</i></td>
							<td><i>Name on Card</i></td>
							<td><i>Address</i></td>
							<td><i>CC Type</i></td>
							<td><i>Card Number</i></td>
							<td><i>Exp</i></td>
							<td><i>Primary</i></td>
							<td><i>Inactive</i></td>
							<td><i>Card Added</i></td>
						</tr>
						<?php 				
						$counter = 0;
						foreach ($userPaymentSetting as $upsId => $upsValue) { 
							if ($upsValue['inactive']) {
								//continue;
							}	
							$selectPrimaryCC = $upsValue['primaryCC'] && !$upsValue['inactive'] ? 'checked' : '';
							$cardInactiveColor = $upsValue['inactive'] ? '#CC0000;' : '#009900;';
							?>
							<tr style="color: <?php echo $cardInactiveColor;?>">
								<td>
									<input <?php echo $selectPrimaryCC;?> type="radio" name="data[PaymentDetail][userPaymentSettingId]" id="PaymentDetailUserPaymentSettingId" value="<?php echo $upsValue['userPaymentSettingId'];?>" />
								</td>
								<td><?php echo $upsValue['userPaymentSettingId'];?></td>
								<td><?php echo $upsValue['nameOnCard'];?></td>
								<td>
									<?php echo $upsValue['address1'];?><br />
									<?php if ($upsValue['address2']) { echo $upsValue['address2'] . '<br />'; } ?>
									<?php echo $upsValue['city'] . ', ' . $upsValue['state'] . ' ' . $upsValue['postalCode'];?>
								</td>
								<td><?php echo $upsValue['ccType'];?></td>
								<td><?php echo $upsValue['ccNumber'];?></td>
								<td><?php echo $upsValue['expMonth'] . '/' . $upsValue['expYear'];?></td>
								<td><?php echo ($upsValue['primaryCC']) ? 'Yes' : 'No';?></td>
								<td><?php echo ($upsValue['inactive']) ? '<strong>Yes</strong>' : 'No';?></td>
								<td><?php echo $upsValue['created'];?></td>
							</tr>
						<?php } ?>
						</table>
				
					</div>
				</fieldset>
			
				<br />
			
				<fieldset class="collapsible">
					<legend class="handle" style="font-size:12px;">Add New Card</legend>
					<div class="collapsibleContent">
						
						<table style="background:whitesmoke;margin:0px;padding:0px;" cellspacing="0" cellpadding="0" border="0">
							<tr>
								<td width="150"><br /><strong>Use New Card</strong><br /><br /></td>
								<td><br /><input type="checkbox" name="data[UserPaymentSetting][useNewCard]" id="UserPaymentSettingUseNewCard" /><br /><br /></td>
							</tr>
							<tr>
								<td width="150">Name on Card</td>
								<td><input type="text" name="data[UserPaymentSetting][nameOnCard]" id="UserPaymentSettingNameOnCard" size="30" /></td>
							</tr>
							<tr>
								<td>Address 1</td>
								<td><input type="text" name="data[UserPaymentSetting][address1]" id="UserPaymentSettingAddress1" size="50" /></td>
							</tr>
							<tr>
								<td>Address 2</td>
								<td><input type="text" name="data[UserPaymentSetting][address2]" id="UserPaymentSettingAddress2" size="50" /></td>
							</tr>
							<tr>
								<td>City</td>
								<td><input type="text" name="data[UserPaymentSetting][city]" id="UserPaymentSettingCity" size="20" /></td>
							</tr>
							<tr>
								<td>State</td>
								<td><input type="text" name="data[UserPaymentSetting][state]" id="UserPaymentSettingState" size="20" /></td>
							</tr>
							<tr>
								<td>Country</td>
								<td>
									<select name="data[UserPaymentSetting][country]" id="UserPaymentSettingCountry">
										<option value="US">US</option>
										<?php
										foreach ($countries as $ckey => $country) {
											echo "<option value=\"$country\">$country</option>\n";
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td>Postal/Zip Code</td>
								<td><input type="text" name="data[UserPaymentSetting][postalCode]" id="UserPaymentSettingPostalCode" size="10" /></td>
							</tr>
							<tr>
								<td>Card Number</td>
								<td><input type="text" name="data[UserPaymentSetting][ccNumber]" id="UserPaymentSettingCcNumber" size="50" /></td>
							</tr>
							<tr>
								<td>Expiration Month</td>
								<td>
									<select name="data[UserPaymentSetting][expMonth]" id="UserPaymentSettingExpMonth">
										<?php
										foreach ($selectExpMonth as $mkey => $eMonth) {
											echo "<option value=\"$eMonth\">$eMonth</option>\n";
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td>Expiration Year</td>
								<td>
									<select name="data[UserPaymentSetting][expYear]" id="UserPaymentSettingExpYear">
										<?php
										foreach ($selectExpYear as $ykey => $eYear) {
											echo "<option value=\"$eYear\">$eYear</option>\n";
										}
										?>
									</select>
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td>
									<br />
									<input type="checkbox" name="data[UserPaymentSetting][save]" id="UserPaymentSettingSave" />&nbsp;
									Save this record for this user 
								</td>
							</tr>
						</table>
						
					</div>
				</fieldset>
				
				<?php echo $form->error('userPaymentSettingId') ?>
			</td>
		</tr>
		<tr>
			<td style="padding-top:10px;padding-bottom:10px;"><strong>Initials</strong></td>
			<?php if ($initials_user) : ?>
				<td style="padding-top:10px;padding-bottom:10px;"><input type="text" name="data[PaymentDetail][initials]" id="PaymentDetailInitials" maxlength="15" size="15" readonly="readonly" value="<?=$initials_user;?>" /></td>
			<?php else : ?>
				<td style="padding-top:10px;padding-bottom:10px;"><input type="text" name="data[PaymentDetail][initials]" id="PaymentDetailInitials" maxlength="15" size="15" /><?php echo $form->error('initials') ?></td>
			<?php endif; ?>
		</tr>
		<tr style="background-color: #CCEEBB;">
			<td style="padding-top:10px;padding-bottom:10px;"><strong>Payment Amount</strong></td>
			<td style="padding-top:10px;padding-bottom:10px;"><input type="text" name="data[PaymentDetail][paymentAmount]" id="PaymentDetailPaymentAmount" value="<?php echo $ticket['Ticket']['totalBillingAmount'];?>" /><?php echo $form->error('paymentAmount') ?>
			( Includes Auction Fee 
			<?php if ($ticket['UserPromo']['Promo'] && $ticket['UserPromo']['Promo']['applied']): ?>
			+  Promo Code Discount 
			<?php endif; ?>
			)
			</td>
		</tr>
	</table>

<?php echo $form->end('Submit Payment');?>
</div>
<?php
//if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>
