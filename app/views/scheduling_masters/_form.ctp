	<fieldset>
		<div id='one'>
			<p class='clean-gray'>
				<? if(!empty($this->data['SchedulingMaster']['schedulingMasterId'])): ?>
				<strong>Scheduling Master Id:</strong> <?php echo $this->data['SchedulingMaster']['schedulingMasterId'] ?><br />
				<? endif; ?>
			<?php echo $package['Package']['shortBlurb'] ?>
			</p>
	<?php
		echo $form->input('offerTypeId', array('label' => 'Offer Type'));
		echo "<strong>For Fixed Price offer types, number of days to run, scheduling delay, and number of iterations will be ignored. You must choose an end date for fixed price offers.</strong>";		
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
		if (empty($this->data['SchedulingMaster']['iterations'])) {
			echo $form->input('iterations', array('value' => 1, 'after' => 'Or, <a href="#" onclick=\'javascript:$("SchedulingMasterIterationSchedulingOption").value = "1"; $("iterations").toggle(); $("endDate").toggle() \'>choose end date.</a>'));
		} else {
			echo $form->input('iterations', array('after' => 'Or, <a href="#" onclick=\'javascript:$("SchedulingMasterIterationSchedulingOption").value = "1"; $("iterations").toggle(); $("endDate").toggle() \'>choose end date.</a>'));
		}
		echo '</div>';
		
		//shows only when infinite iterations until end date is selected
		echo '<div id="endDate"'.$endDateStyle.'>';
		echo $form->input('endDate', array('minYear' => date('Y'), 'maxYear' => $packageEndDate['year'], 'after' => 'Or, <a href="#" onclick=\'$("SchedulingMasterIterationSchedulingOption").value = "0"; javascript:$("iterations").toggle(); $("endDate").toggle() \'>choose fixed number of iterations</a>'));
		echo '</div>';
		
		echo $form->input('startDatePicker', array('class' => 'format-m-d-y divider-dash highlight-days-06 no-transparency range-low-today fill-grid-no-select',
													'label' => 'Start Date',
													'readonly' => 'readonly'));
?>
<script>
delete datePickerController.datePickers['SchedulingMasterStartDatePicker'];
datePickerController.addDatePicker('SchedulingMasterStartDatePicker', {'id':'SchedulingMasterStartDatePicker',
																		'highlightDays':'0,0,0,0,0,1,1',
																		'disableDays':'',
																		'divider':'-',
																		'format':'m-d-y',
																		'locale':true,
																		'splitDate':0,
																		'noTransparency':true,
																		'staticPos':false,
																		'hideInput':false,
																		'low':datePickerController.dateFormat((new Date().getMonth() + 1) + "/" + new Date().getDate() + "/" + new Date().getFullYear(), true)
																		});

</script>
<?	    
		echo $form->label('Start Time');
		echo $form->dateTime('startDateTime', 'NONE', '12', null, array(), false);

		echo $form->input('trackId', array('value' => $package['ClientLoaPackageRel'][0]['trackId'], 'type' => 'hidden'));
		echo $form->input('packageName', array('value' => $package['Package']['packageTitle'], 'type' => 'hidden'));
		echo $form->input('shortBlurb', array('value' => $package['Package']['shortBlurb'], 'type' => 'hidden'));
		echo $form->input('subtitle',  array('value' => $package['Package']['subtitle'], 'type' => 'hidden'));
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