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
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Package'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($worksheet['Package']['packageId'], array('controller'=> 'packages', 'action'=>'view', $worksheet['Package']['packageId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Offer'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($worksheet['Offer']['offerId'], array('controller'=> 'offers', 'action'=>'view', $worksheet['Offer']['offerId'])); ?>
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
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserFirstName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userFirstName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserLastName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userLastName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserEmail1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userEmail1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserWorkPhone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userWorkPhone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserHomePhone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userHomePhone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserMobilePhone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userMobilePhone']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserFax'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userFax']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserAddress1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userAddress1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserAddress2'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userAddress2']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserAddress3'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userAddress3']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserCity'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userCity']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserState'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userState']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserCountry'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userCountry']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UserZip'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['userZip']; ?>
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
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('KeepAmount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['keepAmount']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RemitAmount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['remitAmount']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ComissionAmount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $worksheet['Worksheet']['comissionAmount']; ?>
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
		<li><?php echo $html->link(__('List Packages', true), array('controller'=> 'packages', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Package', true), array('controller'=> 'packages', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Offers', true), array('controller'=> 'offers', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer', true), array('controller'=> 'offers', 'action'=>'add')); ?> </li>
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
		<th><?php __('CreditCardName'); ?></th>
		<th><?php __('PaymentDate'); ?></th>
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
			<td><?php echo $paymentDetail['creditCardName'];?></td>
			<td><?php echo $paymentDetail['paymentDate'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'payment_details', 'action'=>'view', $paymentDetail['paymentDetailId'])); ?>
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
