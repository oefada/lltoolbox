<div class="schedulingMasters form">
    <?php $session->flash();
	$session->flash('error');
	?>
	<?php /* TODO: Move these styles outside of here when it's finalized*/ ?>
<style>
div {
	padding: 0;
	margin: 0;
}
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
<?php
echo $ajax->form('edit', 'post', array('url' => "/scheduling_masters/edit/{$this->data['SchedulingMaster']['schedulingMasterId']}", 'update' => 'MB_content', 'model' => 'SchedulingMaster', 'complete' => 'Modalbox.activate(); closeModalbox()', 'before' => 'Modalbox.deactivate();', 'indicator' => 'spinner'));
?>
<ul id="tabs_example_one" class="subsection_tabs">
	<li class="tab"><a class="active" href="#one">Setup</a></li>
	<li class="tab"><a class="" href="#two">Merchandising</a></li>
	<li class="tab" id='mysteryTab' style="display: none"><a class="" href="#three">Mystery Auction Setup</a></li>
</ul>
	<fieldset>
		<?php if ($remainingIterations): ?>
			<h3 class='icon-error'>Changes to this scheduling master are not allowed. Atleast one offer has already gone live, you must delete all future iterations and create a new scheduling master to make changes.</h3>
		<?php else: ?>
			<h3 class='icon-error'>This Scheduling Master can only be viewed and not edited because all iterations have already gone live.</h3>
		<?php endif; ?>
		<div id='one'>
	<?php
		echo $form->input('schedulingStatusId');
		echo $form->input('offerTypeId', array('label' => 'Offer Type'));
		echo '<div id="defaults">';
		if (isset($defaultFile)) {
			echo $this->renderElement('../scheduling_masters/'.$defaultFile);
		}
		echo '</div>';
		echo $form->input('numDaysToRun');
		echo $form->input('schedulingDelayCtrlId');
		
		//the scheduling iteration option is 0 = iterations, 1 = endDate
		if (!$this->data['SchedulingMaster']['iterationSchedulingOption']) {
			echo $form->input('iterations');
		} else {
			echo $form->input('endDate');
		}
		
		echo $form->input('iterationSchedulingOption', array('type' => 'hidden'));
		echo $form->input('previewDate');
		echo $form->input('startDate');
		echo $form->input('additionalDescription', array('rows' => 2));
		
		echo $ajax->observeField('SchedulingMasterOfferTypeId', array(
																'url' => '/scheduling_masters/getOfferTypeDefaults/packageId:'.$this->data['SchedulingMaster']['packageId'],
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
<?php
if ($remainingIterations) {
	echo $form->end('Schedule Me');
} else {
	echo "</form>";
}?>
<?=$ajax->link('Delete this scheduling master or all future iterations',
			   array('action' => 'delete', $this->data['SchedulingMaster']['schedulingMasterId']),
			   array('update' => 'MB_content', 'complete' => 'closeModalbox()'),
			   'Are you sure? This action is irreversible.');
?>
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