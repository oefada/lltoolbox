<div class="addresses index">
<h2><?php __('Addresses');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('addressId');?></th>
	<th><?php echo $paginator->sort('clientId');?></th>
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('addressTypeId');?></th>
	<th><?php echo $paginator->sort('cityId');?></th>
	<th><?php echo $paginator->sort('stateId');?></th>
	<th><?php echo $paginator->sort('countryId');?></th>
	<th><?php echo $paginator->sort('address1');?></th>
	<th><?php echo $paginator->sort('address2');?></th>
	<th><?php echo $paginator->sort('address3');?></th>
	<th><?php echo $paginator->sort('city');?></th>
	<th><?php echo $paginator->sort('stateName');?></th>
	<th><?php echo $paginator->sort('countryName');?></th>
	<th><?php echo $paginator->sort('postalCode');?></th>
	<th><?php echo $paginator->sort('defaultAddress');?></th>
	<th><?php echo $paginator->sort('latitude');?></th>
	<th><?php echo $paginator->sort('longitude');?></th>
	<th><?php echo $paginator->sort('countrytext');?></th>
	<th><?php echo $paginator->sort('stateCode');?></th>
	<th><?php echo $paginator->sort('countryCode');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($addresses as $address):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $address['Address']['addressId']; ?>
		</td>
		<td>
			<?php echo $html->link($address['Client']['name'], array('controller'=> 'clients', 'action'=>'view', $address['Client']['clientId'])); ?>
		</td>
		<td>
			<?php echo $html->link($address['User']['title'], array('controller'=> 'users', 'action'=>'view', $address['User']['userId'])); ?>
		</td>
		<td>
			<?php echo $html->link($address['AddressType']['addressTypeName'], array('controller'=> 'address_types', 'action'=>'view', $address['AddressType']['addressTypeId'])); ?>
		</td>
		<td>
			<?php echo $html->link($address['City']['cityName'], array('controller'=> 'cities', 'action'=>'view', $address['City']['cityId'])); ?>
		</td>
		<td>
			<?php echo $html->link($address['State']['stateName'], array('controller'=> 'states', 'action'=>'view', $address['State']['stateId'])); ?>
		</td>
		<td>
			<?php echo $html->link($address['Country']['countryName'], array('controller'=> 'countries', 'action'=>'view', $address['Country']['countryId'])); ?>
		</td>
		<td>
			<?php echo $address['Address']['address1']; ?>
		</td>
		<td>
			<?php echo $address['Address']['address2']; ?>
		</td>
		<td>
			<?php echo $address['Address']['address3']; ?>
		</td>
		<td>
			<?php echo $address['Address']['city']; ?>
		</td>
		<td>
			<?php echo $address['Address']['stateName']; ?>
		</td>
		<td>
			<?php echo $address['Address']['countryName']; ?>
		</td>
		<td>
			<?php echo $address['Address']['postalCode']; ?>
		</td>
		<td>
			<?php echo $address['Address']['defaultAddress']; ?>
		</td>
		<td>
			<?php echo $address['Address']['latitude']; ?>
		</td>
		<td>
			<?php echo $address['Address']['longitude']; ?>
		</td>
		<td>
			<?php echo $address['Address']['countrytext']; ?>
		</td>
		<td>
			<?php echo $address['Address']['stateCode']; ?>
		</td>
		<td>
			<?php echo $address['Address']['countryCode']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $address['Address']['addressId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $address['Address']['addressId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $address['Address']['addressId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $address['Address']['addressId'])); ?>
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
		<li><?php echo $html->link(__('New Address', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Address Types', true), array('controller'=> 'address_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Address Type', true), array('controller'=> 'address_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Users', true), array('controller'=> 'users', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller'=> 'users', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>
