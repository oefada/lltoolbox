<fieldset class="collapsible">
	<h3 class="handle">Setup</h3>
	<div class="collapsibleContent disableAutoCollapse">
	<?php
		echo $form->input('packageTitle');
		echo $form->input('shortBlurb');
		echo $form->input('additionalDescription', array('label' => 'Short Description (Displays on Listing Page)'));
		echo $form->input('packageIncludes', array('label' => 'Full Description'));
		
		?><div style="float: left; clear: none"><?php
			echo $form->input('validityStartDate', array('label' => 'Validity Start'));
		?></div><div style="float: left; clear: none"><?php
			echo $form->input('validityEndDate', array('label' => 'Validity End'));
		?>
		</div>

		<div style="float: left; clear: both"><?
			echo $form->input('startDate', array('label' => 'Original Start Date'));
		?></div><div style="float: left; clear: none"><?
			echo $form->input('endDate', array('label' => 'Original End Date'));
		?></div><?
		
		echo $form->input('PackageOfferTypeDefField.7.offerTypeId', array('value' => 7, 'type' => 'hidden'));
		echo $form->input('PackageOfferTypeDefField.7.buyNowPrice', array('value' => 1, 'type' => 'hidden'));
		echo $form->input('PackageOfferTypeDefField.7.percentRetail', array('value' => 100, 'type' => 'hidden'));
		
		echo $form->input('externalOfferUrl', array('label' => 'Offer URL'));
		
		echo $form->input('Format', array('value' => 3, 'type' => 'hidden'));
	?>
	</div>
</fieldset>