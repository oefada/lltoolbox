<?php
$this->pageTitle = 'Edit Client';
$html->addCrumb('Clients', '/clients');
$html->addCrumb($text->truncate($this->data['Client']['name'], 15), '/clients/view/'.$this->data['Client']['clientId']);
$html->addCrumb('Edit');
?>
<?=$layout->blockStart('header');?>
<?= $html->link('<span><b class="icon"></b>Delete Client</span>', array('action'=>'delete', $form->value('Client.clientId')), array('class' => 'button del'), sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Client.clientId')), false); ?>
<?=$layout->blockEnd();?>
<div class="clients form">
<?php echo $form->create('Client');?>
	<fieldset>
 		<legend>Client Details</legend>
		<div class="inlineForms"><? echo $form->input('clientTypeId', array('label' => 'Client Type')); ?><? echo $form->input('clientLevelId', array('label' => 'Client Level')); ?><? echo $form->input('clientStatusId', array('label' => 'Client Status')); ?></div>
	<?php
		echo $form->input('clientId');
		echo $form->input('parentClientId');
		echo $form->input('name');
	?>
	<?php
		echo $form->input('companyName');
		echo $form->input('url');
		echo $form->input('clientAcquisitionSourceId');
		echo $form->input('checkRateUrl');
		echo $form->input('numRooms');
	?>
	</fieldset>
	<fieldset>
		<legend>Contact Details</legend>
		<?php
		echo $form->input('email1');
		echo $form->input('phone1');
		echo $form->input('phone2');
		echo $form->input('country');
		echo $form->input('regionId');
		echo $form->input('airportCode');
		?>
	</fieldset>
	<fieldset>
		<legend>Geographic Details</legend>
		<?php
		echo $form->input('customMapLat');
		echo $form->input('customMapLong');
		echo $form->input('customMapZoomMap');
		echo $form->input('customMapZoomSat');
		?>
	</fieldset>
	<fieldset>
		<legend>Amenities</legend>
		<?php
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
