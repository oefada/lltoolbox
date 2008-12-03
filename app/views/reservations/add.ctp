<div class="reservations form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'reservations/add'))); ?>
	<fieldset>
 		<legend><?php __('Add Reservation');?></legend>
	<?php
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('roomType');
		echo $form->input('numNights');;
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