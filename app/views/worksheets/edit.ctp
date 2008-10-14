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
		echo $form->input('userId');
		echo $form->input('userFirstName');
		echo $form->input('userLastName');
		echo $form->input('userEmail1');
		echo $form->input('userWorkPhone');
		echo $form->input('userHomePhone');
		echo $form->input('userMobilePhone');
		echo $form->input('userFax');
		echo $form->input('userAddress1');
		echo $form->input('userAddress2');
		echo $form->input('userAddress3');
		echo $form->input('userCity');
		echo $form->input('userState');
		echo $form->input('userCountry');
		echo $form->input('userZip');
		echo $form->input('completedUserId');
		echo $form->input('completedDate');
		echo $form->input('keepAmount');
		echo $form->input('remitAmount');
		echo $form->input('comissionAmount');
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
