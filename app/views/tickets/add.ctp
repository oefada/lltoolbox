<div class="tickets form">
<?php echo $form->create('Ticket');?>
	<fieldset>
 		<legend><?php __('Add Ticket');?></legend>
	<?php
		echo $form->input('ticketStatusId');
		echo $form->input('parentTicketId');
		echo $form->input('packageId');
		echo $form->input('offerId');
		echo $form->input('requestId');
		echo $form->input('bidId');
		echo $form->input('requestInfo');
		echo $form->input('notes');
		echo $form->input('isFlake');
		echo $form->input('paymentAuthDate');
		echo $form->input('paymentSettleDate');
		echo $form->input('billingPrice');
		echo $form->input('userId');
		echo $form->input('userFirstName');
		echo $form->input('userLastName');
		echo $form->input('userEmail1');
		echo $form->input('userWorkPhone');
		echo $form->input('userHomePhone');
		echo $form->input('userMobilePhone');
		echo $form->input('userFax');
		echo $form->input('userAddress1');
		echo $form->input('userAddress2');
		echo $form->input('userAddress3');
		echo $form->input('userCity');
		echo $form->input('userState');
		echo $form->input('userCountry');
		echo $form->input('userZip');
		echo $form->input('completedUsername');
		echo $form->input('completedDate');
		echo $form->input('keepAmount');
		echo $form->input('remitAmount');
		echo $form->input('comissionAmount');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>

