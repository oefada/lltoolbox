<div class="reservations form">
<?php echo $form->create('Reservation');?>
	<fieldset>
 		<legend><?php __('Edit Reservation');?></legend>
	<?php
		echo $form->input('reservationId');
		echo $form->input('ticketId', array('readonly'=>'readonly'));
		//echo $form->input('roomType');
		//echo $form->input('numNights');
		//echo $form->input('availabilityConfirmUserId');
		//echo $form->input('customerConsentDate');
		//echo $form->input('arrivalDate');
		//echo $form->input('departureDate');
		//echo $form->input('reservationRequestDate');
		//echo $form->input('reservationConfirmToCustomer');
		echo $form->input('reservationConfirmNum');
	?>
	<div class="input text"><label for="ReservationArrivalDate">Arrival Date</label><input type="text" style="width:100px;" readonly="readonly" class="MB_focusable" name="data[Reservation][arrivalDate]" id="ReservationArrivalDate" value="<?=$this->data['Reservation']['arrivalDate'];?>" onchange="setEndDate();" /></div>
	<div class="input text"><label for="ReservationDepartureDate">Departure Date</label><input type="text" style="width:100px;" readonly="readonly" class="MB_focusable" name="data[Reservation][departureDate]" id="ReservationDepartureDate" value="<?=$this->data['Reservation']['departureDate'];?>" onchange="setEndDate();" /></div>
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
	<?php 
	if ($this->data['Reservation']['retailValue']) {
		echo $form->input('retailValue');
	}
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
