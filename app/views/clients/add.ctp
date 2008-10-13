<?php
$this->pageTitle = 'Add Client';
$html->addCrumb('Clients', '/clients');
$html->addCrumb('Add');
?>

<div class="clients form">
<?php echo $form->create('Client');?>
	<fieldset>
 		<legend><?php __('Add Client');?></legend>
	<?php
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
