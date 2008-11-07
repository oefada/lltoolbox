<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:');
?>
<h2><?php __('View All Packages');?></h2>
<?php if(count($packages) > 0): ?>
<div id='packages-index' class="packages index">
<?= $this->renderElement('ajax_paginator', array('divToPaginate' => 'packages-index', 'showCount' => true))?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('packageName');?></th>
	<th><?php echo $paginator->sort('packageStatusId');?></th>
	<th><?php echo $paginator->sort('startDate');?></th>
	<th><?php echo $paginator->sort('endDate');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($packages as $package):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $package['Package']['packageName']; ?>
		</td>
		<td>
			<?php echo $package['Package']['packageStatusId']; ?>
		</td>
		<td>
			<?php echo $package['Package']['startDate']; ?>
		</td>
		<td>
			<?php echo $package['Package']['endDate']; ?>
		</td>
		<td>
			<?php echo $package['Package']['created']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), "/clients/$clientId/packages/edit/{$package['Package']['packageId']}"); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?= $this->renderElement('ajax_paginator', array('divToPaginate' => 'packages-index'))?>
</div>
<?php else: ?>
	  <div class="blankBar">
	  <h1>
	    <?=$ajax->link("Add the first package for {$client['Client']['name']}", "/clients/$clientId/packages/add", array('update' => 'content-area', 'indicator' => 'loading')) ?>
	  </h1>
	  <p>Create, manage, and delete packages related to this client.</p>
	</div>

<?php endif; ?>