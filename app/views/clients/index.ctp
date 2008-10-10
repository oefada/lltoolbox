<div class="clients index">
<h2><?php __('Clients');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('clientId');?></th>
	<th><?php echo $paginator->sort('parentClientId');?></th>
	<th><?php echo $paginator->sort('name');?></th>
	<th><?php echo $paginator->sort('url');?></th>
	<th><?php echo $paginator->sort('email1');?></th>
	<th><?php echo $paginator->sort('email2');?></th>
	<th><?php echo $paginator->sort('phone1');?></th>
	<th><?php echo $paginator->sort('phone2');?></th>
	<th><?php echo $paginator->sort('clientTypeId');?></th>
	<th><?php echo $paginator->sort('clientLevelId');?></th>
	<th><?php echo $paginator->sort('regionId');?></th>
	<th><?php echo $paginator->sort('clientStatusId');?></th>
	<th><?php echo $paginator->sort('clientAcquisitionSourceId');?></th>
	<th><?php echo $paginator->sort('customMapLat');?></th>
	<th><?php echo $paginator->sort('customMapLong');?></th>
	<th><?php echo $paginator->sort('customMapZoomMap');?></th>
	<th><?php echo $paginator->sort('customMapZoomSat');?></th>
	<th><?php echo $paginator->sort('companyName');?></th>
	<th><?php echo $paginator->sort('country');?></th>
	<th><?php echo $paginator->sort('checkRateUrl');?></th>
	<th><?php echo $paginator->sort('numRooms');?></th>
	<th><?php echo $paginator->sort('airportCode');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($clients as $client):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $client['Client']['clientId']; ?>
		</td>
		<td>
			<?php echo $client['Client']['parentClientId']; ?>
		</td>
		<td>
			<?php echo $client['Client']['name']; ?>
		</td>
		<td>
			<?php echo $client['Client']['url']; ?>
		</td>
		<td>
			<?php echo $client['Client']['email1']; ?>
		</td>
		<td>
			<?php echo $client['Client']['email2']; ?>
		</td>
		<td>
			<?php echo $client['Client']['phone1']; ?>
		</td>
		<td>
			<?php echo $client['Client']['phone2']; ?>
		</td>
		<td>
			<?php echo $html->link($client['ClientType']['clientTypeName'], array('controller'=> 'client_types', 'action'=>'view', $client['ClientType']['clientTypeId'])); ?>
		</td>
		<td>
			<?php echo $html->link($client['ClientLevel']['clientLevelName'], array('controller'=> 'client_levels', 'action'=>'view', $client['ClientLevel']['clientLevelId'])); ?>
		</td>
		<td>
			<?php echo $html->link($client['Region']['regionName'], array('controller'=> 'regions', 'action'=>'view', $client['Region']['regionId'])); ?>
		</td>
		<td>
			<?php echo $html->link($client['ClientStatus']['clientStatusName'], array('controller'=> 'client_statuses', 'action'=>'view', $client['ClientStatus']['clientStatusId'])); ?>
		</td>
		<td>
			<?php echo $html->link($client['ClientAcquisitionSource']['clientAcquisitionSourceName'], array('controller'=> 'client_acquisition_sources', 'action'=>'view', $client['ClientAcquisitionSource']['clientAcquisitionSourceId'])); ?>
		</td>
		<td>
			<?php echo $client['Client']['customMapLat']; ?>
		</td>
		<td>
			<?php echo $client['Client']['customMapLong']; ?>
		</td>
		<td>
			<?php echo $client['Client']['customMapZoomMap']; ?>
		</td>
		<td>
			<?php echo $client['Client']['customMapZoomSat']; ?>
		</td>
		<td>
			<?php echo $client['Client']['companyName']; ?>
		</td>
		<td>
			<?php echo $client['Client']['country']; ?>
		</td>
		<td>
			<?php echo $client['Client']['checkRateUrl']; ?>
		</td>
		<td>
			<?php echo $client['Client']['numRooms']; ?>
		</td>
		<td>
			<?php echo $client['Client']['airportCode']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $client['Client']['clientId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $client['Client']['clientId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $client['Client']['clientId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $client['Client']['clientId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Client', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Client Levels', true), array('controller'=> 'client_levels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Level', true), array('controller'=> 'client_levels', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Statuses', true), array('controller'=> 'client_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Status', true), array('controller'=> 'client_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Types', true), array('controller'=> 'client_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Type', true), array('controller'=> 'client_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Regions', true), array('controller'=> 'regions', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Region', true), array('controller'=> 'regions', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Acquisition Sources', true), array('controller'=> 'client_acquisition_sources', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Acquisition Source', true), array('controller'=> 'client_acquisition_sources', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Loas', true), array('controller'=> 'loas', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa', true), array('controller'=> 'loas', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Addresses', true), array('controller'=> 'addresses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Address', true), array('controller'=> 'addresses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Theme Rels', true), array('controller'=> 'client_theme_rels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Theme Rel', true), array('controller'=> 'client_theme_rels', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Accolades', true), array('controller'=> 'accolades', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Accolade', true), array('controller'=> 'accolades', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Users', true), array('controller'=> 'users', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller'=> 'users', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Amenities', true), array('controller'=> 'amenities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Amenity', true), array('controller'=> 'amenities', 'action'=>'add')); ?> </li>
	</ul>
</div>
