<div style="float:right;">
	<?=$html->link('<span><b class="icon"></b>Export Report</span>', array(
		'controller' => 'calls',
		'action' => 'index',
		'format' => 'csv',
	), array(
		'escape' => false,
		'class' => 'button excel',
	));
?>
</div>
<h2>Calls</h2>
<p>
	Press the ` key to open the CS tool (the one above the TAB key).
</p>
<table>
	<?php $altRow = false; ?>
	<thead>
		<tr>
			<th>Id</th>
			<th>Rep</th>
			<th>Interaction</th>
			<th>Type</th>
			<th>Topic</th>
			<th>User</th>
			<th>Client</th>
			<th>Ticket</th>
			<th>Notes</th>
			<th>Created</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($calls as $call): ?>
			<?php $altRow = !$altRow; ?>
			<tr class="<?php echo $altRow ? 'altrow' : ''; ?>">
				<td><?php echo $html->link($call['Call']['callId'], array('action'=>'view',$call['Call']['callId'])); ?></td>
				<td><?php echo $call['Call']['representative']; ?></td>
				<td><?php echo isset(Call::$interactionTypes[$call['Call']['interactionType']])?Call::$interactionTypes[$call['Call']['interactionType']]:$call['Call']['interactionType']; ?></td>
				<td><?php echo isset(Call::$contactTypes[$call['Call']['contactType']])?Call::$contactTypes[$call['Call']['contactType']]:$call['Call']['contactType']; ?></td>
				<td><?php echo isset(Call::$contactTopics[$call['Call']['contactTopic']])?Call::$contactTopics[$call['Call']['contactTopic']]:$call['Call']['contactTopic']; ?></td>
				<td><?php echo $html->link($call['User']['firstName'].' '.$call['User']['lastName'],array('controller'=>'users','action'=>'view',$call['User']['userId'])); ?></td>
				<td><?php echo $html->link($call['Client']['name'],array('controller'=>'clients','action'=>'view',$call['Client']['clientId'])); ?></td>
				<td><?php echo $html->link($call['Ticket']['ticketId'],array('controller'=>'tickets','action'=>'edit',$call['Ticket']['ticketId'])); ?></td>
				<td><?php echo str_replace("\n", $html->image('carriage_return.png'), htmlentities($call['Call']['notes'])); ?></td>
				<td nowrap><?php echo $call['Call']['created']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
