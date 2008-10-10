<div class="worksheetRefunds index">
<h2><?php __('WorksheetRefunds');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('worksheetRefundId');?></th>
	<th><?php echo $paginator->sort('refundReasonId');?></th>
	<th><?php echo $paginator->sort('worksheetId');?></th>
	<th><?php echo $paginator->sort('dateRefunded');?></th>
	<th><?php echo $paginator->sort('amountRefunded');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($worksheetRefunds as $worksheetRefund):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $worksheetRefund['WorksheetRefund']['worksheetRefundId']; ?>
		</td>
		<td>
			<?php echo $html->link($worksheetRefund['RefundReason']['refundReasonName'], array('controller'=> 'refund_reasons', 'action'=>'view', $worksheetRefund['RefundReason']['refundReasonId'])); ?>
		</td>
		<td>
			<?php echo $worksheetRefund['WorksheetRefund']['worksheetId']; ?>
		</td>
		<td>
			<?php echo $worksheetRefund['WorksheetRefund']['dateRefunded']; ?>
		</td>
		<td>
			<?php echo $worksheetRefund['WorksheetRefund']['amountRefunded']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $worksheetRefund['WorksheetRefund']['worksheetRefundId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $worksheetRefund['WorksheetRefund']['worksheetRefundId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $worksheetRefund['WorksheetRefund']['worksheetRefundId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $worksheetRefund['WorksheetRefund']['worksheetRefundId'])); ?>
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
		<li><?php echo $html->link(__('New WorksheetRefund', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Refund Reasons', true), array('controller'=> 'refund_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Refund Reason', true), array('controller'=> 'refund_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
