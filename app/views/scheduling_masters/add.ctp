<div class="schedulingMasters form">
    <?php $session->flash();
	$session->flash('error');
	?>
<style>
/* Subsection Tabs
--------------------*/
ul.subsection_tabs {
	list-style:none;
	margin:0 0 5px 0;
	padding:0;
	clear:both;
	border-bottom:1px solid #ccc;
	height:20px;
	clear:both;
}

ul.subsection_tabs li.tab {
	float:left;
	margin-right:7px;
	text-align:center;
}

ul.subsection_tabs li.tab a {
	display:block;
	height:20px;
	padding:0 6px 0 6px;
	background-color:#fff;
	color:#666;
	width:80px;
}

ul.subsection_tabs li.tab a:hover {
	color:#666;
}

ul.subsection_tabs li.tab a.active {
	background-color:#ddd;
}

ul.subsection_tabs li.source_code {
	float:right;
}

</style>
<?php echo $ajax->form('add', 'post', array('url' => "/scheduling_masters/add/packageId:{$packageId}", 'update' => 'MB_content', 'model' => 'SchedulingMaster', 'complete' => 'Modalbox.activate(); closeModalbox()', 'before' => 'Modalbox.deactivate();', 'indicator' => 'spinner'));?>
<ul id="tabs_example_one" class="subsection_tabs">
	<li class="tab"><a class="active" href="#one">Setup</a></li>
	<li class="tab"><a class="" href="#two">Merchandising</a></li>
</ul>
	<fieldset>
 		<legend><?php echo $package['Package']['packageName'] ?></legend>
	
		<div id='one'>
	<?php
		echo $form->input('offerTypeId', array('label' => 'Type'));
		echo '<div id="defaults">';
		if (isset($defaultFile)) {
			echo $this->renderElement('../scheduling_masters/'.$defaultFile);
		}
		echo '</div>';
		echo $form->input('numDaysToRun');
		echo $form->input('schedulingDelayCtrlId', array('onchange' => 'this.selectedIndex = 0'));
		
		$iterationsStyle = $endDateStyle = ' style="padding: 0; margin: 0"';
		//shows only when fixed number of iterations is selected
		if (!empty($this->data['SchedulingMaster']['iterationSchedulingOption']) && $this->data['SchedulingMaster']['iterationSchedulingOption'] == 1) {
			$iterationsStyle = ' style="padding: 0; margin: 0; display: none"';
		} else {
			$endDateStyle = ' style="padding: 0; margin: 0; display: none"';
		}
		
		//the scheduling iteration option is 0 = iterations, 1 = endDate
		echo $form->input('iterationSchedulingOption', array('type' => 'text'));
		echo '<div id="iterations"'.$iterationsStyle.'>';
		echo $form->input('iterations', array('after' => 'Or, <a href="#" onclick=\'javascript:$("SchedulingMasterIterationSchedulingOption").value = "1"; $("iterations").toggle(); $("endDate").toggle() \'>choose infinite iterations until end date.</a>'));
		echo '</div>';
		
		//shows only when infinite iterations until end date is selected
		echo '<div id="endDate"'.$endDateStyle.'>';
		echo $form->input('endDate', array('minYear' => date('Y'), 'maxYear' => $packageEndDate['year'], 'after' => 'Or, <a href="#" onclick=\'$("SchedulingMasterIterationSchedulingOption").value = "0"; javascript:$("iterations").toggle(); $("endDate").toggle() \'>choose fixed number of iterations</a>'));
		echo '</div>';
		
		echo $form->input('schedulingStatusId');
		echo $form->input('previewDate');
		echo $form->input('startDate', array('minYear' => date('Y'), 'maxYear' => $packageEndDate['year']));
		
		echo $form->input('packageName', array('value' => $package['Package']['packageName'], 'type' => 'hidden'));
		echo $form->input('subTitle',  array('value' => $package['Package']['subtitle'], 'type' => 'hidden'));
		echo $form->input('packageId', array('value' => $packageId, 'type' => 'hidden'));
		
		echo $ajax->observeField('SchedulingMasterOfferTypeId', array(
																'url' => '/scheduling_masters/getOfferTypeDefaults/packageId:'.$packageId,
																'frequency' => 0.2,
																'update' => 'defaults'));
	?>
		</div>
		<div id='two' style="display: none">	
		<?php echo $form->input('MerchandisingFlag'); ?>
		</div>
	</fieldset>
<?php echo $form->end('Schedule Me');?>
</div>
<script>

new Control.Tabs('tabs_example_one');

</script>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>