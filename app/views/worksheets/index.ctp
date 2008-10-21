<div class="tickets index">
<h2><?php __('Tickets');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('ticketId');?></th>
	<th><?php echo $paginator->sort('ticketStatusId');?></th>
	<th><?php echo $paginator->sort('PackageName');?></th>
	<th><?php echo $paginator->sort('userFirstName');?></th>
	<th><?php echo $paginator->sort('userLastName');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($tickets as $ticket):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $ticket['Ticket']['ticketId']; ?>
		</td>
		<td>
			<?php echo $html->link($ticket['TicketStatus']['ticketStatusName'], array('controller'=> 'ticket_statuses', 'action'=>'view', $ticket['TicketStatus']['ticketStatusId'])); ?>
		</td>
		<td>
			<?php echo $ticket['Package']['packageName']; ?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['userFirstName']; ?>
		</td>
		<td>
			<?php echo $ticket['Ticket']['userLastName']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $ticket['Ticket']['ticketId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $ticket['Ticket']['ticketId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $ticket['Ticket']['ticketId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ticket['Ticket']['ticketId'])); ?>
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
		<li><?php echo $html->link(__('New Ticket', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Ticket Statuses', true), array('controller'=> 'ticket_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket Status', true), array('controller'=> 'ticket_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ticket Cancellations', true), array('controller'=> 'ticket_cancellations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket Cancellation', true), array('controller'=> 'ticket_cancellations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ticket Refunds', true), array('controller'=> 'ticket_refunds', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket Refund', true), array('controller'=> 'ticket_refunds', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Reservations', true), array('controller'=> 'reservations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Reservation', true), array('controller'=> 'reservations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Payment Details', true), array('controller'=> 'payment_details', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Payment Detail', true), array('controller'=> 'payment_details', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ppv Notices', true), array('controller'=> 'ppv_notices', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ppv Notice', true), array('controller'=> 'ppv_notices', 'action'=>'add')); ?> </li>
	</ul>
</div>
