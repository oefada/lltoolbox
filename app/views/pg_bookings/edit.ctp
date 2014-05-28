<?php
$booking = $this->data;
//$this->pageTitle = $this->data['Package']['packageName'].$html2->c($this->data['Booking']['pgBookingId'], 'Ticket Id:');
?>
<div class="tickets form">
<?php echo $form->create('PgBooking', array('disabled' => 'disabled'));?>
	<fieldset>
	<?php
		echo $form->input('pgBookingId', array('type' => 'hidden'));
		if ($allow_status_edit) {
			echo $form->input('pgBookingStatusId', array('label' => 'Ticket Status Id'));
		} 
   	    echo $form->input('Notes.0.note', array('cols'=> '80', 'rows' => '15', 'label' => 'Ticket Notes' ) );
		// just tickets for nowz
		/*
		echo $form->input('ticketStatusId', array('disabled' => 'disabled'));
		echo $form->input('packageId', array('disabled' => 'disabled'));
		echo $form->input('offerId', array('disabled' => 'disabled'));
		echo $form->input('billingPrice', array('disabled' => 'disabled'));
		echo $form->input('userId', array('disabled' => 'disabled'));
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
		*/
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
