<div class="packages form">
<?php echo $form->create('Package');?>
	<fieldset>
 		<legend><?php __('Edit Package');?></legend>
	<?php
		echo $form->input('packageId');
		echo $form->input('packageStatusId');
		echo $form->input('currencyId');
		echo $form->input('packageName');
		echo $form->input('subtitle');
		echo $form->input('currencyAsOfDate');
		echo $form->input('numSold');
		echo $form->input('numConcurrentOffers');
		echo $form->input('suppressRetailOnDisplay');
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('maxOffersToSell');
		echo $form->input('dateClientApproved');
		echo $form->input('copiedFromPackageId');
		echo $form->input('restrictions');
		echo $form->input('validityStartDate');
		echo $form->input('validityEndDate');
		echo $form->input('approvedRetailPrice');
		echo $form->input('numNights');
		echo $form->input('numGuests');
		echo $form->input('Format', array('label' => 'Allowed Formats', 'multiple' => 'checkbox'));
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Package.packageId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Package.packageId'))); ?></li>
		<li><?php echo $html->link(__('List Packages', true), array('action'=>'index'));?></li>
	</ul>
</div>
