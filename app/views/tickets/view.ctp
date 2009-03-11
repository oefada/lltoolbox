<?php
$this->pageTitle = $ticket['Package']['packageName'] . $html2->c($ticket['Ticket']['ticketId'], 'Ticket Id:');
$this->searchController = 'Tickets';
?>
<div class="tickets view">
	<h2 class="title">Ticket Detail</h2>
	<?php if (count($ticket['Client']) > 1) {
		echo "<h3>*** This is a MULTI-CLIENT package ***</h3>";
	} ?>
	<br />
	<div class="ticket-table">
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td width="200"><strong>Ticket Id</strong></td>
				<td><?php echo $ticket['Ticket']['ticketId']; ?></td>
			</tr>
			<tr>
				<td width="200"><strong>Created</strong></td>
				<td><?php echo $ticket['Ticket']['created']; ?></td>
			</tr>
			<tr>
				<td width="200"><strong>Status</strong></td>
				<td><strong><?php echo $ticket['TicketStatus']['ticketStatusName']; ?></strong></td>
			</tr>
			<tr>
				<td width="200"><strong>Package Name</strong></td>
				<td><?php echo $ticket['Package']['packageName']; ?></td>
			</tr>
			<tr>
				<td><strong>Package Id</strong></td>
				<td><?php echo $html->link($ticket['Ticket']['packageId'], array('controller'=> 'clients/' . $ticket['Ticket']['clientId'], 'action'=> '/packages/edit/' . $ticket['Ticket']['packageId'])); ?></td>
			</tr>
			<tr>
				<td width="200"><strong>Client(s)</strong></td>
				<td>
					<?php foreach ($ticket['Client'] as $client) :?>
						<?php echo $html->link($client['Client']['clientId'], array('controller'=> 'clients', 'action'=>'edit', $client['Client']['clientId'])); ?> - 
						<?php echo $client['Client']['name']; ?><br />
					<?php endforeach;?>
				</td>
			</tr>
			<tr>
				<td><strong>Offer Id</strong></td>
				<td>
					<?php echo $html->link($ticket['Ticket']['offerId'], array('controller'=> 'reports', 'action'=>'offer_search', 'filter:'.urlencode($offer_search_serialize))); ?>
					&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.luxurylink.com/portfolio/por_offer_redirect.php?pid=<?php echo $ticket['Ticket']['clientId'];?>" target="_BLANK">Offer Page</a>
				</td>
			</tr>
			<tr>
				<td><strong>Offer Type</strong></td>
				<td><?php echo $offerType[$ticket['Ticket']['offerTypeId']]; ?></td>
			</tr>
			<tr>
				<td><strong>Ticket Amount</strong></td>
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
	<div class="handle"><?php __('Payment Detail History ('. count($ticket['PaymentDetail']) .')');?></div>
	<div class="collapsibleContent related">
	<br />
	<?php if (!empty($ticket['PaymentDetail'])):?>
		<table cellpadding = "0" cellspacing = "0">
		<tr>
			<th><?php __('Payment Detail Id'); ?></th>
			<th><?php __('Processed Date'); ?></th>
			<th><?php __('Billing Amount'); ?></th>
			<th><?php __('Last Four CC'); ?></th>
			<th><?php __('Processor'); ?></th>
			<th><?php __('Status'); ?></th>
			<th><?php __('CC Type'); ?></th>
			<th><?php __('Initials'); ?></th>
			<th class="actions"><?php __('Actions');?></th>
		</tr>
		<?php
			$i = 0;
			foreach ($ticket['PaymentDetail'] as $paymentDetail):
				$processed_flag = $paymentDetail['isSuccessfulCharge'] ? 'Payment Successful' : 'Payment Declined';
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
				}
			?>
				<tr<?php echo $class;?>>
					<td align="center"><?php echo $paymentDetail['paymentDetailId']; ?></td>
					<td align="center"><?php echo $paymentDetail['ppResponseDate'];?></td>
					<td align="center"><?php echo $number->currency($paymentDetail['ppBillingAmount']);?></td>
					<td align="center"><?php echo $paymentDetail['ppCardNumLastFour'];?></td>
					<td align="center"><?php echo $paymentDetail['PaymentProcessor']['paymentProcessorName'];?></td>
					<td align="center"><?php echo $processed_flag;?></td>
					<td align="center"><?php echo $paymentDetail['ccType']; ?></td>
					<td align="center"><?php echo $paymentDetail['initials'];?></td>
					<td class="actions">
						<?php //echo $html->link(__('View', true), array('controller'=> 'payment_details', 'action'=>'view', $paymentDetail['paymentDetailId'])); ?>
						<?php
							echo $html->link('View',
									'/payment_details/view/' . $paymentDetail['paymentDetailId'],
									array(
										'title' => 'View Payment Transaction Details',
										'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
										'complete' => 'closeModalbox()'
									),
									null,
									false
								);
						?>
					</td>
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
	<div class="handle"><?php __('Notifications and PPVs ('. count($ticket['PpvNotice']) .')');?></div>
	<div class="collapsibleContent related">
	<br />
	<?php if (!empty($ticket['PpvNotice'])):?>
		<table cellpadding = "0" cellspacing = "0">
		<tr>
			<th><?php __('PpvNoticeId'); ?></th>
			<th><?php __('Notice Type'); ?></th>
			<th><?php __('To'); ?></th>
			<th><?php __('From'); ?></th>
			<th><?php __('Subject'); ?></th>
			<th><?php __('DateSent'); ?></th>
			<th><?php __('Initials'); ?></th>
			<th class="actions"><?php __('Actions');?></th>
		</tr>
		<?php
			$i = 0;
			foreach ($ticket['PpvNotice'] as $ppvNotice):
				$class = null;
				if ($i++ % 2 == 0) {
					$class = ' class="altrow"';
				}
				
				if ($ppvNotice['emailBody'] && !$ppvNotice['emailBodyFileName']) {
					$ppvNotice['emailTo'] = 'LEGACY';
						$ppvNotice['emailTo'] = 'LEGACY';
						$ppvNotice['emailFrom'] = 'LEGACY';
						$ppvNotice['emailCc'] = 'LEGACY';
						$ppvNotice['emailSubject'] = 'LEGACY';
				}
				
			?>
			<tr<?php echo $class;?>>
				<td><?php echo $ppvNotice['ppvNoticeId'];?></td>
				<td><?php echo $ppvNotice['PpvNoticeType']['ppvNoticeTypeName'];?></td>
				<td><?php echo $ppvNotice['emailTo'];?></td>
				<td><?php echo $ppvNotice['emailFrom'];?></td>
				<td><?php echo $ppvNotice['emailSubject'];?></td>
				<td><?php echo $ppvNotice['emailSentDatetime'];?></td>
				<td><?php echo $ppvNotice['initials'];?></td>
				<td class="actions">
					<?php
					echo $html->link('View',
							'/ppv_notices/view/' . $ppvNotice['ppvNoticeId'],
							array(
								'title' => 'View PPV / Notice',
								'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
								'complete' => 'closeModalbox()'
							),
							null,
							false
						);
					?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	<?php endif; ?>
	
	<br />
	<table cellspacing="3" cellpadding="3" border="0" style="border:0px;">
		<tr>
			<td style="border:0px;"><h2>Fixed Price</h2></td>
			<td style="border:0px;"><h2>Auctions</h2></td>
		</tr>
		<tr>
			<td style="border:0px;"><?php echo $html->link('Send Reservation Request', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/2');?></td>
			<td style="border:0px;"><?php echo $html->link('Send Winner Notification', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/5');?></td>
		</tr>
		<tr>
			<td style="border:0px;"><?php echo $html->link('Send Reservation Confirmation', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/1');?></td>
			<td style="border:0px;"><?php echo $html->link('Send Winner Notification w/ checkout', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/6');?></td>
		</tr>
		<tr>
			<td style="border:0px;"><?php echo $html->link('Send Fixed Price - Winner Notification', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/9');?></td>
			<td style="border:0px;"><?php echo $html->link('Send Notification Declined CC', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/7');?></td>
		</tr>
		<tr>
			<td style="border:0px;"><?php echo $html->link('Send Fixed Price - Client Exclusive Email', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/10');?></td>
			<td style="border:0px;"><?php echo $html->link('Send Notification Expired CC', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/8');?></td>
		</tr>
		<tr>
			<td style="border:0px;"><?php echo $html->link('Send Fixed Price - Internal Exclusive Email', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/11');?></td>
			<td style="border:0px;"><?php echo $html->link('Send Client PPV', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/4');?></td>
		</tr>
		<tr>
			<td style="border:0px;">&nbsp;</td>
			<td style="border:0px;"><?php echo $html->link('Send Winner PPV', '/tickets/' . $ticket['Ticket']['ticketId'] . '/ppvNotices/add/3');?></td>
		</tr>
		
	</table>
	
	</div>
</div>

<br />
<div class="collapsible">
	<div class="handle"><?php __('Write-Off');?></div>
	<div class="collapsibleContent related">
	<br />
	<?php if (!empty($ticket['TicketWriteoff']['ticketWriteoffId'])):?>
		<table cellpadding="0" cellspacing="0">
			<tr class="altrow">
				<td width="200">Writeoff Id</td>
				<td><?php echo $ticket['TicketWriteoff']['ticketWriteoffId'];?></td>
			</tr>
			<tr>
				<td width="200">Writeoff Reason</td>
				<td><?php echo $ticket['TicketWriteoff']['TicketWriteoffReason']['ticketWriteoffReasonName'];?></td>
			</tr>
			<tr class="altrow">
				<td width="200">Writeoff Date</td>
				<td><?php echo $ticket['TicketWriteoff']['dateRequested'];?></td>
			</tr>
			<tr>
				<td width="200">Writeoff Notes</td>
				<td><?php echo $ticket['TicketWriteoff']['writeoffNotes'];?></td>
			</tr>
		</table>
		<?php
		echo $html->link('Edit Ticket Write-Off',
			'/ticket_writeoffs/edit/' . $ticket['TicketWriteoff']['ticketWriteoffId'],
			array(
				'title' => 'Edit Ticket Write-Off',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);
		?>
	<?php else: ?>
		<?php
		echo $html->link('Write-Off this Ticket',
			'/tickets/' . $ticket['Ticket']['ticketId'] . '/ticket_writeoffs/add',
			array(
				'title' => 'Ticket Write-Off',
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
				<td><?php echo $ticket['TicketRefund']['dateRequested'];?></td>
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
<div class="collapsible">
	<div class="handle"><?php __("Revenue Allocation ($trackExistsCount)");?></div>
	<div class="collapsibleContent related">
	<br />
	<?php if ($trackDetailExists):?>
		<table cellspacing="0" cellpadding="0">
 			<tr>
 				<th style="text-align:center;">Track Detail Id</th>
 				<th style="text-align:center;">Ticket Id</th>
 				<th style="text-align:center;">Ticket Amount</th>
 				<th style="text-align:center;">Allocated Amount</th>
 				<th style="text-align:center;">Cycle</th>
 				<th style="text-align:center;">Iteration</th>
 				<th style="text-align:center;">Amount Kept</th>
 				<th style="text-align:center;">Amount Remitted</th>
 				<th style="text-align:center;">xy Running Total</th>
 				<th style="text-align:center;">xy Average</th>
 				<th style="text-align:center;">Keep Balance Due</th>
 				<th style="text-align:center;">Initials</th>
 			</tr>
 			<?php foreach ($trackDetails as $k => $v) : ?>
 			<?php $class_alt = ($v['trackDetail']['ticketId'] == $ticket['Ticket']['ticketId']) ? 'class="altrow"' : ''; ?>
 			<?php if (!$class_alt) continue ;?>
 			<tr <?php echo $class_alt;?> >
				<td style="text-align:center;"><?=$v['trackDetail']['trackDetailId'];?></td>
				<td style="text-align:center;"><?=$v['trackDetail']['ticketId'];?></td>
				<td style="text-align:center;"><?=$number->currency($v['trackDetail']['ticketAmount']);?></td>
				<td style="text-align:center;"><?=$number->currency($v['trackDetail']['allocatedAmount']);?></td>
				<td style="text-align:center;"><?=$v['trackDetail']['cycle'];?></td>
				<td style="text-align:center;"><?=$v['trackDetail']['iteration'];?></td>
				<td style="text-align:center;"><?=$number->currency($v['trackDetail']['amountKept']);?></td>
				<td style="text-align:center;"><?=$number->currency($v['trackDetail']['amountRemitted']);?></td>
				<td style="text-align:center;"><?=$number->currency($v['trackDetail']['xyRunningTotal']);?></td>
				<td style="text-align:center;"><?=$number->currency($v['trackDetail']['xyAverage']);?></td>
				<td style="text-align:center;"><?=$number->currency($v['trackDetail']['keepBalDue']);?></td>
				<td style="text-align:center;"><?=$v['trackDetail']['initials'];?></td>
			</tr>
			<?php endforeach; ?>
		</table>
		<?php echo $html->link('View Track Detail', '/tickets/' . $ticket['Ticket']['ticketId'] . '/trackDetails/add');?>
	<?php else: ?>
		<?php
		echo $html->link('Add Track Detail', '/tickets/' . $ticket['Ticket']['ticketId'] . '/trackDetails/add');
		/*
		echo $html->link('Add Track Detail',
			'/tickets/' . $ticket['Ticket']['ticketId'] . '/trackDetails/add',
			array(
				'title' => 'Ticket - Track Detail Revenue Allocation',
				'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
				'complete' => 'closeModalbox()'
				),
			null,
			false
		);*/
		?>
	<?php endif; ?>
	</div>
</div>
<br />
