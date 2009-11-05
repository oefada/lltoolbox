<div class="accolades form">
<?php echo $form->create('Accolade');?>
	<fieldset>
 		<legend><?php __('Add Accolade');?></legend>
		<div class="controlset4">
		<?
		echo $form->input('sites', array('multiple' => 'checkbox'));
		?>
		</div>
	<?php
		echo $form->input('accoladeSourceId', array('label' => 'Accolade Source'));
		echo $form->input('clientId', array('label' => 'Client ID'));
		echo $form->input('accoladeName');
		echo $form->input('description');
		echo $form->input('accoladeDate');
		echo $form->input('displayDate');
	?>
	<div class="controlset">
	<?php
		echo $form->input('inactive');
	?>
	</div>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
