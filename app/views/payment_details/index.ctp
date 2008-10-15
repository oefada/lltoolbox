<div class="paymentDetails index">
<h2><?php __('PaymentDetails');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('paymentDetailId');?></th>
	<th><?php echo $paginator->sort('worksheetId');?></th>
	<th><?php echo $paginator->sort('creditCardNum');?></th>
	<th><?php echo $paginator->sort('expirationDate');?></th>
	<th><?php echo $paginator->sort('cvv2Value');?></th>
	<th><?php echo $paginator->sort('creditCardFirstName');?></th>
	<th><?php echo $paginator->sort('billingAddress1');?></th>
	<th><?php echo $paginator->sort('billingCity');?></th>
	<th><?php echo $paginator->sort('billingState');?></th>
	<th><?php echo $paginator->sort('billingZip');?></th>
	<th><?php echo $paginator->sort('billingCountry');?></th>
	<th><?php echo $paginator->sort('billingAmount');?></th>
	<th><?php echo $paginator->sort('applyToLOA');?></th>
	<th><?php echo $paginator->sort('applyLoaAuthUsername');?></th>
	<th><?php echo $paginator->sort('paymentTypeId');?></th>
	<th><?php echo $paginator->sort('paymentDate');?></th>
	<th><?php echo $paginator->sort('refundWholeTicket');?></th>
	<th><?php echo $paginator->sort('cardProcessorName');?></th>
	<th><?php echo $paginator->sort('ppResponseDate');?></th>
	<th><?php echo $paginator->sort('ppTransactionId');?></th>
	<th><?php echo $paginator->sort('ppApprovalStatus');?></th>
	<th><?php echo $paginator->sort('ppApprovalCode');?></th>
	<th><?php echo $paginator->sort('ppAvsCode');?></th>
	<th><?php echo $paginator->sort('ppResponseText');?></th>
	<th><?php echo $paginator->sort('ppReasonCode');?></th>
	<th><?php echo $paginator->sort('autoProcessed');?></th>
	<th><?php echo $paginator->sort('successfulCharge');?></th>
	<th><?php echo $paginator->sort('chargedByUsername');?></th>
	<th><?php echo $paginator->sort('creditCardLastName');?></th>
	<th><?php echo $paginator->sort('ppResponseSubcode');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($paymentDetails as $paymentDetail):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['paymentDetailId']; ?>
		</td>
		<td>
			<?php echo $html->link($paymentDetail['Worksheet']['worksheetId'], array('controller'=> 'worksheets', 'action'=>'view', $paymentDetail['Worksheet']['worksheetId'])); ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['creditCardNum']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['expirationDate']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['cvv2Value']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['creditCardFirstName']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['billingAddress1']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['billingCity']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['billingState']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['billingZip']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['billingCountry']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['billingAmount']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['applyToLOA']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['applyLoaAuthUsername']; ?>
		</td>
		<td>
			<?php echo $html->link($paymentDetail['PaymentType']['paymentTypeName'], array('controller'=> 'payment_types', 'action'=>'view', $paymentDetail['PaymentType']['paymentTypeId'])); ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['paymentDate']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['refundWholeTicket']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['cardProcessorName']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['ppResponseDate']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['ppTransactionId']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['ppApprovalStatus']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['ppApprovalCode']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['ppAvsCode']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['ppResponseText']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['ppReasonCode']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['autoProcessed']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['successfulCharge']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['chargedByUsername']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['creditCardLastName']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['ppResponseSubcode']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $paymentDetail['PaymentDetail']['paymentDetailId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $paymentDetail['PaymentDetail']['paymentDetailId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $paymentDetail['PaymentDetail']['paymentDetailId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $paymentDetail['PaymentDetail']['paymentDetailId'])); ?>
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
		<li><?php echo $html->link(__('New PaymentDetail', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Worksheets', true), array('controller'=> 'worksheets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Worksheet', true), array('controller'=> 'worksheets', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Payment Types', true), array('controller'=> 'payment_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Type', true), array('controller'=> 'payment_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
