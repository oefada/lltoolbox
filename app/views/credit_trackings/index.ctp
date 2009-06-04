<div class="creditTrackings index">
<h2>All Credit on File</h2>

<table cellpadding="0" cellspacing="0">
<tr>
	<th>User Id</th>
	<th>Email</th>
	<th>Username</th>
	<th>Balance</th>
	<th>Last Transaction</th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($creditTrackings as $creditTracking):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $creditTracking['creditTracking']['userId']; ?>
		</td>
		<td>
			<?php echo $creditTracking['user']['email']; ?>
		</td>
		<td>
			<?php echo $creditTracking['userSiteExtended']['username']; ?>
		</td>
		<td>
			<?php echo $creditTracking['creditTracking']['balance']; ?>
		</td>
		<td>
			<?php echo $creditTracking['creditTracking']['datetime']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $creditTracking['creditTracking']['userId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>