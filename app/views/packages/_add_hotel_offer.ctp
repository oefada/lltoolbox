<fieldset class="collapsible">
	<h3 class="handle">Setup</h3>
	<div class="collapsibleContent disableAutoCollapse">
	<?php
		echo $form->input('packageTitle');
		echo $form->input('shortBlurb');
		echo $form->input('additionalDescription', array('label' => 'Short Description<br/><br/><span style="font-weight:normal;">Displays on Listing<br/>135 Characters Limit</span>'));
		echo $form->input('packageIncludes', array('label' => 'Full Description'));
		
		?><div style="float: left; clear: both"><?
			echo $form->input('startDate', array('label' => 'Original Start Date'));
		?></div><div style="float: left; clear: none"><?
			echo $form->input('endDate', array('label' => 'Original End Date'));
		?></div><?
		
		echo $form->input('PackageOfferTypeDefField.7.offerTypeId', array('value' => 7, 'type' => 'hidden'));
		echo $form->input('PackageOfferTypeDefField.7.buyNowPrice', array('value' => 1, 'type' => 'hidden'));
		echo $form->input('PackageOfferTypeDefField.7.percentRetail', array('value' => 100, 'type' => 'hidden'));
		
		if ($package['Package']['externalOfferUrl']) {
			echo $form->input('externalOfferUrl', array('label' => 'Offer URL'));	
		} else {
			echo $form->input('externalOfferUrl', array('label' => 'Offer URL', 'value' => 'http://'));
		}
		
		
		echo $form->input('Format', array('value' => 3, 'type' => 'hidden'));
	?>
	</div>
</fieldset>