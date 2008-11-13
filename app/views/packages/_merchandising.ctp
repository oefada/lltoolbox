<fieldset class="collapsible">
	<h3 class="handle">Merchandising</h3>
	<div class="collapsibleContent disableAutoCollapse">
<?php
	echo $form->input('packageTitle');
	echo $form->input('subtitle');
	echo $form->input('shortBlurb');
	echo $form->input('restrictions', array('label' => 'Terms & Conditions'));
	echo $form->input('additionalDescription', array('label' => 'Additional Description'));
	echo $form->input('leadInLine', array('label' => 'Lead-in Line'));
	echo $form->input('validityLeadInLine', array('label' => 'Validity Lead-in'));
?>
	</div>
</fieldset>