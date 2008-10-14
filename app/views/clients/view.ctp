<?php
$this->pageTitle = 'Clients';
$html->addCrumb('Clients', '/clients');
$html->addCrumb($client['Client']['name']);
?>
<?=$layout->blockStart('header');?>
	<a href="/clients/edit/<?=$client['Client']['clientId']?>" title="Edit Client" class="button edit"><span><b class="icon"></b>Edit Client</span></a>
<?=$layout->blockEnd();?>
<?=$layout->blockStart('sidebar');?>
<?= $html->link('View Loas','/clients/'.$client['Client']['clientId'].'/loas'); ?>
<?=$layout->blockEnd();?>
<div class="clients view">
<h2><?php  echo $client['Client']['name']; ?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ClientId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['clientId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ParentClientId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['parentClientId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Url'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['url']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Email1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['email1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Phone1'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['phone1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Phone2'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['phone2']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Client Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($client['ClientType']['clientTypeName'], array('controller'=> 'client_types', 'action'=>'view', $client['ClientType']['clientTypeId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Client Level'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($client['ClientLevel']['clientLevelName'], array('controller'=> 'client_levels', 'action'=>'view', $client['ClientLevel']['clientLevelId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Region'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($client['Region']['regionName'], array('controller'=> 'regions', 'action'=>'view', $client['Region']['regionId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Client Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($client['ClientStatus']['clientStatusName'], array('controller'=> 'client_statuses', 'action'=>'view', $client['ClientStatus']['clientStatusId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Client Acquisition Source'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($client['ClientAcquisitionSource']['clientAcquisitionSourceName'], array('controller'=> 'client_acquisition_sources', 'action'=>'view', $client['ClientAcquisitionSource']['clientAcquisitionSourceId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomMapLat'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['customMapLat']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomMapLong'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['customMapLong']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomMapZoomMap'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['customMapZoomMap']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomMapZoomSat'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['customMapZoomSat']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CompanyName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['companyName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Country'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['country']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CheckRateUrl'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['checkRateUrl']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumRooms'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['numRooms']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AirportCode'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $client['Client']['airportCode']; ?>
			&nbsp;
		</dd>
	</dl>
</div>

<div class="related">
	<h3><?php __('Related LOAs');?></h3>
	<?php if (!empty($client['Loa'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('LoaId'); ?></th>
		<th><?php __('Approval Status'); ?></th>
		<th><?php __('Loa Value'); ?></th>
		<th><?php __('Remaining Balance'); ?></th>
		<th><?php __('Remit Status'); ?></th>
		<th><?php __('Upgraded'); ?></th>
		<th><?php __('Number Packages'); ?></th>
		<th><?php __('Remaining Packages'); ?></th>
		<th><?php __('CashPaid'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Loa'] as $loa):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $loa['loaId'];?></td>
			<td><?php echo $loa['customerApprovalStatusId'];?></td>
			<td><?php echo $loa['loaValue'];?></td>
			<td><?php echo $loa['remainingBalance'];?></td>
			<td><?php echo $loa['remitStatus'];?></td>
			<td><?php echo $loa['upgraded'];?></td>
			<td><?php echo $loa['loaNumberPackages'];?></td>
			<td><?php echo $loa['remainingPackagesToSell'];?></td>
			<td><?php echo $loa['cashPaid'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View Details', true), array('controller'=> 'loas', 'action'=>'view', $loa['loaId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Loa', true), array('controller'=> 'loas', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Addresses');?></h3>
	<?php if (!empty($client['Address'])):?>
	<?php
		$i = 0;
		foreach ($client['Address'] as $address):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<div<?php echo $class;?>>
			User: <?php echo $address['userId'];?><br />
			Type: <?php echo $address['addressTypeId'];?><br />
			City: <?php echo $address['cityId'];?><br />
			State: <?php echo $address['stateId'];?><br />
			Country: <?php echo $address['countryId'];?><br />
			Address 1:<?php echo $address['address1'];?><br />
			Address 2:<?php echo $address['address2'];?><br />
			<?php echo $address['address3'];?><br />
			<?php echo $address['city'];?><br />
			<?php echo $address['stateName'];?><br />
			<?php echo $address['countryName'];?><br />
			<?php echo $address['postalCode'];?><br />
			<?php echo $address['defaultAddress'];?><br />
			<?php echo $address['latitude'];?><br />
			<?php echo $address['longitude'];?><br />
			<?php echo $address['countrytext'];?><br />
			<?php echo $address['stateCode'];?><br />
			<?php echo $address['countryCode'];?><br />
				<?php echo $html->link(__('View', true), array('controller'=> 'addresses', 'action'=>'view', $address['addressId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'addresses', 'action'=>'edit', $address['addressId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'addresses', 'action'=>'delete', $address['addressId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $address['addressId'])); ?>
		</div>
		<?php endforeach; ?>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Address', true), array('controller'=> 'addresses', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Client Theme Rels');?></h3>
	<?php if (!empty($client['ClientThemeRel'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('ClientThemeRelId'); ?></th>
		<th><?php __('ClientId'); ?></th>
		<th><?php __('ThemeId'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['ClientThemeRel'] as $clientThemeRel):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $clientThemeRel['clientThemeRelId'];?></td>
			<td><?php echo $clientThemeRel['clientId'];?></td>
			<td><?php echo $clientThemeRel['themeId'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'client_theme_rels', 'action'=>'view', $clientThemeRel['clientThemeRelId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'client_theme_rels', 'action'=>'edit', $clientThemeRel['clientThemeRelId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'client_theme_rels', 'action'=>'delete', $clientThemeRel['clientThemeRelId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $clientThemeRel['clientThemeRelId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Client Theme Rel', true), array('controller'=> 'client_theme_rels', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Accolades');?></h3>
	<?php if (!empty($client['Accolade'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('AccoladeId'); ?></th>
		<th><?php __('ClientId'); ?></th>
		<th><?php __('AccoladeSourceId'); ?></th>
		<th><?php __('Description'); ?></th>
		<th><?php __('AccoladeDate'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Accolade'] as $accolade):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $accolade['accoladeId'];?></td>
			<td><?php echo $accolade['clientId'];?></td>
			<td><?php echo $accolade['accoladeSourceId'];?></td>
			<td><?php echo $accolade['description'];?></td>
			<td><?php echo $accolade['accoladeDate'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'accolades', 'action'=>'view', $accolade['accoladeId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'accolades', 'action'=>'edit', $accolade['accoladeId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'accolades', 'action'=>'delete', $accolade['accoladeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $accolade['accoladeId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Accolade', true), array('controller'=> 'accolades', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Users');?></h3>
	<?php if (!empty($client['User'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('UserId'); ?></th>
		<th><?php __('Title'); ?></th>
		<th><?php __('SalutationId'); ?></th>
		<th><?php __('FirstName'); ?></th>
		<th><?php __('LastName'); ?></th>
		<th><?php __('Email1'); ?></th>
		<th><?php __('Email2'); ?></th>
		<th><?php __('WorkPhone'); ?></th>
		<th><?php __('MobilePhone'); ?></th>
		<th><?php __('HomePhone'); ?></th>
		<th><?php __('OtherPhone'); ?></th>
		<th><?php __('Fax'); ?></th>
		<th><?php __('UserAcquisitionSourceId'); ?></th>
		<th><?php __('DoNotContact'); ?></th>
		<th><?php __('Notes'); ?></th>
		<th><?php __('Inactive'); ?></th>
		<th><?php __('ClientNotificationEmailsActive'); ?></th>
		<th><?php __('RegistrationDate'); ?></th>
		<th><?php __('InitialSignUpDate'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['User'] as $user):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $user['userId'];?></td>
			<td><?php echo $user['title'];?></td>
			<td><?php echo $user['salutationId'];?></td>
			<td><?php echo $user['firstName'];?></td>
			<td><?php echo $user['lastName'];?></td>
			<td><?php echo $user['email1'];?></td>
			<td><?php echo $user['email2'];?></td>
			<td><?php echo $user['workPhone'];?></td>
			<td><?php echo $user['mobilePhone'];?></td>
			<td><?php echo $user['homePhone'];?></td>
			<td><?php echo $user['otherPhone'];?></td>
			<td><?php echo $user['fax'];?></td>
			<td><?php echo $user['userAcquisitionSourceId'];?></td>
			<td><?php echo $user['doNotContact'];?></td>
			<td><?php echo $user['notes'];?></td>
			<td><?php echo $user['inactive'];?></td>
			<td><?php echo $user['clientNotificationEmailsActive'];?></td>
			<td><?php echo $user['registrationDate'];?></td>
			<td><?php echo $user['initialSignUpDate'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'users', 'action'=>'view', $user['userId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'users', 'action'=>'edit', $user['userId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'users', 'action'=>'delete', $user['userId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $user['userId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New User', true), array('controller'=> 'users', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Amenities');?></h3>
	<?php if (!empty($client['Amenity'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('AmenityId'); ?></th>
		<th><?php __('AmenityName'); ?></th>
		<th><?php __('AmenityTypeId'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($client['Amenity'] as $amenity):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $amenity['amenityId'];?></td>
			<td><?php echo $amenity['amenityName'];?></td>
			<td><?php echo $amenity['amenityTypeId'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'amenities', 'action'=>'view', $amenity['amenityId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'amenities', 'action'=>'edit', $amenity['amenityId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'amenities', 'action'=>'delete', $amenity['amenityId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $amenity['amenityId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Amenity', true), array('controller'=> 'amenities', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
