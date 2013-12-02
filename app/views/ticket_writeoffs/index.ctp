<div class="ticketWriteoffs index">
<h2><?php __('TicketWriteoffs');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('ticketWriteoffId');?></th>
	<th><?php echo $paginator->sort('ticketWriteoffReasonId');?></th>
	<th><?php echo $paginator->sort('ticketId');?></th>
	<th><?php echo $paginator->sort('dateRequested');?></th>
	<th><?php echo $paginator->sort('writeoffNotes');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($ticketWriteoffs as $ticketWriteoff):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $ticketWriteoff['TicketWriteoff']['ticketWriteoffId']; ?>
		</td>
		<td>
			<?php echo $html->link($ticketWriteoff['writeoffReason']['writeoffReasonName'], array('controller'=> 'ticket_writeoff_reasons', 'action'=>'view', $ticketWriteoff['writeoffReason']['writeoffReasonId'])); ?>
		</td>
		<td>
			<?php echo $ticketWriteoff['TicketWriteoff']['ticketId']; ?>
		</td>
		<td>
			<?php echo $ticketWriteoff['TicketWriteoff']['dateRequested']; ?>
		</td>
		<td>
			<?php echo $ticketWriteoff['TicketWriteoff']['writeoffNotes']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $ticketWriteoff['TicketWriteoff']['ticketWriteoffId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $ticketWriteoff['TicketWriteoff']['ticketWriteoffId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $ticketWriteoff['TicketWriteoff']['ticketWriteoffId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ticketWriteoff['TicketWriteoff']['ticketWriteoffId'])); ?>
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
