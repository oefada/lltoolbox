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
		echo $form->input('email');
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
		
		<div style="width: 400px; float: left">
		<?php
		echo $ajax->autoComplete('Amenity', '/amenities/auto_complete');
		?></div><div style="clear: none; float: left;"><?php
		echo $ajax->link('Add new LOA item',
						'/amenities/view_complete_list_compact',
						array(
							'title' => 'All Amenities',
							'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
							'complete' => 'closeModalbox()'
							),
						null,
						false
						);
		?></div>
		
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
