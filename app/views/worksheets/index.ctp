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
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('userFirstName');?></th>
	<th><?php echo $paginator->sort('userLastName');?></th>
	<th><?php echo $paginator->sort('userEmail1');?></th>
	<th><?php echo $paginator->sort('userWorkPhone');?></th>
	<th><?php echo $paginator->sort('userHomePhone');?></th>
	<th><?php echo $paginator->sort('userMobilePhone');?></th>
	<th><?php echo $paginator->sort('userFax');?></th>
	<th><?php echo $paginator->sort('userAddress1');?></th>
	<th><?php echo $paginator->sort('userAddress2');?></th>
	<th><?php echo $paginator->sort('userAddress3');?></th>
	<th><?php echo $paginator->sort('userCity');?></th>
	<th><?php echo $paginator->sort('userState');?></th>
	<th><?php echo $paginator->sort('userCountry');?></th>
	<th><?php echo $paginator->sort('userZip');?></th>
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
			<?php echo $worksheet['Worksheet']['userId']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userFirstName']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userLastName']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userEmail1']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userWorkPhone']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userHomePhone']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userMobilePhone']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userFax']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userAddress1']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userAddress2']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userAddress3']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userCity']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userState']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userCountry']; ?>
		</td>
		<td>
			<?php echo $worksheet['Worksheet']['userZip']; ?>
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
