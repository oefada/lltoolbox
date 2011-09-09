<?php $this->set('hideSidebar', true); ?>
<?php $this->pageTitle = 'Credit Tracking'; ?>
<div class="creditTrackings index">
<?php if (isset($query)): ?>
	<h2>You searched for: <?= $query ?></h2>
<?php endif; ?>
<p style="float: right">
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<?php echo $this->renderElement('ajax_paginator'); ?>
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
			<?php echo $creditTracking['CreditTracking']['userId']; ?>
		</td>
		<td>
			<?php echo $creditTracking['User']['email']; ?>
		</td>
		<td>
			<?php echo $creditTracking['UserSiteExtended']['username']; ?>
		</td>
		<td>
			$<?php echo number_format($creditTracking['CreditTracking']['balance'],2); ?>
		</td>
		<td>
			<?php echo $creditTracking['CreditTracking']['datetime']; ?>
		</td>
		<td class="actions">
			<?php if ($canSave == true) echo $html->link(__('Edit', true), array('action'=>'edit', $creditTracking['CreditTracking']['creditTrackingId'])); ?>
			<?php echo $html->link(__('View', true), array('action'=>'view', $creditTracking['CreditTracking']['userId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Add Manual Entry', true), array('action'=>'add')); ?></li>
	</ul>
</div>
<?php echo $this->renderElement('ajax_paginator'); ?>