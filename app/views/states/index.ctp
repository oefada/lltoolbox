<?php $this->set('hideSidebar', true); ?>
<div class="states index">
<h2><?php __('States');?></h2>
<?php if (isset($query)): ?>
<h2>You searched for: <?= $query ?></h2>
<?php endif; ?> 
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('stateId');?></th>
	<th><?php echo $paginator->sort('stateName');?></th>
	<th><?php echo $paginator->sort('countryId');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($states as $state):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $state['State']['id']; ?>
		</td>
		<td>
			<?php echo $state['State']['stateId']; ?>
		</td>
		<td>
			<?php echo $state['State']['stateName']; ?>
		</td>
		<td>
			<?php echo $html->link($state['Country']['countryName'], array('controller'=> 'countries', 'action'=>'edit', $state['Country']['countryId'])); ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $state['State']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $state['State']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $state['State']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>
