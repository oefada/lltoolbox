<?php  $this->pageTitle = 'Payment Processor ' . $html2->c($ticket['Ticket']['ticketId'], 'Ticket Id:');?>
<?php $session->flash();
	$session->flash('error');
?>
<div class="paymentDetails form">
<?php echo $form->create('PaymentDetail', array('url' => "/tickets/{$ticket['Ticket']['ticketId']}/paymentDetails/add"));?>
<?php //echo $ajax->form('add', 'post', array('url' => "/tickets/{$ticket['Ticket']['ticketId']}/paymentDetails/add", 'update' => 'MB_content', 'model' => 'PaymentDetail', 	'complete' => 'closeModalbox()'));?>
	
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
					
				<table style="background:whitesmoke;margin:0px;padding:0px;" cellspacing="0" cellpadding="0" border="1">
				<tr>
					<td>&nbsp;</td>
					<td><i>Card Id</i></td>
					<td><i>Name on Card</i></td>
					<td><i>Address</i></td>
					<td><i>Card Number</i></td>
					<td><i>Exp</i></td>
					<td><i>Primary</i></td>
				</tr>
				<?php 				
				$counter = 0;
				foreach ($userPaymentSetting as $upsId => $upsValue) { 
					if ($upsValue['inactive']) {
						continue;
					}	
					?>
					<tr>
						<td>
							<input type="radio" name="data[PaymentDetail][userPaymentSettingId]" id="PaymentDetailUserPaymentSettingId" value="<?php echo $upsValue['userPaymentSettingId'];?>" />
						</td>
						<td><?php echo $upsValue['userPaymentSettingId'];?></td>
						<td><?php echo $upsValue['nameOnCard'];?></td>
						<td>
							<?php echo $upsValue['address1'];?><br />
							<?php if ($upsValue['address2']) { echo $upsValue['address2'] . '<br />'; } ?>
							<?php echo $upsValue['city'] . ', ' . $upsValue['state'] . ' ' . $upsValue['postalCode'];?>
						</td>
						<td><?php echo $upsValue['ccNumber'];?></td>
						<td><?php echo $upsValue['expMonth'] . '/' . $upsValue['expYear'];?></td>
						<td><?php echo ($upsValue['primaryCC']) ? 'Yes' : 'No';?></td>
					</tr>
				<?php } ?>
				</table>
				
				<?php echo $form->error('userPaymentSettingId') ?>
			</td>
		
		</tr>
		<tr>
			<td><strong>Initials</strong></td>
			<td><input type="text" name="data[PaymentDetail][initials]" id="PaymentDetailInitials" maxlength="5" size="5" /><?php echo $form->error('initials') ?></td>
		</tr>
		<tr>
			<td><strong>Payment Amount</strong></td>
			<td><input type="text" name="data[PaymentDetail][paymentAmount]" id="PaymentDetailPaymentAmount" /><?php echo $form->error('paymentAmount') ?></td>
		</tr>
	</table>

<?php echo $form->end('Submit Payment');?>
</div>
<?php
//if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>