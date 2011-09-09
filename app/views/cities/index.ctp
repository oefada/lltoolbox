<div class="cities index">
<h2><?php __('Cities');?></h2>
<p>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New City', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
	</ul>
</div>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('cityId');?></th>
	<th><?php echo $paginator->sort('City Name','cityName');?></th>
	<th><?php echo $paginator->sort('City Alias','cityAlias');?></th>
	<th><?php echo $paginator->sort('State Name','State.stateName');?></th>
	<th><?php echo $paginator->sort('Country Name','Country.countryName');?></th>
	<th><?php echo $paginator->sort('latitude');?></th>
	<th><?php echo $paginator->sort('longitude');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$this->set('hideSidebar', true);
$i = 0;
foreach ($cities as $city):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $city['City']['cityId']; ?>
		</td>
		<td>
			<?php echo $city['City']['cityName']; ?>
		</td>
		<td>
			<?php echo $city['City']['cityAlias']; ?>
		</td>
		<td>
			<?php echo $html->link($city['State']['stateName'], array('controller'=> 'states', 'action'=>'edit', $city['State']['stateId'])); ?>
		</td>
		<td>
			<?php echo $html->link($city['Country']['countryName'], array('controller'=> 'countries', 'action'=>'edit', $city['Country']['countryId'])); ?>
		</td>
		<td>
			<?php echo $city['City']['latitude']; ?>
		</td>
		<td>
			<?php echo $city['City']['longitude']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $city['City']['cityId'])); ?>
			<?php echo $html->link(__('Disable', true), array('action'=>'disable', $city['City']['cityId']), null, sprintf(__('Are you sure you want to disable city %s?', true), $city['City']['cityId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
