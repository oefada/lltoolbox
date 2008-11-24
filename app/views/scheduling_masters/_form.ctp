	<fieldset>
		<div id='one'>
			<p class='clean-gray'><?php echo $package['Package']['shortBlurb'] ?></p>
	<?php
		echo $form->input('offerTypeId', array('label' => 'Offer Type'));		
		echo $form->input('retailValue', array('disabled' => 'disabled'));
		
		/* TODO: Marketing/Judy needs to be able to override the defaults*/
		echo '<div id="defaults" style="margin: 0; padding: 0">';
		if (isset($defaultFile)) {
			echo $this->renderElement('../scheduling_masters/'.$defaultFile);
		}
		echo '</div>';
		echo $form->input('reservePrice', array('value' => $package['Package']['reservePrice'], 'disabled' => 'disabled'));
		if (empty($this->data['SchedulingMaster']['numDaysToRun'])) {
			$this->data['SchedulingMaster']['numDaysToRun'] = 3;
		}
		
		/* TODO: Marketing/Judy need to be able to se num days to an arbitrary #, and unlock scheduling delay */
		echo $form->input('numDaysToRun', array('type' => 'select', 'options' => array(2 => '2', 3 => '3', 7 => '7')));
		echo $form->input('schedulingDelayCtrlId', array('onchange' => 'this.selectedIndex = 0', 'readonly' => 'readonly', 'label' => 'Scheduling Delay'));
		
		$iterationsStyle = $endDateStyle = ' style="padding: 0; margin: 0"';
		//shows only when fixed number of iterations is selected
		if (!empty($this->data['SchedulingMaster']['iterationSchedulingOption']) && $this->data['SchedulingMaster']['iterationSchedulingOption'] == 1) {
			$iterationsStyle = ' style="padding: 0; margin: 0; display: none"';
		} else {
			$endDateStyle = ' style="padding: 0; margin: 0; display: none"';
		}
		
		//the scheduling iteration option is 0 = iterations, 1 = endDate
		echo $form->input('iterationSchedulingOption', array('type' => 'hidden'));
		echo '<div id="iterations"'.$iterationsStyle.'>';
		echo $form->input('iterations', array('after' => 'Or, <a href="#" onclick=\'javascript:$("SchedulingMasterIterationSchedulingOption").value = "1"; $("iterations").toggle(); $("endDate").toggle() \'>choose infinite iterations until end date.</a>'));
		echo '</div>';
		
		//shows only when infinite iterations until end date is selected
		echo '<div id="endDate"'.$endDateStyle.'>';
		echo $form->input('endDate', array('minYear' => date('Y'), 'maxYear' => $packageEndDate['year'], 'after' => 'Or, <a href="#" onclick=\'$("SchedulingMasterIterationSchedulingOption").value = "0"; javascript:$("iterations").toggle(); $("endDate").toggle() \'>choose fixed number of iterations</a>'));
		echo '</div>';
		
		echo $form->input('startDate', array('minYear' => date('Y'), 'maxYear' => $packageEndDate['year']));
		echo $form->input('packageName', array('value' => $package['Package']['packageName'], 'type' => 'hidden'));
		echo $form->input('subTitle',  array('value' => $package['Package']['subtitle'], 'type' => 'hidden'));
		echo $form->input('packageId', array('value' => $packageId, 'type' => 'hidden'));
		
		echo $ajax->observeField('SchedulingMasterOfferTypeId', array(
																'url' => '/scheduling_masters/getOfferTypeDefaults/packageId:'.$packageId,
																'frequency' => 0.2,
																'update' => 'defaults',
																'complete' => 'new Effect.Highlight("defaults")'));
	?>
		</div>
		<div id='two' style="display: none">	
		<?php echo $form->input('MerchandisingFlag'); ?>
		</div>
		<div id='three' style="display: none">
			<?php echo $this->renderElement('../scheduling_masters/_mystery_fields'); ?>
		</div>
		<div id='four' style="display: none">
			<?php echo $form->input('previewDate'); ?>
		</div>
	</fieldset>