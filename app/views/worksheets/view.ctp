<div class="worksheets view">
<h2><?php  __('Worksheet');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['worksheetId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Worksheet Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($worksheet['WorksheetStatus']['worksheetStatusName'], array('controller'=> 'worksheet_statuses', 'action'=>'view', $worksheet['WorksheetStatus']['worksheetStatusId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ParentWorksheetId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['parentWorksheetId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['packageId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('OfferId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['offerId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RequestId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['requestId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BidId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['bidId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RequestInfo'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['requestInfo']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Notes'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['notes']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('IsFlake'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['isFlake']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PaymentAuthDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['paymentAuthDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PaymentSettleDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['paymentSettleDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BillingPrice'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['billingPrice']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BookingPrice'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['bookingPrice']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerFirstName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerFirstName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerLastName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerLastName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerEmail1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerEmail1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerWorkPhone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerWorkPhone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerHomePhone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerHomePhone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerMobilePhone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerMobilePhone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerFax'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerFax']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerAddress1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerAddress1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerAddress2'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerAddress2']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerAddress3'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerAddress3']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerCity'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerCity']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerState'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerState']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerCountry'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerCountry']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerZip'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['customerZip']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CompletedUserId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['completedUserId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CompletedDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['completedDate']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Worksheet', true), array('action'=>'edit', $worksheet['Worksheet']['worksheetId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Worksheet', true), array('action'=>'delete', $worksheet['Worksheet']['worksheetId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $worksheet['Worksheet']['worksheetId'])); ?> </li>
		<li><?php echo $html->link(__('List Worksheets', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Worksheet', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Worksheet Statuses', true), array('controller'=> 'worksheet_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Worksheet Status', true), array('controller'=> 'worksheet_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Worksheet Cancellations', true), array('controller'=> 'worksheet_cancellations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Worksheet Cancellation', true), array('controller'=> 'worksheet_cancellations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Worksheet Refunds', true), array('controller'=> 'worksheet_refunds', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Worksheet Refund', true), array('controller'=> 'worksheet_refunds', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Reservations', true), array('controller'=> 'reservations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Reservation', true), array('controller'=> 'reservations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Payment Details', true), array('controller'=> 'payment_details', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Detail', true), array('controller'=> 'payment_details', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ppv Notices', true), array('controller'=> 'ppv_notices', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ppv Notice', true), array('controller'=> 'ppv_notices', 'action'=>'add')); ?> </li>
	</ul>
</div>
	<div class="related">
		<h3><?php  __('Related Worksheet Cancellations');?></h3>
	<?php if (!empty($worksheet['WorksheetCancellation'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetCancellationId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetCancellation']['worksheetCancellationId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CancellationReasonId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetCancellation']['cancellationReasonId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetCancellation']['worksheetId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateCancelled');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetCancellation']['dateCancelled'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CancellationNotes');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetCancellation']['cancellationNotes'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
				<li><?php echo $html->link(__('Edit Worksheet Cancellation', true), array('controller'=> 'worksheet_cancellations', 'action'=>'edit', $worksheet['WorksheetCancellation']['worksheetCancellationId'])); ?></li>
			</ul>
		</div>
	</div>
		<div class="related">
		<h3><?php  __('Related Worksheet Refunds');?></h3>
	<?php if (!empty($worksheet['WorksheetRefund'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetRefundId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetRefund']['worksheetRefundId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RefundReasonId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetRefund']['refundReasonId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetRefund']['worksheetId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateRefunded');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetRefund']['dateRefunded'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AmountRefunded');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['WorksheetRefund']['amountRefunded'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
				<li><?php echo $html->link(__('Edit Worksheet Refund', true), array('controller'=> 'worksheet_refunds', 'action'=>'edit', $worksheet['WorksheetRefund']['worksheetRefundId'])); ?></li>
			</ul>
		</div>
	</div>
		<div class="related">
		<h3><?php  __('Related Reservations');?></h3>
	<?php if (!empty($worksheet['Reservation'])):?>
		<dl>	<?php $i = 0; $class = ' class="altrow"';?>
			<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('WorksheetId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['worksheetId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RoomType');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['roomType'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumNights');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['numNights'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AvailabilityConfirmDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['availabilityConfirmDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AvailabilityConfirmUserId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['availabilityConfirmUserId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerConsentDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['customerConsentDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ArrivalDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['arrivalDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DepartureDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['departureDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationRequestDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['reservationRequestDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationMadeDate');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['reservationMadeDate'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationMadeUserId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['reservationMadeUserId'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationConfirmToCustomer');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['reservationConfirmToCustomer'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationConfirmNum');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['reservationConfirmNum'];?>
&nbsp;</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReservationConfirmUserId');?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
	<?php echo $worksheet['Reservation']['reservationConfirmUserId'];?>
&nbsp;</dd>
		</dl>
	<?php endif; ?>
		<div class="actions">
			<ul>
				<li><?php echo $html->link(__('Edit Reservation', true), array('controller'=> 'reservations', 'action'=>'edit', $worksheet['Reservation']['worksheetId'])); ?></li>
			</ul>
		</div>
	</div>
	<div class="related">
	<h3><?php __('Related Payment Details');?></h3>
	<?php if (!empty($worksheet['PaymentDetail'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('PaymentDetailId'); ?></th>
		<th><?php __('WorksheetId'); ?></th>
		<th><?php __('WorksheetTypeId'); ?></th>
		<th><?php __('CreditCardNum'); ?></th>
		<th><?php __('ExpirationDate'); ?></th>
		<th><?php __('Cvv2Value'); ?></th>
		<th><?php __('CreditCardName'); ?></th>
		<th><?php __('BillingAddress1'); ?></th>
		<th><?php __('BillingCity'); ?></th>
		<th><?php __('BillingState'); ?></th>
		<th><?php __('BillingZip'); ?></th>
		<th><?php __('BillingCountry'); ?></th>
		<th><?php __('BillingAmount'); ?></th>
		<th><?php __('ApplyToLOA'); ?></th>
		<th><?php __('ApplyLoaAuthUserId'); ?></th>
		<th><?php __('SettlementId'); ?></th>
		<th><?php __('PaymentTypeId'); ?></th>
		<th><?php __('PaymentDate'); ?></th>
		<th><?php __('WholeRefundId'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($worksheet['PaymentDetail'] as $paymentDetail):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $paymentDetail['paymentDetailId'];?></td>
			<td><?php echo $paymentDetail['worksheetId'];?></td>
			<td><?php echo $paymentDetail['worksheetTypeId'];?></td>
			<td><?php echo $paymentDetail['creditCardNum'];?></td>
			<td><?php echo $paymentDetail['expirationDate'];?></td>
			<td><?php echo $paymentDetail['cvv2Value'];?></td>
			<td><?php echo $paymentDetail['creditCardName'];?></td>
			<td><?php echo $paymentDetail['billingAddress1'];?></td>
			<td><?php echo $paymentDetail['billingCity'];?></td>
			<td><?php echo $paymentDetail['billingState'];?></td>
			<td><?php echo $paymentDetail['billingZip'];?></td>
			<td><?php echo $paymentDetail['billingCountry'];?></td>
			<td><?php echo $paymentDetail['billingAmount'];?></td>
			<td><?php echo $paymentDetail['applyToLOA'];?></td>
			<td><?php echo $paymentDetail['applyLoaAuthUserId'];?></td>
			<td><?php echo $paymentDetail['settlementId'];?></td>
			<td><?php echo $paymentDetail['paymentTypeId'];?></td>
			<td><?php echo $paymentDetail['paymentDate'];?></td>
			<td><?php echo $paymentDetail['wholeRefundId'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'payment_details', 'action'=>'view', $paymentDetail['paymentDetailId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'payment_details', 'action'=>'edit', $paymentDetail['paymentDetailId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'payment_details', 'action'=>'delete', $paymentDetail['paymentDetailId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $paymentDetail['paymentDetailId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Payment Detail', true), array('controller'=> 'payment_details', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Ppv Notices');?></h3>
	<?php if (!empty($worksheet['PpvNotice'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('PpvNoticeId'); ?></th>
		<th><?php __('PpvNoticeTypeId'); ?></th>
		<th><?php __('WorksheetId'); ?></th>
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
		foreach ($worksheet['PpvNotice'] as $ppvNotice):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $ppvNotice['ppvNoticeId'];?></td>
			<td><?php echo $ppvNotice['ppvNoticeTypeId'];?></td>
			<td><?php echo $ppvNotice['worksheetId'];?></td>
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
