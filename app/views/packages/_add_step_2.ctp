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
?>
<div style="float: left; clear: none">
<?php
	echo $form->input('approvedRetailPrice', array('label' => 'Retail Value<br />'.$this->data['Currency']['currencyCode']));
?>
</div>
<div style="float: left; clear: none">
<?php
	echo "<label for='PackageApprovedRetailPriceExchangeRate'>Exchange Rate</label>";
	echo $form->select('approvedRetailPriceExchangeRate', array('1.2' => 'Today (1.2)', '1.5' => '7 Day Average (1.5)', '0.8' => '28 day average (0.8)'), null, array('label' => 'Exchange Rate'), false);
	echo $form->input('approvedRetailPriceInUSD', array('disabled' => 'disabled', 'label' => 'USD', 'value' => '$'.$this->data['Package']['approvedRetailPrice']*1.2));
?></div>
<script language="javascript">
  // Example using periodic Form Observer
  var obs=new Form.Element.EventObserver($("PackageApprovedRetailPrice"),displayAlert);
  var obs2=new Form.Element.EventObserver($("PackageApprovedRetailPriceExchangeRate"),displayAlert);
  function displayAlert(){
     $('PackageApprovedRetailPriceInUSD').value = '$'+($F('PackageApprovedRetailPrice')*$F('PackageApprovedRetailPriceExchangeRate'));
  }
</script>
</div>
</fieldset>
