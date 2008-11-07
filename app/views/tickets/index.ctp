<?php if (isset($user)):
$this->pageTitle = $this->pageTitle = $user['User']['firstName'].' '.$user['User']['lastName'].$html2->c($user['User']['userId'], 'User Id:');
endif ?>
<div id='tickets-index' class="tickets index">
<h2><?php __('Tickets');?></h2>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'tickets-index', 'showCount' => true))?>
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
			<?php echo $html->link(__('View Details', true), array('controller' => 'tickets', 'action'=>'view', $ticket['Ticket']['ticketId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'tickets-index'))?>
</div>