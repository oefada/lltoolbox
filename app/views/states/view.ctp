<div class="states view">
<h2><?php  __('State');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('StateId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $state['State']['stateId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Country'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($state['Country']['countryText'], array('controller'=> 'countries', 'action'=>'view', $state['Country']['countryId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('StateCode'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $state['State']['stateCode']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('StateName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $state['State']['stateName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ADM1Code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $state['State']['ADM1Code']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit State', true), array('action'=>'edit', $state['State']['stateId'])); ?> </li>
		<li><?php echo $html->link(__('Delete State', true), array('action'=>'delete', $state['State']['stateId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $state['State']['stateId'])); ?> </li>
		<li><?php echo $html->link(__('List States', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Cities');?></h3>
	<?php if (!empty($state['City'])):?>
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
		foreach ($state['City'] as $city):
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
	<?php if (!empty($state['Tag'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('TagId'); ?></th>
		<th><?php __('TagName'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($state['Tag'] as $tag):
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
