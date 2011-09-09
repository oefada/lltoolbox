<?php $this->set('hideSidebar', true); ?>
<div class="countries index">
<h2><?php __('Countries');?></h2>
<?php if (isset($query)): ?>
<h2>You searched for: <?= $query ?></h2>
<?php endif; ?> 
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('countryId');?></th>
	<th><?php echo $paginator->sort('countryName');?></th>
	<th><?php echo $paginator->sort('mapRef');?></th>
	<th><?php echo $paginator->sort('currencyName');?></th>
	<th><?php echo $paginator->sort('currencyCode');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($countries as $country):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $country['Country']['countryId']; ?>
		</td>
		<td>
			<?php echo $country['Country']['countryName']; ?>
		</td>
		<td>
			<?php echo $country['Country']['mapRef']; ?>
		</td>
		<td>
			<?php echo $country['Country']['currencyName']; ?>
		</td>
		<td>
			<?php echo $country['Country']['currencyCode']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $country['Country']['countryId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>
