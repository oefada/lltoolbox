<div class="tickets view">
<h2><?php  __('Ticket');?></h2>

	<?php if ($showCancellation) { ?>
	<h3>This ticket has been flagged with a flake status.</h3>
	<div>
		Click on the link below to fill out the ticket cancellation information.<br />
		<a href="/tickets/<?php echo $ticket['Ticket']['ticketId'];?>/ticket_cancellations/add">Fill out Ticket Cancellation</a><br /><br />
	</div>
	<?php } ?>
	
	<?php if ($showRefund) { ?>
	<h3>This ticket has been flagged with a refund status.</h3>
	<div>
		Click on the link below to fill out the ticket refund information.<br />
		<a href="/tickets/<?php echo $ticket['Ticket']['ticketId'];?>/ticket_refunds/add">Fill out Ticket Refund</a><br /><br />
	</div>
	<?php } ?>
	
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['ticketId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Ticket Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['TicketStatus']['ticketStatusName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ParentTicketId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['parentTicketId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Package'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($ticket['Package']['packageId'], array('controller'=> 'packages', 'action'=>'view', $ticket['Package']['packageId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Offer'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($ticket['Offer']['offerId'], array('controller'=> 'offers', 'action'=>'view', $ticket['Offer']['offerId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RequestId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['requestId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BidId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['bidId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RequestInfo'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['requestInfo']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Notes'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['notes']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('IsFlake'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['isFlake']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PaymentAuthDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['paymentAuthDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PaymentSettleDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['paymentSettleDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BillingPrice'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['billingPrice']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BookingPrice'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['bookingPrice']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserFirstName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userFirstName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserLastName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userLastName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserEmail1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userEmail1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserWorkPhone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userWorkPhone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserHomePhone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userHomePhone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserMobilePhone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userMobilePhone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserFax'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userFax']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserAddress1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userAddress1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserAddress2'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userAddress2']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserAddress3'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userAddress3']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserCity'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userCity']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserState'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userState']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserCountry'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userCountry']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserZip'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['userZip']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CompletedUsername'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['completedUsername']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CompletedDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['completedDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('KeepAmount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['keepAmount']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RemitAmount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['remitAmount']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ComissionAmount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ticket['Ticket']['comissionAmount']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Ticket', true), array('action'=>'edit', $ticket['Ticket']['ticketId'])); ?> </li>
		<!--li><?php echo $html->link(__('Delete Ticket', true), array('action'=>'delete', $ticket['Ticket']['ticketId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ticket['Ticket']['ticketId'])); ?> </li>
		<li><?php echo $html->link(__('List Tickets', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ticket Statuses', true), array('controller'=> 'ticket_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket Status', true), array('controller'=> 'ticket_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Packages', true), array('controller'=> 'packages', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Package', true), array('controller'=> 'packages', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Offers', true), array('controller'=> 'offers', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer', true), array('controller'=> 'offers', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ticket Cancellations', true), array('controller'=> 'ticket_cancellations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket Cancellation', true), array('controller'=> 'ticket_cancellations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ticket Refunds', true), array('controller'=> 'ticket_refunds', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket Refund', true), array('controller'=> 'ticket_refunds', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Reservations', true), array('controller'=> 'reservations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Reservation', true), array('controller'=> 'reservations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Payment Details', true), array('controller'=> 'payment_details', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Detail', true), array('controller'=> 'payment_details', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ppv Notices', true), array('controller'=> 'ppv_notices', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ppv Notice', true), array('controller'=> 'ppv_notices', 'action'=>'add')); ?> </li-->
	</ul>
</div>
	<div class="related">
		<h3><?php  __('Related Ticket Cancellations');?></h3>
	<?php if (!empty($ticket['TicketCancellation']['ticketId'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketCancellationId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketCancellation']['ticketCancellationId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CancellationReasonId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketCancellation']['cancellationReasonId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketCancellation']['ticketId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateCancelled');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketCancellation']['dateCancelled'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CancellationNotes');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketCancellation']['cancellationNotes'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div>
			<?php if ($validNextBid) { ?>
				<a href="/tickets/autoNewTicket/<?php echo $ticket['Ticket']['ticketId'];?>"><h4>&raquo;&nbsp;Create New Ticket using next bid</h4></a>
			<?php } else { ?>
				<h4>There is NO next eligible bid or this ticket is still active.  If this ticket is a flake, please edit this ticket and check the isFlake checkbox.  After saving the ticket, you 
				will be prompted to fill out a ticket cancellation form.  Finally, you can auto create the ticket with the next eligible bid (if exists).</h4>
			<?php } ?>
		</div>
	</div>
	
		<div class="related">
		<h3><?php  __('Related Ticket Refunds');?></h3>
	<?php if (!empty($ticket['TicketRefund']['ticketId'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketRefundId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketRefund']['ticketRefundId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RefundReasonId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketRefund']['refundReasonId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketRefund']['ticketId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateRefunded');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketRefund']['dateRefunded'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AmountRefunded');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['TicketRefund']['amountRefunded'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
	</div>
	
		<div class="related">
		<h3><?php  __('Related Reservations');?></h3>
	<?php if (!empty($ticket['Reservation']['ticketId'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TicketId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['ticketId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RoomType');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['roomType'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumNights');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['numNights'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AvailabilityConfirmDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['availabilityConfirmDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AvailabilityConfirmUserId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['availabilityConfirmUserId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerConsentDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['customerConsentDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ArrivalDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['arrivalDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DepartureDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['departureDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationRequestDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['reservationRequestDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationMadeDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['reservationMadeDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationMadeUserId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['reservationMadeUserId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationConfirmToCustomer');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['reservationConfirmToCustomer'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationConfirmNum');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['reservationConfirmNum'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationConfirmUserId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $ticket['Reservation']['reservationConfirmUserId'];?>
&nbsp;</dd>
		</dl>
	<?php else: ?>
		<a href="/tickets/<?php echo $ticket['Ticket']['ticketId'];?>/reservations/add"><h4>&raquo;&nbsp;Add Reservation Detail</h4></a>
	<?php endif; ?>
	</div>
	
	<div class="related">
	<h3><?php __('Related Payment Details');?></h3>
	<?php if (!empty($ticket['PaymentDetail'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Payment Detail Id'); ?></th>
		<th><?php __('First Name'); ?></th>
		<th><?php __('Last Name'); ?></th>
		<th><?php __('Billing Amount'); ?></th>
		<th><?php __('Applied to LOA'); ?></th>
		<th><?php __('Payment Date'); ?></th>
		<th><?php __('Refund Ticket'); ?></th>
		<th><?php __('Processed'); ?></th>
		<th><?php __('Successful Charge'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($ticket['PaymentDetail'] as $paymentDetail):
			$successful_charge = $paymentDetail['successfulCharge'] ? 'Yes' : 'No';
			$processed_flag = $paymentDetail['processed'] ? 'Yes' : 'No';
			if ($paymentDetail['autoProcessed']) {
				$processed_flag.= ' (Auto)';
			}
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $paymentDetail['paymentDetailId'];?></td>
			<td><?php echo $paymentDetail['creditCardFirstName'];?></td>
			<td><?php echo $paymentDetail['creditCardLastName'];?></td>
			<td>$<?php echo $paymentDetail['billingAmount'];?></td>
			<td><?php echo $paymentDetail['applyToLOA'];?></td>
			<td><?php echo $paymentDetail['paymentDate'];?></td>
			<td><?php echo $paymentDetail['refundWholeTicket'];?></td>
			<td><strong><?php echo $processed_flag;?></strong></td>
			<td><?php echo $successful_charge;?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'payment_details', 'action'=>'view', $paymentDetail['paymentDetailId'])); ?>
				<?php 
				if (!$paymentDetail['processed']) {
					echo $html->link(__('Process Payment', true), array('controller'=> 'payment_details', 'action'=>'confirmPayment', $paymentDetail['paymentDetailId']));	
				}
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><a href="/tickets/<?php echo $ticket['Ticket']['ticketId'];?>/payment_details/add">Add New Payment Detail</a></li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Ppv Notices');?></h3>
	<?php if (!empty($ticket['PpvNotice'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('PpvNoticeId'); ?></th>
		<th><?php __('PpvNoticeTypeId'); ?></th>
		<th><?php __('TicketId'); ?></th>
		<th><?php __('To'); ?></th>
		<th><?php __('From'); ?></th>
		<th><?php __('Cc'); ?></th>
		<th><?php __('Body'); ?></th>
		<th><?php __('DateSent'); ?></th>
		<th><?php __('Subject'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($ticket['PpvNotice'] as $ppvNotice):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $ppvNotice['ppvNoticeId'];?></td>
			<td><?php echo $ppvNotice['ppvNoticeTypeId'];?></td>
			<td><?php echo $ppvNotice['ticketId'];?></td>
			<td><?php echo $ppvNotice['to'];?></td>
			<td><?php echo $ppvNotice['from'];?></td>
			<td><?php echo $ppvNotice['cc'];?></td>
			<td><?php echo $ppvNotice['body'];?></td>
			<td><?php echo $ppvNotice['dateSent'];?></td>
			<td><?php echo $ppvNotice['subject'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'ppv_notices', 'action'=>'view', $ppvNotice['ppvNoticeId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'ppv_notices', 'action'=>'edit', $ppvNotice['ppvNoticeId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'ppv_notices', 'action'=>'delete', $ppvNotice['ppvNoticeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ppvNotice['ppvNoticeId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Ppv Notice', true), array('controller'=> 'ppv_notices', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
