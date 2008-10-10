<div class="addresses form">
<?php echo $form->create('Address');?>
	<fieldset>
 		<legend><?php __('Add Address');?></legend>
	<?php
		echo $form->input('clientId');
		echo $form->input('userId');
		echo $form->input('addressTypeId');
		echo $form->input('cityId');
		echo $form->input('stateId');
		echo $form->input('countryId');
		echo $form->input('address1');
		echo $form->input('address2');
		echo $form->input('address3');
		echo $form->input('city');
		echo $form->input('stateName');
		echo $form->input('countryName');
		echo $form->input('postalCode');
		echo $form->input('defaultAddress');
		echo $form->input('latitude');
		echo $form->input('longitude');
		echo $form->input('countrytext');
		echo $form->input('stateCode');
		echo $form->input('countryCode');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Addresses', true), array('action'=>'index'));?></li>
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
