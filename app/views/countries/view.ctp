<div class="countries view">
<h2><?php  __('Country');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CountryId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $country['Country']['countryId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CountryCode'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $country['Country']['countryCode']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CountryName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $country['Country']['countryName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MapRef'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $country['Country']['mapRef']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CurrencyName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $country['Country']['currencyName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CurrencyCode'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $country['Country']['currencyCode']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Country', true), array('action'=>'edit', $country['Country']['countryId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Country', true), array('action'=>'delete', $country['Country']['countryId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $country['Country']['countryId'])); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related States');?></h3>
	<?php if (!empty($country['State'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('StateId'); ?></th>
		<th><?php __('CountryId'); ?></th>
		<th><?php __('StateCode'); ?></th>
		<th><?php __('StateName'); ?></th>
		<th><?php __('ADM1Code'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($country['State'] as $state):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $state['stateId'];?></td>
			<td><?php echo $state['countryId'];?></td>
			<td><?php echo $state['stateCode'];?></td>
			<td><?php echo $state['stateName'];?></td>
			<td><?php echo $state['ADM1Code'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'states', 'action'=>'view', $state['stateId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'states', 'action'=>'edit', $state['stateId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'states', 'action'=>'delete', $state['stateId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $state['stateId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Cities');?></h3>
	<?php if (!empty($country['City'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('CityId'); ?></th>
		<th><?php __('CityName'); ?></th>
		<th><?php __('StateId'); ?></th>
		<th><?php __('CountryId'); ?></th>
		<th><?php __('Latitude'); ?></th>
		<th><?php __('Longitude'); ?></th>
		<th><?php __('CityCode'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($country['City'] as $city):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $city['cityId'];?></td>
			<td><?php echo $city['cityName'];?></td>
			<td><?php echo $city['stateId'];?></td>
			<td><?php echo $city['countryId'];?></td>
			<td><?php echo $city['latitude'];?></td>
			<td><?php echo $city['longitude'];?></td>
			<td><?php echo $city['cityCode'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'cities', 'action'=>'view', $city['cityId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'cities', 'action'=>'edit', $city['cityId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'cities', 'action'=>'delete', $city['cityId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $city['cityId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Tags');?></h3>
	<?php if (!empty($country['Tag'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('TagId'); ?></th>
		<th><?php __('TagName'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($country['Tag'] as $tag):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $tag['tagId'];?></td>
			<td><?php echo $tag['tagName'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'tags', 'action'=>'view', $tag['tagId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'tags', 'action'=>'edit', $tag['tagId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'tags', 'action'=>'delete', $tag['tagId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $tag['tagId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
