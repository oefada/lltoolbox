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
	<th><?php echo $paginator->sort('worksheetTypeId');?></th>
	<th><?php echo $paginator->sort('creditCardNum');?></th>
	<th><?php echo $paginator->sort('expirationDate');?></th>
	<th><?php echo $paginator->sort('cvv2Value');?></th>
	<th><?php echo $paginator->sort('creditCardName');?></th>
	<th><?php echo $paginator->sort('billingAddress1');?></th>
	<th><?php echo $paginator->sort('billingCity');?></th>
	<th><?php echo $paginator->sort('billingState');?></th>
	<th><?php echo $paginator->sort('billingZip');?></th>
	<th><?php echo $paginator->sort('billingCountry');?></th>
	<th><?php echo $paginator->sort('billingAmount');?></th>
	<th><?php echo $paginator->sort('applyToLOA');?></th>
	<th><?php echo $paginator->sort('applyLoaAuthUserId');?></th>
	<th><?php echo $paginator->sort('settlementId');?></th>
	<th><?php echo $paginator->sort('paymentTypeId');?></th>
	<th><?php echo $paginator->sort('paymentDate');?></th>
	<th><?php echo $paginator->sort('wholeRefundId');?></th>
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
			<?php echo $paymentDetail['PaymentDetail']['worksheetTypeId']; ?>
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
			<?php echo $paymentDetail['PaymentDetail']['creditCardName']; ?>
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
			<?php echo $paymentDetail['PaymentDetail']['applyLoaAuthUserId']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['settlementId']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['paymentTypeId']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['paymentDate']; ?>
		</td>
		<td>
			<?php echo $paymentDetail['PaymentDetail']['wholeRefundId']; ?>
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
	</ul>
</div>
