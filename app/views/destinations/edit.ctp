<div class="destinations form">
<?php echo $form->create('Destination');?>
	<fieldset>
 		<legend><?php __('Edit Destination');?></legend>
		<div class="controlset4">
		<?
		echo $form->input('sites', array('multiple' => 'checkbox'));
		?>
		</div>
	<?php
		echo $form->input('destinationId');
		echo $form->input('parentId');
		echo $form->input('destinationName');
		echo $form->input('includeInNav');
		echo $form->input('display');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
