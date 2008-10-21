<div class="reservations form">
<?php echo $form->create('Reservation');?>
	<fieldset>
 		<legend><?php __('Edit Reservation');?></legend>
	<?php
		echo $form->input('reservationId');
		echo $form->input('ticketId');
		echo $form->input('roomType');
		echo $form->input('numNights');
		echo $form->input('availabilityConfirmDate');
		echo $form->input('availabilityConfirmUserId');
		echo $form->input('customerConsentDate');
		echo $form->input('arrivalDate');
		echo $form->input('departureDate');
		echo $form->input('reservationRequestDate');
		echo $form->input('reservationMadeDate');
		echo $form->input('reservationMadeUserId');
		echo $form->input('reservationConfirmToCustomer');
		echo $form->input('reservationConfirmNum');
		echo $form->input('reservationConfirmUserId');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Reservation.ticketId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Reservation.ticketId'))); ?></li>
		<li><?php echo $html->link(__('List Reservations', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Tickets', true), array('controller'=> 'tickets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket', true), array('controller'=> 'tickets', 'action'=>'add')); ?> </li>
	</ul>
</div>
