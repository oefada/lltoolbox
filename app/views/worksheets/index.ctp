<div class="worksheets index">
<h2><?php __('Worksheets');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('worksheetId');?></th>
	<th><?php echo $paginator->sort('worksheetStatusId');?></th>
	<th><?php echo $paginator->sort('parentWorksheetId');?></th>
	<th><?php echo $paginator->sort('packageId');?></th>
	<th><?php echo $paginator->sort('offerId');?></th>
	<th><?php echo $paginator->sort('requestId');?></th>
	<th><?php echo $paginator->sort('bidId');?></th>
	<th><?php echo $paginator->sort('requestInfo');?></th>
	<th><?php echo $paginator->sort('notes');?></th>
	<th><?php echo $paginator->sort('isFlake');?></th>
	<th><?php echo $paginator->sort('paymentAuthDate');?></th>
	<th><?php echo $paginator->sort('paymentSettleDate');?></th>
	<th><?php echo $paginator->sort('billingPrice');?></th>
	<th><?php echo $paginator->sort('bookingPrice');?></th>
	<th><?php echo $paginator->sort('customerId');?></th>
	<th><?php echo $paginator->sort('customerFirstName');?></th>
	<th><?php echo $paginator->sort('customerLastName');?></th>
	<th><?php echo $paginator->sort('customerEmail1');?></th>
	<th><?php echo $paginator->sort('customerWorkPhone');?></th>
	<th><?php echo $paginator->sort('customerHomePhone');?></th>
	<th><?php echo $paginator->sort('customerMobilePhone');?></th>
	<th><?php echo $paginator->sort('customerFax');?></th>
	<th><?php echo $paginator->sort('customerAddress1');?></th>
	<th><?php echo $paginator->sort('customerAddress2');?></th>
	<th><?php echo $paginator->sort('customerAddress3');?></th>
	<th><?php echo $paginator->sort('customerCity');?></th>
	<th><?php echo $paginator->sort('customerState');?></th>
	<th><?php echo $paginator->sort('customerCountry');?></th>
	<th><?php echo $paginator->sort('customerZip');?></th>
	<th><?php echo $paginator->sort('completedUserId');?></th>
	<th><?php echo $paginator->sort('completedDate');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($worksheets as $worksheet):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $worksheet['Worksheet']['worksheetId']; ?>
		</td>
		<td>
			<?php echo $html->link($worksheet['WorksheetStatus']['worksheetStatusName'], array('controller'=> 'worksheet_statuses', 'action'=>'view', $worksheet['WorksheetStatus']['worksheetStatusId'])); ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['parentWorksheetId']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['packageId']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['offerId']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['requestId']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['bidId']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['requestInfo']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['notes']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['isFlake']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['paymentAuthDate']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['paymentSettleDate']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['billingPrice']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['bookingPrice']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerId']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerFirstName']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerLastName']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerEmail1']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerWorkPhone']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerHomePhone']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerMobilePhone']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerFax']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerAddress1']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerAddress2']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerAddress3']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerCity']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerState']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerCountry']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['customerZip']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['completedUserId']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['completedDate']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $worksheet['Worksheet']['worksheetId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $worksheet['Worksheet']['worksheetId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $worksheet['Worksheet']['worksheetId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $worksheet['Worksheet']['worksheetId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Worksheet', true), array('action'=>'add')); ?></li>
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
