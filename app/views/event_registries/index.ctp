<?php
$this->pageTitle = 'Event Registries';
$this->set('hideSidebar', true);
?>


<div class="clients index">
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('eventRegistryId');?></th>
	<th><?php echo $paginator->sort('eventTitle');?></th>
	<th><?php echo $paginator->sort('dateCreated');?></th>
	<th><?php echo $paginator->sort('registryUrl');?></th>
	<th><?php echo $paginator->sort('eventName');?></th>
	<th><?php echo $paginator->sort('registrant1_firstName');?></th>
	<th><?php echo $paginator->sort('registrant1_lastName');?></th>
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('balance');?></th>
</tr>

<?php
$i = 0;
foreach ($registries as $registry):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>

		<td>
			<?php echo $html->link(__($registry['EventRegistry']['eventRegistryId'], true), array('action'=>'edit', $registry['EventRegistry']['eventRegistryId'])); ?>
		</td>
		<td><strong>
			<?php echo $html->link(__($registry['EventRegistry']['eventTitle'], true), array('action'=>'edit', $registry['EventRegistry']['eventRegistryId'])); ?>
		</strong></td>
		<td><?php echo date('Y-M-d h:i a', strtotime($registry['EventRegistry']['dateCreated'])); ?></td>
		<td><?php echo $registry['EventRegistry']['registryUrl']; ?></td>
		<td><?php echo $registry['EventRegistryType']['eventName']; ?></td>
		<td><?php echo $registry['EventRegistry']['registrant1_firstName']; ?></td>
		<td><?php echo $registry['EventRegistry']['registrant1_lastName']; ?></td>

		<td>
			<?php echo $html->link(__($registry['EventRegistry']['userId'], true), '/users/edit/' . $registry['EventRegistry']['userId']) . " - " . $registry['User']['firstName'] . " " . $registry['User']['lastName']; ?>
		</td>
		<td>$<?php echo number_format($registry['0']['balance'], 2); ?></td>
	</tr>
	
<? endforeach; ?>
</div>


</table>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>