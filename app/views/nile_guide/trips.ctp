<h2>Trips</h2>

<table>
	<tr>
		<th>Id</th>
		<th>Title</th>
		<th>Summary</th>
		<th>NG Id</th>
		<th>Dest Id</th>
		<th>Published</th>
	</tr>
	<?php foreach ($trips as $trip): ?>
		<tr>
			<td><?php echo $html->link($trip['NileGuideTrip']['id'], array('action'=>'trip', $trip['NileGuideTrip']['id'])); ?></td>
			<td><?php echo $trip['NileGuideTrip']['title']; ?></td>
			<td><?php echo substr(strip_tags($trip['NileGuideTrip']['summary']), 0, 100); ?>&hellip;</td>
			<td><?php echo $trip['NileGuideTrip']['ngId']; ?></td>
			<td><?php echo $trip['NileGuideTrip']['nileGuideDestinationId']; ?></td>
			<td><?php echo $trip['NileGuideTrip']['publish']; ?></td>
		</tr>
	<?php endforeach; ?>
</table>
