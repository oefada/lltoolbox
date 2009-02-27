<div class="tickets form">
<?php echo $form->create('Ticket');?>
	<fieldset>
 		<legend><?php __('Create Manual Ticket');?></legend>
 		<h2>This will NOT autocharge or autosend ppv<br/ ><br /></h2>
	<?php
		echo $form->input('ticketNotes');
		echo $form->input('packageId');
		echo $form->input('clientId');
		echo $form->input('formatId');
		echo $form->input('offerTypeId');
		echo $form->input('offerId');
		echo $form->input('billingPrice');
		echo $form->input('bidId');
		echo $form->input('requestArrival');
		echo $form->input('requestDeparture');
		echo $form->input('requestNumGuests');
		echo $form->input('requestNotes');
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
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
