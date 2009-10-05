<div class="reservations form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'reservations/add'))); ?>
	<fieldset>
 		<legend><?php __('Add Reservation');?></legend>
	<?php
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		//echo $form->input('roomType');
		//echo $form->input('numNights');;
		//echo $form->input('availabilityConfirmUserId');
		//echo $form->input('customerConsentDate');
		//echo $form->input('reservationRequestDate');
		//echo $form->input('reservationConfirmToCustomer');
		echo $form->input('reservationConfirmNum');
	?>
	<div class="input text"><label for="ReservationArrivalDate">Arrival Date</label><input type="text" style="width:100px;" readonly="readonly" class="MB_focusable" name="data[Reservation][arrivalDate]" id="ReservationArrivalDate" onchange="setEndDate();" /></div>
	<div class="input text"><label for="ReservationDepartureDate">Departure Date</label><input type="text" style="width:100px;" readonly="readonly" class="MB_focusable" name="data[Reservation][departureDate]" id="ReservationDepartureDate" onchange="setEndDate();" /></div>
	<script>
	var id = 'ReservationArrivalDate';
	delete datePickerController.datePickers[id];
    datePickerController.addDatePicker(id,
        {
        'id': id,
        'highlightDays':'0,0,0,0,0,1,1',
        'disableDays':'',
        'divider':'-',
        'format': "y-m-d",
        'locale':true,
        'splitDate':0,
        'noTransparency':true,
        'staticPos':false,
        'hideInput':false,
        }
    );
	var id = 'ReservationDepartureDate';
	delete datePickerController.datePickers[id];
    datePickerController.addDatePicker(id,
        {
        'id': id,
        'highlightDays':'0,0,0,0,0,1,1',
        'disableDays':'',
        'divider':'-',
        'format': "y-m-d",
        'locale':true,
        'splitDate':0,
        'noTransparency':true,
        'staticPos':false,
        'hideInput':false,
        }
    );
	</script>

	</fieldset>
<?php echo $form->end('Submit');?>
</div>
