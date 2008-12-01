<?php
$this->pageTitle = $ticket['Package']['packageName'] . $html2->c($ticket['Ticket']['ticketId'], 'Ticket Id:');
$this->searchController = 'Tickets';
?>
<div class="tickets view">
	<h2 class="title">Ticket Detail</h2>
	<br />
	<div class="ticket-table">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="200"><strong>Ticket Id</strong></td>
				<td><?php echo $ticket['Ticket']['ticketId']; ?></td>
			</tr>
			<tr>
				<td width="200"><strong>Status</strong></td>
				<td><?php echo $ticket['TicketStatus']['ticketStatusName']; ?></td>
			</tr>
			<tr>
				<td width="200"><strong>Package Name</strong></td>
				<td><?php echo $ticket['Package']['packageName']; ?></td>
			</tr>
			<tr>
				<td><strong>Package Id</strong></td>
				<td><?php echo $html->link($ticket['Package']['packageId'], array('controller'=> 'packages', 'action'=>'view', $ticket['Package']['packageId'])); ?></td>
			</tr>
			<tr>
				<td><strong>Offer Id</strong></td>
				<td><?php echo $html->link($ticket['Offer']['offerId'], array('controller'=> 'offers', 'action'=>'view', $ticket['Offer']['offerId'])); ?></td>
			</tr>
			<tr>
				<td><strong>Offer Type</strong></td>
				<td><?php echo $offerType[$ticket['Ticket']['offerTypeId']]; ?></td>
			</tr>
			<tr>
				<td><strong>Booking Price</strong></td>
				<td><?php echo $number->currency($ticket['Ticket']['bookingPrice']); ?></td>
			</tr>
			<tr>
				<td><strong>Billing Price</strong></td>
				<td><?php echo $number->currency($ticket['Ticket']['billingPrice']); ?></td>
			</tr>
			<tr>
				<td><strong>User Id</strong></td>
				<td><?php echo $html->link($ticket['Ticket']['userId'], array('controller'=> 'users', 'action'=>'view', $ticket['Ticket']['userId'])); ?></td>
			</tr>
			<tr>
				<td><strong>Name</strong></td>
				<td><?php echo $ticket['Ticket']['userFirstName'] . ' ' . $ticket['Ticket']['userLastName']; ?></td>
			</tr>
			<tr>
				<td><strong>Email</strong></td>
				<td><?php echo $ticket['Ticket']['userEmail1']; ?></td>
			</tr>
			<tr>
				<td><strong>Home Phone</strong></td>
				<td><?php echo $ticket['Ticket']['userHomePhone']; ?></td>
			</tr>
			<tr>
				<td><strong>Mobile Phone</strong></td>
				<td><?php echo $ticket['Ticket']['userMobilePhone']; ?></td>
			</tr>
			<tr>
				<td><strong>Work Phone</strong></td>
				<td><?php echo $ticket['Ticket']['userWorkPhone']; ?></td>
			</tr>
			<tr>
				<td><strong>Address</strong></td>
				<td>
					<?php
						if ($ticket['Ticket']['userAddress1']) {
							echo $ticket['Ticket']['userAddress1'];
						}
						if ($ticket['Ticket']['userAddress2']) {
							echo '<br />' . $ticket['Ticket']['userAddress2'];
						}
						if ($ticket['Ticket']['userAddress3']) {
							echo '<br/ >' . $ticket['Ticket']['userAddress3'];
						}
						echo '<br />' . $ticket['Ticket']['userCity'] . ', ' . $ticket['Ticket']['userState'] . ' ' . $ticket['Ticket']['userZip'] . '<br />' . $ticket['Ticket']['userCountry'];
					?>
				</td>
			</tr>
			<?php if ($ticket['Ticket']['requestQueueId']) : ?>
			<tr>
				<td><strong>Request Arrival</strong></td>
				<td><?php echo $ticket['Ticket']['requestArrival']; ?></td>
			</tr>
			<tr>
				<td><strong>Request Departure</strong></td>
				<td><?php echo $ticket['Ticket']['requestDeparture']; ?></td>
			</tr>
			<tr>
				<td><strong>Request Num Guests</strong></td>
				<td><?php echo $ticket['Ticket']['requestNumGuests']; ?></td>
			</tr>
			<tr>
				<td><strong>Request Notes</strong></td>
				<td><?php echo $ticket['Ticket']['requestNotes']; ?></td>
			</tr>
			<?php else: ?>
			<tr>
				<td><strong>Bid Id</strong></td>
				<td><?php echo $html->link($ticket['Ticket']['bidId'], array('controller'=> 'bids', 'action'=>'view', $ticket['Ticket']['bidId'])); ?></td>
			</tr>
			<?php endif; ?>
			<tr>
				<td><strong>Ticket Notes</strong>
				<?php
					echo $html->link('Edit',
						'/tickets/edit/' . $ticket['Ticket']['ticketId'],
						array(
							'title' => 'Edit Ticket Notes',
							'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
							'complete' => 'closeModalbox()'
							),
						null,
						false
						);
				?>
				</td>
				<td><?php echo $ticket['Ticket']['ticketNotes']; ?></td>
			</tr>
		</table>
	</div>
	<div style="clear:both;"></div>
</div>

<br />
<div class="collapsible">
	<div class="handle"><?php __('Payment Detail History');?></div>
	<div class="collapsibleContent related">
	<br />
	<?php if (!empty($ticket['PaymentDetail'])):?>
		<table cellpadding = "0" cellspacing = "0">
		<tr>
			<th><?php __('Payment Detail Id'); ?></th>
			<th><?php __('Processed Date'); ?></th>
			<th><?php __('Billing Amount'); ?></th>
			<th><?php __('Processor'); ?></th>
			<th><?php __('Status'); ?></th>
			<th class="actions"><?php __('Actions');?></th>
		</tr>
		<?php
			$i = 0;
			foreach ($ticket['PaymentDetail'] as $paymentDetail):
				$processed_flag = $paymentDetail['successfulCharge'] ? 'Payment Successful' : 'Payment Declined';
				if ($paymentDetail['autoProcessed']) {
					$processed_flag.= ' (Auto)';
				}
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
				}
			?>
				<tr<?php echo $class;?>>
					<td align="center"><?php echo $paymentDetail['paymentDetailId']; ?></td>
					<td align="center"><?php echo $paymentDetail['paymentDatetime'];?></td>
					<td align="center"><?php echo $number->currency($paymentDetail['paymentAmount']);?></td>
					<td align="center"><?php echo $paymentDetail['PaymentProcessor']['paymentProcessorName'];?></td>
					<td align="center"><?php echo $processed_flag;?></td>
					<td class="actions"><?php echo $html->link(__('View', true), array('controller'=> 'payment_details', 'action'=>'view', $paymentDetail['paymentDetailId'])); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>
	<?php
		echo $html->link('Create New Payment', '/tickets/' . $ticket['Ticket']['ticketId'] . '/payment_details/add');
		/*echo $html->link('Create New Payment',
			'/tickets/' . $ticket['Ticket']['ticketId'] . '/payment_details/add',
			array(
				'title' => 'Create New Payment',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);*/
	?>
	</div>
</div>

<br />
<div class="collapsible">
	<div class="handle"><?php __('Notifications and PPVs');?></div>
	<div class="collapsibleContent related">
	<br />
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
	</div>
</div>

<br />
<div class="collapsible">
	<div class="handle"><?php __('Reservations');?></div>
	<div class="collapsibleContent related">
	<br />
	<?php if (!empty($ticket['Reservation']['ticketId'])):?>
		<table cellpadding="0" cellspacing="0">
			<tr class="altrow">
				<td width="200">Reservation Id</td>
				<td><?php echo $ticket['Reservation']['reservationId'];?></td>
			</tr>
			<tr>
				<td width="200">Room Type</td>
				<td><?php echo $ticket['Reservation']['roomType'];?></td>
			</tr>
			<tr class="altrow">
				<td width="200">Number of Nights</td>
				<td><?php echo $ticket['Reservation']['numNights'];?></td>
			</tr>
			<tr>
				<td width="200">Availability Confirm Date</td>
				<td><?php echo $ticket['Reservation']['availabilityConfirmDate'];?></td>
			</tr>
			<tr class="altrow">
				<td width="200">Customer Consent Date</td>
				<td><?php echo $ticket['Reservation']['customerConsentDate'];?></td>
			</tr>
			<tr>
				<td width="200">Arrival Date</td>
				<td><?php echo $ticket['Reservation']['arrivalDate'];?></td>
			</tr>
			<tr class="altrow">
				<td width="200">Department Date</td>
				<td><?php echo $ticket['Reservation']['departureDate'];?></td>
			</tr>
			<tr>
				<td width="200">Reservation Request Date</td>
				<td><?php echo $ticket['Reservation']['reservationRequestDate'];?></td>
			</tr>
			<tr class="altrow">
				<td width="200">Reservation Made Date</td>
				<td><?php echo $ticket['Reservation']['reservationMadeDate'];?></td>
			</tr>
			<tr>
				<td width="200">Reservation Confirm to Customer</td>
				<td><?php echo $ticket['Reservation']['reservationConfirmToCustomer'];?></td>
			</tr>
			<tr class="altrow">
				<td width="200">Reservation Confirmation NUmber</td>
				<td><?php echo $ticket['Reservation']['reservationConfirmNum'];?></td>
			</tr>
		</table>
		<?php
		echo $html->link('Edit Reservation',
			'/reservations/edit/' . $ticket['Reservation']['reservationId'],
			array(
				'title' => 'Edit Reservation',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);
		?>
	<?php else: ?>
		<?php
		echo $html->link('Start Reservation Procses',
			'/tickets/' . $ticket['Ticket']['ticketId'] . '/reservations/add',
			array(
				'title' => 'Reservation',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);
		?>
	<?php endif; ?>
	</div>
</div>

<br />
<div class="collapsible">
	<div class="handle"><?php __('Cancellation');?></div>
	<div class="collapsibleContent related">
	<br />
	<?php if (!empty($ticket['TicketCancellation']['ticketCancellationId'])):?>
		<table cellpadding="0" cellspacing="0">
			<tr class="altrow">
				<td width="200">Cancellation Id</td>
				<td><?php echo $ticket['TicketCancellation']['ticketCancellationId'];?></td>
			</tr>
			<tr>
				<td width="200">Cancellation Reason</td>
				<td><?php echo $ticket['TicketCancellation']['CancellationReason']['cancellationReasonName'];?></td>
			</tr>
			<tr class="altrow">
				<td width="200">Cancellation Date</td>
				<td><?php echo $ticket['TicketCancellation']['dateCancelled'];?></td>
			</tr>
			<tr>
				<td width="200">Cancellation Notes</td>
				<td><?php echo $ticket['TicketCancellation']['cancellationNotes'];?></td>
			</tr>
		</table>
		<?php
		echo $html->link('Edit Ticket Cancellation',
			'/ticket_cancellations/edit/' . $ticket['TicketCancellation']['ticketCancellationId'],
			array(
				'title' => 'Edit Ticket Cancellation',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);
		?>
	<?php else: ?>
		<?php
		echo $html->link('Cancel this Ticket',
			'/tickets/' . $ticket['Ticket']['ticketId'] . '/ticket_cancellations/add',
			array(
				'title' => 'Ticket Cancellation',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);
		?>
	<?php endif; ?>
	</div>
</div>
	
<br />
<div class="collapsible">
	<div class="handle"><?php __('Refund');?></div>
	<div class="collapsibleContent related">
	<br />
	<?php if (!empty($ticket['TicketRefund']['ticketRefundId'])):?>
		<table cellpadding="0" cellspacing="0">
			<tr class="altrow">
				<td width="200">Refund Id</td>
				<td><?php echo $ticket['TicketRefund']['ticketRefundId'];?></td>
			</tr>
			<tr>
				<td width="200">Refund Reason</td>
				<td><?php echo $ticket['TicketRefund']['RefundReason']['refundReasonName'];?></td>
			</tr>
			<tr class="altrow">
				<td width="200">Refund Date</td>
				<td><?php echo $ticket['TicketRefund']['dateRefunded'];?></td>
			</tr>
			<tr>
				<td width="200">Refund Amount</td>
				<td><?php echo $number->currency($ticket['TicketRefund']['amountRefunded']);?></td>
			</tr>
			<tr class="altrow">
				<td width="200">Refund Notes</td>
				<td><?php echo $ticket['TicketRefund']['refundNotes'];?></td>
			</tr>
		</table>
		<?php
		echo $html->link('Edit Ticket Refund',
			'/ticket_refunds/edit/' . $ticket['TicketRefund']['ticketRefundId'],
			array(
				'title' => 'Edit Ticket Refund',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);
		?>
	<?php else: ?>
		<?php
		echo $html->link('Refund this Ticket',
			'/tickets/' . $ticket['Ticket']['ticketId'] . '/ticket_refunds/add',
			array(
				'title' => 'Ticket Refund',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);
		?>
	<?php endif; ?>
	</div>
</div>
<br />
