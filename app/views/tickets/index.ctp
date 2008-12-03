<?php
$this->pageTitle = 'Tickets';
if (isset($query)) {
	$html->addCrumb('Tickets', '/tickets');
	$html->addCrumb('search for '.$query);
} else {
	$html->addCrumb('Tickets');
}
$this->set('hideSidebar', true);
?>

<div id="ticket-index">
	<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'ticket-index', 'showCount' => true)); ?>
<div class="tickets index">
	<?php if (isset($query) && !empty($query)): ?>
		<div style="clear: both">
		<strong>Search Criteria:</strong> <?php echo $query; ?>
		</div>
	<?php endif ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('Ticket Id', 'Ticket.ticketId');?></th>
	<th><?php echo $paginator->sort('Status', 'Ticket.ticketStatusId');?></th>
	<th><?php echo $paginator->sort('Package Name', 'Ticket.packageName');?></th>
	<th><?php echo $paginator->sort('User First name', 'Ticket.userFirstName');?></th>
	<th><?php echo $paginator->sort('User Last Name', 'Ticket.userLastName');?></th>
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
			<?php echo $ticket['TicketStatus']['ticketStatusName']; ?>
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