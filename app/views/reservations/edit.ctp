<div class="reservations form">
<?php echo $form->create('Reservation');?>
	<fieldset>
 		<legend><?php __('Edit Reservation');?></legend>
	<?php
		echo $form->input('reservationId');
		echo $form->input('ticketId');
		echo $form->input('roomType');
		echo $form->input('numNights');
		echo $form->input('availabilityConfirmUserId');
		echo $form->input('customerConsentDate');
		echo $form->input('arrivalDate');
		echo $form->input('departureDate');
		echo $form->input('reservationRequestDate');
		echo $form->input('reservationConfirmToCustomer');
		echo $form->input('reservationConfirmNum');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>