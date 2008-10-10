<div class="tags view">
<h2><?php  __('Tag');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TagId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $tag['Tag']['tagId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TagName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $tag['Tag']['tagName']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Tag', true), array('action'=>'edit', $tag['Tag']['tagId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Tag', true), array('action'=>'delete', $tag['Tag']['tagId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $tag['Tag']['tagId'])); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Destinations', true), array('controller'=> 'destinations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Destination', true), array('controller'=> 'destinations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Coordinates', true), array('controller'=> 'coordinates', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Coordinate', true), array('controller'=> 'coordinates', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Destinations');?></h3>
	<?php if (!empty($tag['Destination'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('DestinationId'); ?></th>
		<th><?php __('DestinationName'); ?></th>
		<th><?php __('ParentId'); ?></th>
		<th><?php __('TagId'); ?></th>
		<th><?php __('LeftValue'); ?></th>
		<th><?php __('RightValue'); ?></th>
		<th><?php __('IncludeInNav'); ?></th>
		<th><?php __('Display'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($tag['Destination'] as $destination):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $destination['destinationId'];?></td>
			<td><?php echo $destination['destinationName'];?></td>
			<td><?php echo $destination['parentId'];?></td>
			<td><?php echo $destination['tagId'];?></td>
			<td><?php echo $destination['leftValue'];?></td>
			<td><?php echo $destination['rightValue'];?></td>
			<td><?php echo $destination['includeInNav'];?></td>
			<td><?php echo $destination['display'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'destinations', 'action'=>'view', $destination['destinationId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'destinations', 'action'=>'edit', $destination['destinationId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'destinations', 'action'=>'delete', $destination['destinationId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $destination['destinationId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Destination', true), array('controller'=> 'destinations', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Countries');?></h3>
	<?php if (!empty($tag['Country'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('CountryId'); ?></th>
		<th><?php __('CountryCode'); ?></th>
		<th><?php __('CountryName'); ?></th>
		<th><?php __('MapRef'); ?></th>
		<th><?php __('CurrencyName'); ?></th>
		<th><?php __('CurrencyCode'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($tag['Country'] as $country):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $country['countryId'];?></td>
			<td><?php echo $country['countryCode'];?></td>
			<td><?php echo $country['countryName'];?></td>
			<td><?php echo $country['mapRef'];?></td>
			<td><?php echo $country['currencyName'];?></td>
			<td><?php echo $country['currencyCode'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'countries', 'action'=>'view', $country['countryId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'countries', 'action'=>'edit', $country['countryId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'countries', 'action'=>'delete', $country['countryId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $country['countryId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related States');?></h3>
	<?php if (!empty($tag['State'])):?>
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
		foreach ($tag['State'] as $state):
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
	<?php if (!empty($tag['City'])):?>
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
		foreach ($tag['City'] as $city):
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
	<h3><?php __('Related Coordinates');?></h3>
	<?php if (!empty($tag['Coordinate'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('CoordinateId'); ?></th>
		<th><?php __('TopLeftLat'); ?></th>
		<th><?php __('TopLeftLong'); ?></th>
		<th><?php __('BottomRightLat'); ?></th>
		<th><?php __('BottomRightLong'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($tag['Coordinate'] as $coordinate):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $coordinate['coordinateId'];?></td>
			<td><?php echo $coordinate['topLeftLat'];?></td>
			<td><?php echo $coordinate['topLeftLong'];?></td>
			<td><?php echo $coordinate['bottomRightLat'];?></td>
			<td><?php echo $coordinate['bottomRightLong'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'coordinates', 'action'=>'view', $coordinate['coordinateId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'coordinates', 'action'=>'edit', $coordinate['coordinateId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'coordinates', 'action'=>'delete', $coordinate['coordinateId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $coordinate['coordinateId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Coordinate', true), array('controller'=> 'coordinates', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Clients');?></h3>
	<?php if (!empty($tag['Client'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('ClientId'); ?></th>
		<th><?php __('ParentClientId'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Url'); ?></th>
		<th><?php __('Email1'); ?></th>
		<th><?php __('Email2'); ?></th>
		<th><?php __('Phone1'); ?></th>
		<th><?php __('Phone2'); ?></th>
		<th><?php __('ClientTypeId'); ?></th>
		<th><?php __('ClientLevelId'); ?></th>
		<th><?php __('RegionId'); ?></th>
		<th><?php __('ClientStatusId'); ?></th>
		<th><?php __('ClientAcquisitionSourceId'); ?></th>
		<th><?php __('CustomMapLat'); ?></th>
		<th><?php __('CustomMapLong'); ?></th>
		<th><?php __('CustomMapZoomMap'); ?></th>
		<th><?php __('CustomMapZoomSat'); ?></th>
		<th><?php __('CompanyName'); ?></th>
		<th><?php __('Country'); ?></th>
		<th><?php __('CheckRateUrl'); ?></th>
		<th><?php __('NumRooms'); ?></th>
		<th><?php __('AirportCode'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($tag['Client'] as $client):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $client['clientId'];?></td>
			<td><?php echo $client['parentClientId'];?></td>
			<td><?php echo $client['name'];?></td>
			<td><?php echo $client['url'];?></td>
			<td><?php echo $client['email1'];?></td>
			<td><?php echo $client['email2'];?></td>
			<td><?php echo $client['phone1'];?></td>
			<td><?php echo $client['phone2'];?></td>
			<td><?php echo $client['clientTypeId'];?></td>
			<td><?php echo $client['clientLevelId'];?></td>
			<td><?php echo $client['regionId'];?></td>
			<td><?php echo $client['clientStatusId'];?></td>
			<td><?php echo $client['clientAcquisitionSourceId'];?></td>
			<td><?php echo $client['customMapLat'];?></td>
			<td><?php echo $client['customMapLong'];?></td>
			<td><?php echo $client['customMapZoomMap'];?></td>
			<td><?php echo $client['customMapZoomSat'];?></td>
			<td><?php echo $client['companyName'];?></td>
			<td><?php echo $client['country'];?></td>
			<td><?php echo $client['checkRateUrl'];?></td>
			<td><?php echo $client['numRooms'];?></td>
			<td><?php echo $client['airportCode'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'clients', 'action'=>'view', $client['clientId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'clients', 'action'=>'edit', $client['clientId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'clients', 'action'=>'delete', $client['clientId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $client['clientId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
