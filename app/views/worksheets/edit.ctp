<div class="worksheets form">
<?php echo $form->create('Worksheet');?>
	<fieldset>
 		<legend><?php __('Edit Worksheet');?></legend>
	<?php
		echo $form->input('worksheetId');
		echo $form->input('worksheetStatusId');
		echo $form->input('parentWorksheetId');
		echo $form->input('packageId');
		echo $form->input('offerId');
		echo $form->input('requestId');
		echo $form->input('bidId');
		echo $form->input('requestInfo');
		echo $form->input('notes');
		echo $form->input('isFlake');
		echo $form->input('paymentAuthDate');
		echo $form->input('paymentSettleDate');
		echo $form->input('billingPrice');
		echo $form->input('bookingPrice');
		echo $form->input('customerId');
		echo $form->input('customerFirstName');
		echo $form->input('customerLastName');
		echo $form->input('customerEmail1');
		echo $form->input('customerWorkPhone');
		echo $form->input('customerHomePhone');
		echo $form->input('customerMobilePhone');
		echo $form->input('customerFax');
		echo $form->input('customerAddress1');
		echo $form->input('customerAddress2');
		echo $form->input('customerAddress3');
		echo $form->input('customerCity');
		echo $form->input('customerState');
		echo $form->input('customerCountry');
		echo $form->input('customerZip');
		echo $form->input('completedUserId');
		echo $form->input('completedDate');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Worksheet.worksheetId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Worksheet.worksheetId'))); ?></li>
		<li><?php echo $html->link(__('List Worksheets', true), array('action'=>'index'));?></li>
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
