<?php $this->set('hideSidebar', true); ?>
<?php $this->pageTitle = 'Credit Tracking - View User'; ?>
<h2><?php echo $html->link(__('View all Credits', true), array('action'=>'index')); ?></h2>
<div class="creditTrackings index">
<h2>Credit on File for User Id: <span style="color:black;"><?php echo $creditTrackings[0]['CreditTracking']['userId']; ?></span></h2>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Credit Tracking Id</th>
	<th>Tracking Type</th>
	<th>User Id</th>
	<th>Amount</th>
   	<th>Running Balance</th>
   	<th>Notes</th>
   	<th>Datetime</th>
   	<th>Actions</th>
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
			<?php echo $creditTracking['CreditTracking']['creditTrackingId']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTrackingType']['creditTrackingTypeName']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTracking']['userId']; ?>
		</td>
		<td>
			$<?php echo $creditTracking['CreditTracking']['amount']; ?>
		</td>
		<td>
			$<?php echo $creditTracking['CreditTracking']['balance']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTracking']['notes']; ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTracking']['datetime']; ?>
		</td>
		<td>
			<?php
			if ($canSave == true) {
				echo $html->link(__('Void Entry', true), array('action'=>'delete', $creditTracking['CreditTracking']['creditTrackingId'],'userId' => $creditTracking['CreditTracking']['userId']));
			}
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>

<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List All Credit on File', true), array('action'=>'index')); ?></li>
	</ul>
</div>
