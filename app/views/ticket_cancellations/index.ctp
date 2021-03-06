<div class="ticketCancellations index">
<h2><?php __('TicketCancellations');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('ticketCancellationId');?></th>
	<th><?php echo $paginator->sort('cancellationReasonId');?></th>
	<th><?php echo $paginator->sort('ticketId');?></th>
	<th><?php echo $paginator->sort('dateCancelled');?></th>
	<th><?php echo $paginator->sort('cancellationNotes');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($ticketCancellations as $ticketCancellation):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $ticketCancellation['TicketCancellation']['ticketCancellationId']; ?>
		</td>
		<td>
			<?php echo $html->link($ticketCancellation['cancellationReason']['cancellationReasonName'], array('controller'=> 'cancellation_reasons', 'action'=>'view', $ticketCancellation['cancellationReason']['cancellationReasonId'])); ?>
		</td>
		<td>
			<?php echo $ticketCancellation['TicketCancellation']['ticketId']; ?>
		</td>
		<td>
			<?php echo $ticketCancellation['TicketCancellation']['dateCancelled']; ?>
		</td>
		<td>
			<?php echo $ticketCancellation['TicketCancellation']['cancellationNotes']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $ticketCancellation['TicketCancellation']['ticketCancellationId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $ticketCancellation['TicketCancellation']['ticketCancellationId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $ticketCancellation['TicketCancellation']['ticketCancellationId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ticketCancellation['TicketCancellation']['ticketCancellationId'])); ?>
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
