<fieldset class="collapsible">
	<legend class="handle">Step 2 - Add Basic Package Information</legend>
	<div class="collapsibleContent disableAutoCollapse">
<?php
	
	echo $form->input('packageStatusId', array('label' => 'Status'));
	echo $form->input('packageName');
	echo $form->input('subtitle');
	echo $form->input('shortBlurb');
?>

<div style="float: left; clear: none">
<?php
	echo $form->input('startDate', array('label' => 'Package Start Date'));
?>
</div>
<div style="float: left; clear: none">
<?php
	echo $form->input('endDate', array('label' => 'Package End Date'));
?>
</div>
<div style="float: left; clear: left">
<?php
	echo $form->input('validityStartDate');
?>
</div>
<div style="float: left; clear: none">
<?php
	echo $form->input('validityEndDate');
?>
</div>
<?php
	echo $form->input('numGuests');
	echo $form->input('numNights');
	echo $form->input('maxNumSales');
	
	echo $form->input('numConcurrentOffers');
	
	echo '<div class="controlset">'.$form->input('Package.suppressRetailOnDisplay').'</div>';
	
	echo $form->input('dateClientApproved');
	echo $form->input('restrictions');
	echo $form->input('currencyId', array('disabled' => 'true'));
	echo $form->input('currencyId', array('type' => 'hidden'));
	echo $form->input('approvedRetailPrice');
?>
	</div>
</fieldset>
