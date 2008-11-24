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
<?php echo $ajax->form('add', 'post', array('url' => "/scheduling_masters/add/packageId:{$packageId}", 'update' => 'MB_content', 'model' => 'SchedulingMaster', 'complete' => 'Modalbox.activate(); closeModalbox()', 'before' => 'Modalbox.deactivate();', 'indicator' => 'spinner'));?>
<ul id="tabs_example_one" class="subsection_tabs">
	<li class="tab"><a class="active" href="#one">Setup</a></li>
	<li class="tab"><a class="" href="#two">Merchandising</a></li>
	<?php
	//only hide the mystery setup if the mystery flag is not set
	$style = ' style="display: none"';
	if (in_array(3, $this->data['MerchandisingFlag']['MerchandisingFlag'])) {
			$style = '';
	}
	?>
	<li class="tab" id='mysteryTab'<?=$style?>><a class="" href="#three">Mystery Auction Setup</a></li>
</ul>

<?php echo $this->renderElement('../scheduling_masters/_form') ?>

<?php echo $form->end('Schedule Me');?>
</div>
<script>
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

new Control.Tabs('tabs_example_one', {afterChange: function(){Modalbox.resizeToContent()}});
</script>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>