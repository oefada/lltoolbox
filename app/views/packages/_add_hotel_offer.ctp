<fieldset class="collapsible">
	<h3 class="handle">Setup</h3>
	<div class="collapsibleContent disableAutoCollapse">
	<?php
	    echo $form->input('siteId', array('label' => 'Package For'));
		echo $form->input('packageTitle');
		echo $form->input('shortBlurb');
		echo $form->input('additionalDescription', array('label' => 'Short Description<br/><br/><span style="font-weight:normal;">Displays on Listing<br/>135 Characters Limit</span>'));
		echo $form->input('packageIncludes', array('label' => 'Full Description'));
		
		?><div style="float: left; clear: both"><?
			echo $form->input('startDate', array('label' => 'Display Start Date'));
		?></div><div style="float: left; clear: none"><?
			echo $form->input('endDate', array('label' => 'Display End Date'));
		?></div><?

        ?><div style="float: left; clear: both"><?
		    echo $form->input('validityStartDate', array('label' => 'Validity Start Date'));
		?></div><div style="float: left; clear: none"><?
			echo $form->input('validityEndDate', array('label' => 'Validity End Date'));
		?></div><?


		
		echo $form->input('PackageOfferTypeDefField.7.offerTypeId', array('value' => 7, 'type' => 'hidden'));
		echo $form->input('PackageOfferTypeDefField.7.buyNowPrice', array('value' => 1, 'type' => 'hidden'));
		echo $form->input('PackageOfferTypeDefField.7.percentRetail', array('value' => 100, 'type' => 'hidden'));
		
		if (isset($package['Package']['externalOfferUrl'])) {
			echo $form->input('externalOfferUrl', array('label' => 'Offer URL'));	
		} else {
			echo $form->input('externalOfferUrl', array('label' => 'Offer URL', 'value' => 'http://'));
		}
		
		echo $form->input('Format', array('value' => 3, 'type' => 'hidden'));
		echo $form->input('packageStatusId', array('value' => 4, 'type' => 'hidden'));
	?>
	</div>

	<h3 class="handle">Link Trackings (optional)</h3>
	<div class="collapsibleContent disableAutoCollapse">
		<p style="font-size:11px; line-height:15px; margin:10px 0px; font-style:italic;"><b>Offer URL</b> above is the actual and default URL for all hotel offer links. It will also be used to display with "Available on:".<br/>Specifying the tracking links below will replace the Offer URL link for that element (e.g. logo, button, or "available on").</p>
	<?
		
		echo $form->input('ClientTracking.5.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">&quot;Go To Offer&quot; Button</span>'));
		echo $form->input('ClientTracking.5.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">&quot;Go To Offer&quot; Button</span>'));
		echo $form->input('ClientTracking.5.clientTrackingTypeId', array('value' => 5, 'type' => 'hidden'));
		echo $form->input('ClientTracking.5.clientTrackingId', array('type' => 'hidden'));
		
		?><br/><?
		
		echo $form->input('ClientTracking.6.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">&quot;Available on&quot;</span>'));
		echo $form->input('ClientTracking.6.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">&quot;Available on&quot;</span>'));
		echo $form->input('ClientTracking.6.clientTrackingTypeId', array('value' => 6, 'type' => 'hidden'));
		echo $form->input('ClientTracking.6.clientTrackingId', array('type' => 'hidden'));
	?>
	</div>

</fieldset>
