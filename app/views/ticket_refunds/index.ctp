<div class="ticketRefunds index">
<h2><?php __('TicketRefunds');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('ticketRefundId');?></th>
	<th><?php echo $paginator->sort('refundReasonId');?></th>
	<th><?php echo $paginator->sort('ticketId');?></th>
	<th><?php echo $paginator->sort('dateRefunded');?></th>
	<th><?php echo $paginator->sort('amountRefunded');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($ticketRefunds as $ticketRefund):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $ticketRefund['TicketRefund']['ticketRefundId']; ?>
		</td>
		<td>
			<?php echo $html->link($ticketRefund['RefundReason']['refundReasonName'], array('controller'=> 'refund_reasons', 'action'=>'view', $ticketRefund['RefundReason']['refundReasonId'])); ?>
		</td>
		<td>
			<?php echo $ticketRefund['TicketRefund']['ticketId']; ?>
		</td>
		<td>
			<?php echo $ticketRefund['TicketRefund']['dateRefunded']; ?>
		</td>
		<td>
			<?php echo $ticketRefund['TicketRefund']['amountRefunded']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $ticketRefund['TicketRefund']['ticketRefundId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $ticketRefund['TicketRefund']['ticketRefundId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $ticketRefund['TicketRefund']['ticketRefundId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ticketRefund['TicketRefund']['ticketRefundId'])); ?>
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
		<li><?php echo $html->link(__('New TicketRefund', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Refund Reasons', true), array('controller'=> 'refund_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Refund Reason', true), array('controller'=> 'refund_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
