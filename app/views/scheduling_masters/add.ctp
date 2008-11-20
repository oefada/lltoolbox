<div class="schedulingMasters form">
    <?php $session->flash();
	$session->flash('error');
	?>
	<?php /* TODO: Move these styles outside of here when it's finalized*/ ?>
<style>
/* Subsection Tabs
--------------------*/
ul.subsection_tabs {
	list-style:none;
	margin:0 0 5px 0;
	clear:both;
	height: 30px;
}

ul.subsection_tabs li.tab {
	float:left;
	margin-right:7px;
	text-align:center;
}

ul.subsection_tabs li.tab a {
	display:block;
	margin: 0;
	padding: 10px 10px 0 10px;
	color:#7f0000;
	text-decoration: none;
}

ul.subsection_tabs li.tab a:hover {
	color:#666;
}

ul.subsection_tabs li.tab a.active {
	color: #fff;
	padding: 10px 10px 0 10px;
	background-color:#545454;
}

</style>
<?php echo $ajax->form('add', 'post', array('url' => "/scheduling_masters/add/packageId:{$packageId}", 'update' => 'MB_content', 'model' => 'SchedulingMaster', 'complete' => 'Modalbox.activate(); closeModalbox()', 'before' => 'Modalbox.deactivate();', 'indicator' => 'spinner'));?>
<ul id="tabs_example_one" class="subsection_tabs">
	<li class="tab"><a class="active" href="#one">Setup</a></li>
	<li class="tab"><a class="" href="#two">Merchandising</a></li>
	<li class="tab" id='mysteryTab' style="display: none"><a class="" href="#three">Mystery Auction Setup</a></li>
</ul>
	<fieldset>
		<div id='one'>
			<p class='clean-gray'><?php echo $package['Package']['shortBlurb'] ?> <?php echo $package['Package']['shortBlurb'] ?> <?php echo $package['Package']['shortBlurb'] ?> <?php echo $package['Package']['shortBlurb'] ?> <?php echo $package['Package']['shortBlurb'] ?></p>
	<?php
		echo $form->input('offerTypeId', array('label' => 'Offer Type'));
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
		echo $form->input('iterationSchedulingOption', array('type' => 'hidden'));
		echo '<div id="iterations"'.$iterationsStyle.'>';
		echo $form->input('iterations', array('after' => 'Or, <a href="#" onclick=\'javascript:$("SchedulingMasterIterationSchedulingOption").value = "1"; $("iterations").toggle(); $("endDate").toggle() \'>choose infinite iterations until end date.</a>'));
		echo '</div>';
		
		//shows only when infinite iterations until end date is selected
		echo '<div id="endDate"'.$endDateStyle.'>';
		echo $form->input('endDate', array('minYear' => date('Y'), 'maxYear' => $packageEndDate['year'], 'after' => 'Or, <a href="#" onclick=\'$("SchedulingMasterIterationSchedulingOption").value = "0"; javascript:$("iterations").toggle(); $("endDate").toggle() \'>choose fixed number of iterations</a>'));
		echo '</div>';
		
		echo $form->input('previewDate');
		echo $form->input('startDate', array('minYear' => date('Y'), 'maxYear' => $packageEndDate['year']));
		echo $form->input('additionalDescription', array('rows' => 2));
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
		<div id='three' style="display: none">
			<?php
			$openingBid 	= (!empty($this->data['SchedulingMaster']['openingBid'])) 	? $this->data['SchedulingMaster']['openingBid'] 	: 1;
			$bidIncrement 	= (!empty($this->data['SchedulingMaster']['bidIncrement'])) ? $this->data['SchedulingMaster']['bidIncrement'] 	: 1;
			echo $form->input('Mystery.openingBid', array('value' => $openingBid));
			echo $form->input('Mystery.bidIncrement', array('value' => $bidIncrement));
			echo $form->input('Mystery.packageName');
			echo $form->input('Mystery.subtitle');
			echo $form->input('Mystery.shortBlurb', array('rows' => 3));
			?>
		</div>
	</fieldset>
<?php echo $form->end('Schedule Me');?>
</div>
<script>
new Control.Tabs('tabs_example_one', {afterChange: function(){Modalbox.resizeToContent()}});

function merchandisingSetup(element, value) {
	for (var i = 0; i < value.length; i++) {
		if (value[i] == 3) {
			if ($('mysteryTab').getStyle('display') == 'none') {
				$('mysteryTab').show();
				new Effect.Highlight('mysteryTab', {duration: 2});
			}
			return;
		}
	}
	$('mysteryTab').hide();
}

new Form.Element.EventObserver('MerchandisingFlagMerchandisingFlag', function(element, value) {merchandisingSetup(element, value);});
</script>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>