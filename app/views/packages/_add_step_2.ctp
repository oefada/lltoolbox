<fieldset class="collapsible">
	<legend class="handle">Step 2 - Add Basic Package Information</legend>
	<div class="collapsibleContent disableAutoCollapse">
<?php
	
	echo $form->input('packageName');
	echo $form->input('subtitle');
	echo $form->input('startDate', array('label' => 'Package Start Date'));
	echo $form->input('endDate', array('label' => 'Package End Date'));
	echo $form->input('validityStartDate');
	echo $form->input('validityEndDate');
	echo $form->input('numGuests');
	echo $form->input('maxOffersToSell');
	
	
	echo $form->input('copiedFromPackageId', array('readonly' => 'readonly'));
	
	echo $form->input('packageStatusId', array('label' => 'Status'));
	
	echo $form->input('currencyId');
	echo $form->input('numConcurrentOffers');
	
	echo '<div class="controlset">'.$form->input('Package.suppressRetailOnDisplay').'</div>';
	
	echo $form->input('dateClientApproved');
	echo $form->input('restrictions');
	echo $form->input('approvedRetailPrice');
	echo $form->input('numNights');
?>
	</div>
</fieldset>
