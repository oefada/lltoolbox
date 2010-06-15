<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']);
?>
<?php if(count($packages) > 0): ?>
<div id='packages-index' class="packages index">
<h2><?php __('View All Packages');?></h2>
<?= $this->renderElement('ajax_paginator', array('showCount' => true))?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('LOA ID', 'ClientLoaPackageRel.loaId', array('url' => array('clientId' => $clientId)));?></th>
	<th><?php echo $paginator->sort('Package Name', 'Package.packageName', array('url' => array('clientId' => $clientId)));?></th>
	<th><?php echo $paginator->sort('Package Status', 'Package.packageStatusId', array('url' => array('clientId' => $clientId)));?></th>
	<th><?php echo $paginator->sort('Start Date', 'Package.startDate', array('url' => array('clientId' => $clientId)));?></th>
	<th><?php echo $paginator->sort('End Date', 'Package.endDate', array('url' => array('clientId' => $clientId)));?></th>
	<th><?php echo $paginator->sort('Created', 'Package.created', array('url' => array('clientId' => $clientId)));?></th>
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
			<?php echo $package['ClientLoaPackageRel']['loaId']; ?>
		</td>
		<td>
			<?php echo $html->link($package['Package']['packageName'], "/clients/$clientId/packages/edit/{$package['Package']['packageId']}"); ?>
		</td>
		<td>
			<?php echo $packageStatusIds[$package['Package']['packageStatusId']]; ?>
		</td>
		<td>
			<?php echo $html2->date($package['Package']['startDate']); ?>
		</td>
		<td>
			<?php echo $html2->date($package['Package']['endDate']); ?>
		</td>
		<td>
			<?php echo $html2->date($package['Package']['created']); ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), "/clients/$clientId/packages/edit/{$package['Package']['packageId']}"); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?= $this->renderElement('ajax_paginator')?>
</div>
<?php else: ?>
	  <div class="blankBar">
	  <h1>
	    <?=$ajax->link("Add the first package for {$client['Client']['name']}", "/clients/$clientId/packages/add", array('update' => 'content-area', 'indicator' => 'loading')) ?>
	  </h1>
	  <p>Create, manage, and delete packages related to this client.</p>
	</div>

<?php endif; ?>