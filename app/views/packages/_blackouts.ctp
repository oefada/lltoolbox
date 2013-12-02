<fieldset class="collapsible">
	<h3 class="handle">Blackout Periods</h3>
	<div class="collapsibleContent">
		<div class='controlset3'>
		<?php
			/* Simple checkboxes for the dates. We start the array with index 1 to work nicely with the date format 'N', the
				ISO-8601 numeric representation of the day of the week
			*/
			echo $form->input('Recurring Day Blackout', array('multiple'=>'checkbox', 'options' => array(1=>'M','T','W','Th','F','S', 'Su'))); 
		?>
		</div>
		<div id="blackoutPeriods">
		<?php echo $this->renderElement('../packages/_step_3_blackout_periods'); ?>
		</div>
	</div>
</fieldset>