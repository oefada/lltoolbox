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
<?php echo $ajax->form('edit', 'post', array('url' => "/scheduling_masters/edit/{$this->data['SchedulingMaster']['schedulingMasterId']}", 'update' => 'MB_content', 'model' => 'SchedulingMaster', 'complete' => 'Modalbox.activate(); closeModalbox()', 'before' => 'Modalbox.deactivate();', 'indicator' => 'spinner'));?>
<ul id="tabs_example_one" class="subsection_tabs">
	<li class="tab"><a class="active" href="#one">Setup</a></li>
	<li class="tab"><a class="" href="#two">Merchandising</a></li>
	<?php
	//only hide the mystery setup if the mystery flag is not set
	$style = ' style="display: none"';
	foreach ($this->data['MerchandisingFlag'] as $flag) {
		if ($flag['merchandisingFlagId'] == 3) {
			$style = '';
			break;
		}
	}
	?>
	<li class="tab" id='mysteryTab'<?=$style?>><a class="" href="#three">Mystery Auction Setup</a></li>
	<?php
	//only hide the mystery setup if the mystery flag is not set
	$style = ' style="display: none"';
	foreach ($this->data['MerchandisingFlag'] as $flag) {
		if ($flag['merchandisingFlagId'] == 1) {
			$style = '';
			break;
		}
	}
	?>
	<li class="tab" id='previewTab'<?=$style?>><a class="" href="#four">Preview Setup</a></li>
</ul>

<?php echo $this->renderElement('../scheduling_masters/_form') ?>
<?php echo $form->input('schedulingMasterId') ?>
<?php
//if ($remainingIterations) {
	echo $form->end('Save Changes');
//} else {
//	echo "</form>";
//}
?>
<div style="margin: 0 auto; width: 400px">
<?php if (strtotime($this->data['SchedulingMaster']['startDate']) > strtotime('NOW')) :?>
    <?php $previewType = ($this->data['SchedulingMaster']['offerTypeId'] == 7) ? 'package' : 'pricepoint'; ?>
	<a href="http://www.luxurylink.com/luxury-hotels/preview.html?clid=<?=$package['ClientLoaPackageRel'][0]['clientId']?>&oid=<?=$this->data['PricePoint']['pricePointId']?>&preview=<?php echo $previewType; ?>" class='button' target="_blank"><span>Preview offer on live site</span></a>
<?php else: ?>
    <?php $previewType = ($this->data['SchedulingMaster']['offerTypeId'] == 7) ? 'package' : 'old_offer'; ?>
	<a href="http://www.luxurylink.com/luxury-hotels/preview.html?clid=<?=$package['ClientLoaPackageRel'][0]['clientId']?>&oid=<?=$old_offer_id;?>&preview=<?php echo $previewType; ?>" class='button' target="_blank"><span>Preview old offer on live site</span></a>
<?php endif;?>

<?
/*** Delete/Close Offer Link ***/
$action = 'delete';

switch($masterState) {
	case 0:
		$linkTitle = 'Delete this master and all future instances';
		break;
	case 1:
		switch($this->data['SchedulingMaster']['offerTypeId']) {
			case 3:
			case 4:
				$linkTitle = 'Close this Fixed Price Offer';
				$action = 'closeFixedPriceOffer';
				break;
			case 7:
				$linkTitle = 'Close this Hotel Offer';
				$action = 'closeFixedPriceOffer';
				break;
			default:
				$linkTitle = 'Delete all future instances for this master';
				break;
		}
		break;
}

echo $ajax->link("<span>$linkTitle</span>",
			   array('action' => $action, $this->data['SchedulingMaster']['schedulingMasterId']),
			   array('update' => 'MB_content', 'complete' => 'closeModalbox()', 'class' => 'button'),
			   'Are you sure? This action is irreversible.',
				false);

/*** End Delete/Close Offer Link ***/
?>
</div>
</div>
<script>
function merchandisingSetup(element, value) {
	mysteryTab = false;
	previewTab = false;
	for (var i = 0; i < value.length; i++) {
		if (value[i] == 3) {
			if ($('mysteryTab').getStyle('display') == 'none') {
				$('mysteryTab').show();
				new Effect.Highlight('mysteryTab', {duration: 2});
			}
			mysteryTab = true;
		}
		
		if (value[i] == 1) {
			if ($('previewTab').getStyle('display') == 'none') {
				$('previewTab').show();
				new Effect.Highlight('previewTab', {duration: 2});
			}
			previewTab = true;
		}
	}
	
	if (mysteryTab == false) {
		$('mysteryTab').hide();
	}
	if (previewTab == false) {
		$('previewTab').hide();
	}
}

new Form.Element.EventObserver('MerchandisingFlagMerchandisingFlag', function(element, value) {merchandisingSetup(element, value);});

new Control.Tabs('tabs_example_one', {afterChange: function(){Modalbox.resizeToContent()}});
</script>
<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>
