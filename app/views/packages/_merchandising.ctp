<fieldset class="collapsible">
	<h3 class="handle">Merchandising</h3>
	<div class="collapsibleContent disableAutoCollapse">
<?php
	echo $form->input('packageTitle');
	echo $form->input('shortBlurb');
	echo $form->input('termsAndConditions', array('label' => 'Terms & Conditions'));
	echo $form->input('additionalDescription', array('label' => 'Additional Description'));
	echo $form->input('packageIncludes', array('label' => 'Inclusions'));
	echo "<div class='controlset'>";
	echo $form->input('repopulateInclusions', array('label' => 'Reset and Repopulate Inclusions on Save', 'type' => 'checkbox'));
	echo "</div>";
	echo $form->input('leadInLine', array('label' => 'Lead-in Line'));
	echo $form->input('validityLeadInLine', array('label' => 'Validity Lead-in', 'value' => (empty($this->data['Package']['validityLeadInLine']) ? 'This package is valid for travel:' : $this->data['Package']['validityLeadInLine'])));
	echo $form->input('validityDisclaimer', array('value' => (empty($this->data['Package']['validityDisclaimer']) ? 'Subject to availability at time of booking' : $this->data['Package']['validityDisclaimer'])));
?>
	</div>
</fieldset>