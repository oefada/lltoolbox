<div class="amenitites form">
<?php echo $form->create('Amenity');?>
	<fieldset>
 		<legend><?php __('Edit Amenity');?></legend>
	<?php
		echo $form->input('amenityId');
		echo $form->input('amenityName');
		echo "<div class='controlset'>";
		echo $form->input('inactive');
		echo "</div>";
	?>
	</fieldset>
<?php echo $form->end('Submit');?>

<?php echo $html->link('Delete', array('action' => 'delete', 'id' => $this->data['Amenity']['amenityId']))?>
</div>
