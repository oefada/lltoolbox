<div class="clients form">
<?php echo $form->create('Client');?>
	<fieldset>
 		<legend><?php __('Edit Client');?></legend>
	<?php
		echo $form->input('clientId');
		echo $form->input('parentClientId');
		echo $form->input('name');
		echo $form->input('url');
		echo $form->input('email1');
		echo $form->input('email2');
		echo $form->input('phone1');
		echo $form->input('phone2');
		echo $form->input('clientTypeId');
		echo $form->input('clientLevelId');
		echo $form->input('regionId');
		echo $form->input('clientStatusId');
		echo $form->input('clientAcquisitionSourceId');
		echo $form->input('customMapLat');
		echo $form->input('customMapLong');
		echo $form->input('customMapZoomMap');
		echo $form->input('customMapZoomSat');
		echo $form->input('companyName');
		echo $form->input('country');
		echo $form->input('checkRateUrl');
		echo $form->input('numRooms');
		echo $form->input('airportCode');
		echo $form->input('Tag');
		echo $form->input('User');
		echo $form->input('Amenity');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Client.clientId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Client.clientId'))); ?></li>
		<li><?php echo $html->link(__('List Clients', true), array('action'=>'index'));?></li>
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
