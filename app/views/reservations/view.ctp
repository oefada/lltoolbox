<div class="reservations view">
<h2><?php  __('Reservation');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['worksheetId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RoomType'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['roomType']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumNights'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['numNights']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AvailabilityConfirmDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['availabilityConfirmDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AvailabilityConfirmUserId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['availabilityConfirmUserId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerConsentDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['customerConsentDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ArrivalDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['arrivalDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DepartureDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['departureDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationRequestDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['reservationRequestDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationMadeDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['reservationMadeDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationMadeUserId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['reservationMadeUserId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationConfirmToCustomer'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['reservationConfirmToCustomer']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationConfirmNum'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['reservationConfirmNum']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationConfirmUserId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $reservation['Reservation']['reservationConfirmUserId']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Reservation', true), array('action'=>'edit', $reservation['Reservation']['worksheetId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Reservation', true), array('action'=>'delete', $reservation['Reservation']['worksheetId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $reservation['Reservation']['worksheetId'])); ?> </li>
		<li><?php echo $html->link(__('List Reservations', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Reservation', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Worksheets', true), array('controller'=> 'worksheets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Worksheet', true), array('controller'=> 'worksheets', 'action'=>'add')); ?> </li>
	</ul>
</div>
	<div class="related">
		<h3><?php  __('Related Worksheets');?></h3>
	<?php if (!empty($reservation['Worksheet'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['worksheetId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetStatusId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['worksheetStatusId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ParentWorksheetId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['parentWorksheetId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['packageId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('OfferId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['offerId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RequestId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['requestId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BidId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['bidId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RequestInfo');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['requestInfo'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Notes');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['notes'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('IsFlake');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['isFlake'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PaymentAuthDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['paymentAuthDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PaymentSettleDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['paymentSettleDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BillingPrice');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['billingPrice'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BookingPrice');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['bookingPrice'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerFirstName');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerFirstName'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerLastName');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerLastName'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerEmail1');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerEmail1'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerWorkPhone');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerWorkPhone'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerHomePhone');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerHomePhone'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerMobilePhone');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerMobilePhone'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerFax');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerFax'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerAddress1');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerAddress1'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerAddress2');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerAddress2'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerAddress3');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerAddress3'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerCity');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerCity'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerState');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerState'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerCountry');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerCountry'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerZip');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['customerZip'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CompletedUserId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['completedUserId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CompletedDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $reservation['Worksheet']['completedDate'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
				<li><?php echo $html->link(__('Edit Worksheet', true), array('controller'=> 'worksheets', 'action'=>'edit', $reservation['Worksheet']['worksheetId'])); ?></li>
			</ul>
		</div>
	</div>
	