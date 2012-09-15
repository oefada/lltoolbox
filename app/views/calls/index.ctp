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
			<th>I</th>
			<th>C</th>
			<th>T</th>
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
				<td><?php echo $call['Call']['callId']; ?></td>
				<td><?php echo $call['Call']['representative']; ?></td>
				<td><?php echo $call['Call']['interactionType']; ?></td>
				<td><?php echo $call['Call']['contactType']; ?></td>
				<td><?php echo $call['Call']['contactTopic']; ?></td>
				<td><?php echo $call['Call']['userId']; ?></td>
				<td><?php echo $call['Call']['clientId']; ?></td>
				<td><?php echo $call['Call']['ticketId']; ?></td>
				<td><?php echo $call['Call']['notes']; ?></td>
				<td nowrap><?php echo $call['Call']['created']; ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
