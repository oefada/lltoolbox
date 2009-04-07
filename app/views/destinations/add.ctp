<div class="destinations form">
<?php echo $form->create('Destination');?>
	<fieldset>
 		<legend><?php __('Add Destination');?></legend>
	<?php
		echo $form->input('parentId');
		echo $form->input('destinationName');
		echo $form->input('includeInNav');
		echo $form->input('display');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
