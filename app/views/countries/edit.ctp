<?php $this->set('hideSidebar', true); ?>
<div class="countries form">
<?php echo $form->create('Country');?>
	<fieldset>
 		<legend><?php __('Edit Country');?></legend>
	<?php
		echo $form->input('countryId');
		echo $form->input('countryName');
		echo $form->input('mapRef');
		echo $form->input('currencyName');
		echo $form->input('currencyCode');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Countries', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>
