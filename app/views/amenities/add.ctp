<div class="amenitites form">
<?php echo $form->create('Amenity');?>
	<fieldset>
 		<legend><?php __('Edit Amenity');?></legend>
	<?php
		echo $form->input('amenityName');
		echo "<div class='controlset'>";
		echo $form->input('inactive');
		echo "</div>";
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
