<div class="schedulingMasters form">
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
<?php echo $ajax->form('add', 'post', array('url' => "/scheduling_masters/add/packageId:{$packageId}", 'update' => 'MB_content', 'model' => 'SchedulingMaster', 'complete' => 'closeModalbox()', 'before' => 'Modalbox.deactivate();', 'indicator' => 'spinner'));?>
<ul id="tabs_example_one" class="subsection_tabs">
	<li class="tab"><a class="active" href="#one">Setup</a></li>
	<li class="tab"><a class="" href="#two">Merchandising</a></li>
</ul>
	<fieldset>
 		<legend><?php echo $package['Package']['packageName'] ?></legend>
	
		<div id='one'>
	<?php
		echo $form->input('offerTypeId', array('label' => 'Type'));
		echo $form->input('openingBid');
		echo $form->input('retailValue');
		echo $form->input('maxBid');
		echo $form->input('bidIncrement');
		echo $form->input('numWinners');
		echo $form->input('buyNowPrice');
		echo $form->input('numDaysToRun');
		echo $form->input('schedulingDelayCtrlId');
		echo $form->input('iterations');
		echo $form->input('endDate');
		echo $form->input('schedulingStatusId');
		echo $form->input('previewDate');
		echo $form->input('startDate');
		
		echo $form->input('packageName', array('value' => $package['Package']['packageName'], 'type' => 'hidden'));
		echo $form->input('subTitle',  array('value' => $package['Package']['subtitle'], 'type' => 'hidden'));
		echo $form->input('packageId', array('value' => $packageId, 'type' => 'hidden'));
	?>
		</div>
		<div id='two' style="display: none">	
		<?php echo $form->input('MerchandisingFlag'); ?>
		</div>
	</fieldset>
<?php echo $form->end('Schedule Me');?>
</div>
<script>new Control.Tabs('tabs_example_one');  </script>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>