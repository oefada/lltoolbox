<div class="reservations index">
<h2><?php __('Reservations');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('reservationId');?></th>
	<th><?php echo $paginator->sort('ticketId');?></th>
	<th><?php echo $paginator->sort('roomType');?></th>
	<th><?php echo $paginator->sort('numNights');?></th>
	<th><?php echo $paginator->sort('availabilityConfirmDate');?></th>
	<th><?php echo $paginator->sort('availabilityConfirmUserId');?></th>
	<th><?php echo $paginator->sort('customerConsentDate');?></th>
	<th><?php echo $paginator->sort('arrivalDate');?></th>
	<th><?php echo $paginator->sort('departureDate');?></th>
	<th><?php echo $paginator->sort('reservationRequestDate');?></th>
	<th><?php echo $paginator->sort('reservationMadeDate');?></th>
	<th><?php echo $paginator->sort('reservationMadeUserId');?></th>
	<th><?php echo $paginator->sort('reservationConfirmToCustomer');?></th>
	<th><?php echo $paginator->sort('reservationConfirmNum');?></th>
	<th><?php echo $paginator->sort('reservationConfirmUserId');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($reservations as $reservation):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $reservation['Reservation']['ticketId']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['roomType']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['numNights']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['availabilityConfirmDate']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['availabilityConfirmUserId']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['customerConsentDate']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['arrivalDate']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['departureDate']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['reservationRequestDate']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['reservationMadeDate']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['reservationMadeUserId']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['reservationConfirmToCustomer']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['reservationConfirmNum']; ?>
		</td>
		<td>
			<?php echo $reservation['Reservation']['reservationConfirmUserId']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $reservation['Reservation']['ticketId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $reservation['Reservation']['ticketId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $reservation['Reservation']['ticketId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $reservation['Reservation']['ticketId'])); ?>
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
		<li><?php echo $html->link(__('New Reservation', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Tickets', true), array('controller'=> 'tickets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket', true), array('controller'=> 'tickets', 'action'=>'add')); ?> </li>
	</ul>
</div>
