<?php $urls = array('LL' => 'luxurylink.com',
                    'Family' => 'familygetaway.com');
?>

<div id='package_<?=$package['Package']['packageId']?>' class="collapsible <?php echo $site; ?>">
<div class='handle'>&nbsp;</div>
<div class='pkgTitle clearfix'>
	<div class='title' id='packageTitle<?=$package['Package']['packageId']?>'>
		<?=$html->link($text->truncate(trim($package['Package']['packageName']), 45), "/clients/$clientId/packages/summary/{$package['Package']['packageId']}")?> <?=$html2->c($package['Package']['packageId'], 'ID')?>
		<?=$html->link("View on {$site}", "http://www.".$urls[$site]."/luxury-hotels/{$package['Client']['seoName']}.html?clid={$package['Client']['clientId']}&pkid={$package['Package']['packageId']}", array('target' => '_blank'))?>
	</div>

		<? 

		// ticket 1566
		// mbyrnes 2011-02-17
		$link_arr=array(
			"controller"=>"scheduling", 
			"action"=>"delPackageOffers",
			$package['Client']['clientId'],
			$package['Package']['packageId']
		);
		// can't geth $html->link() working with styles
		echo "<style>dumb_link{
			color:#ffffff;
			}
			a.dumb_link:link{color:#FFFFFF}
			a.dumb_link:hover{color:#7F0000}
			a.dumb_link:visited{color:#ffffff;text-decoration:underline;}
			";
		echo "</style>";
		echo "<div class='del_package_offers' style='color:#FFFFFF;margin-left:10px;font-weight:bold;float:left;'>";
		//echo $html->link('Delete offers under this package',$link_arr,array(),"Are you sure?");
		echo "<a onclick='return confirm(\"Are you sure?\");' class='dumb_link' href='/scheduling/delPackageOffers/".$package['Client']['clientId']."/".$package['Package']['packageId']."'>Delete offers under this package</a>";
		echo "<div style='clear:both;'></div>";
		echo "</div>";

		?>

	<div class='scheduleThisPackage'>
		<span class="masterListTarget" id="masterListTarget<?=$package['Package']['packageId']?>"><a href="javascript: void(0);" style="color: #fff"><?= count($package['Package']['masterList']) ?> Masters</a></span>
		<div class="masterList" id="masterList<?=$package['Package']['packageId']?>" style="display: none">
		<ul>
		<?php 
		      // 06/13/2011 jwoods - ticket 1872 - unique display for Hotel Offers 
		      $packageIsHotelOffer = false;
		      foreach($package['Package']['masterList'] as $m):
			$offerTypeName = $m['OfferType']['offerTypeName'];
			$offerTypeId = $m['OfferType']['offerTypeId'];
			$m = $m['SchedulingMaster'];
			if ($offerTypeId == 7) { $packageIsHotelOffer = true; } ?>
			
			<li style="margin-bottom:5px;"><b><?=$offerTypeName?></b><br/><a href="javascript: void(0);" onclick="Modalbox.show('/scheduling_masters/edit/<?=$m['schedulingMasterId']?>', {title: 'Edit Scheduling Master'});">Master Id #<?=$m['schedulingMasterId']?><br/><?=date('M d, Y', strtotime($m['startDate']))?> - <?=date('M d, Y', strtotime($m['endDate']))?></a></li>

		     <?php endforeach; ?>
		</ul>	
		</div>
		
		<? 	
		if ($packageIsHotelOffer) { ?>
			<strong><< Edit Hotel Offer Dates</strong>
		<? } else { ?>
		
			<strong>Schedule This Package</strong> - Start Date
			<input type="hidden" class="format-y-m-d divider-dash range-low-today fill-grid-no-select opacity-99" id="dp-package-<?=$package['Package']['packageId']?>" name="dp-normal-<?=$package['Package']['packageId']?>" value="<?=$year.'-'.$month.'-01'?>" maxlength="10"/>
			<script>
				Event.observe(window,'load', function() {
					$("fd-dp-package-<?=$package['Package']['packageId']?>").getElementsBySelector('td').each(function(obj, index){
							obj.onclick = null;
					});
					$("fd-dp-package-<?=$package['Package']['packageId']?>").observe('click', function(event) {
						arr = event.element().className.match(/dmy-(.*)-(.*)-(.*)\s/);

						if(arr[1]) {
							$("fd-dp-package-<?=$package['Package']['packageId']?>").fade();
							openSchedulingOverlay(arr[3]+'-'+arr[2]+'-'+arr[1], <?=$package['Package']['packageId']?>, <?="'",str_replace("'","\'",htmlentities($package['Package']['packageName'])),"'"?>);
						}
						return false;});
				});
				/*new Form.Element.Observer($("dp-package-<?=$package['Package']['packageId']?>"), 0.2, function() { if($F("dp-package-<?=$package['Package']['packageId']?>") == '') { return; } openSchedulingOverlay("dp-package-<?=$package['Package']['packageId']?>", <?=$package['Package']['packageId']?>, <?="'",str_replace("'","\'",htmlentities($package['Package']['packageName'])),"'"?>); $("dp-package-<?=$package['Package']['packageId']?>").value = ''});*/
			</script>
		
		<? } ?>
		
	</div>
</div>
<div class='sGrid collapsibleContent disableAutoCollapse'>
	<?
	$validityEndDate = strtotime($package['Package']['validityEndDate']);
	$validityFlagDate = strtotime('-60 days', $validityEndDate);
	
	if (date('m', $validityFlagDate) == $month && date('Y', $validityFlagDate) == $year):
		$flagPosition = CELL_WIDTH * (date('d', $validityFlagDate) - 1);
	?>
	<div id='package-<?=$package['Package']['packageId']?>-flags' class="packageFlags">
	<div id='package-<?=$package['Package']['packageId']?>-flag-validityEndDate' class="flag validityEndDateFlag" style='width: <?=CELL_WIDTH?>; left: <?=$flagPosition?>%'>
		<?php
		echo $html->image('flag.png');
		$prototip->tooltip('package-'.$package['Package']['packageId'].'-flag-validityEndDate', 'Validity End Date is 60 days from now on '.$package['Package']['validityEndDate'], array('title' => 'Flag Details'));
		?>
		
	</div>
	</div>
	<?php endif; ?>
<?php
if($this->data['masterRows'] >= 5) {
	$this->data['masterRows'] = 0;
	echo $this->renderElement('../scheduling/_days');
}
?>
<?php
if (!is_array($package['Scheduling'])) { $package['Scheduling'] = array(); }
foreach($package['Scheduling'] as $k => $master):
	$mstrStartDate = $master['SchedulingMaster']['startDate'];

	if (count($master['SchedulingInstance'])):
	$this->data['masterRows']++;
?>
<div class='masterRow'>
<?= $this->renderElement('../scheduling/_days', array('grid' => true)) ?>
<?  $firstInstanceDisplayed = false;
    foreach($master['SchedulingInstance'] as $instance):

	$startDate = $instance['startDate'];
	$startDateOnly = explode(' ', $startDate);
	$startDateOnly = $startDateOnly[0];
	
	$endDate = $instance['endDate'];
	$days = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);

	$beginHour = date('G', strtotime($startDate));
	$endHour = date('G', strtotime($endDate));
	$beginOn = date('j', strtotime($startDate));
	
	
	$width = $days*100/$monthDays;// + (100/$monthDays * $endHour/24);
	$lastMonthDays = date("t", strtotime($year . "-" . $month . "-01 -1 month"));
	$left = 100/$monthDays*(strtotime($startDateOnly)-strtotime($year.'-'.$month.'-01'))/(60 * 60 * 24);

	if ($width >= 100 && $left < 0 && date('m', strtotime($endDate)) != $month) {
		$width = 100;
		$left = 0;
	} elseif($width >= 100 && $left < 0 && date('m', strtotime($endDate)) == $month) {
		$left = 0;
		$diff = strtotime($endDate)-strtotime($year.'-'.$month.'-01');
		$width = round($diff / 86400)*100/$monthDays;
	}

	$classes = array('sItem', 'oType'.$master['SchedulingMaster']['offerTypeId']);

	if (substr($mstrStartDate, 0, 10) == substr($startDate, 0, 10)) {
		$classes[] = 'sMaster';
	} else {
		$classes[] = 'sIteration';
	}
	
	$class = " class='".implode($classes, ' ')."'";
	?>
	<?php
    $param = (in_array($master['SchedulingMaster']['offerTypeId'], array(1,2,3,6,7))) ? 'instanceId:'.$instance['schedulingInstanceId'] : '';
	if (substr($mstrStartDate, 0, 10) == substr($startDate, 0, 10)) {
	?>
		<div id='schedulingMaster<?=$master['SchedulingMaster']['schedulingMasterId']?>' style="width: <?=$width?>%; left: <?=$left?>%"<?=$class?> onclick="Modalbox.show('/scheduling_masters/edit/<?=$instance['schedulingMasterId']?>/<?php echo $param; ?>', {title: 'Edit Scheduling Master'});">	
		<strong>Retail Value</strong><br />
        <?php if (empty($master['SchedulingMaster']['pricePointId'])): ?>
            <?=$number->currency($master['SchedulingMaster']['retailValue'])?>
        <?php else: ?>
            <?=$number->currency($master['PricePoint']['retailValue'])?><br />
            <?php echo $master['PricePoint']['name']; ?><br />
            Price Point ID: <?php echo $master['PricePoint']['pricePointId']; ?>
        <?php endif; ?>		
		
	<?php
		$prototip->tooltip('schedulingMaster'.$master['SchedulingMaster']['schedulingMasterId'], array('ajax' =>
		 																		array('url' => '/scheduling_instances/performanceTooltip/'.$instance['schedulingInstanceId'], 
																						'options' => array('method' => 'get')
																					),
																				'title' => 'Offer Performance'
																				));
	} else {
	?>
		<div id="schedulingInstance<?=$instance['schedulingInstanceId']?>"style="width: <?=$width?>%; left: <?=$left?>%"<?=$class?> onclick="Modalbox.show('/scheduling_masters/edit/<?=$instance['schedulingMasterId']?>/instanceId:<?php echo $instance['schedulingInstanceId']; ?>', {title: 'Edit Scheduling Master'});">	
		Iteration 

	<? // 08/03/11 jwoods - added info to Auction cells 
	   // day of month is needed to skip partial cells from previous month
	   if (!$firstInstanceDisplayed && date('j', strtotime($startDate)) < 20) {
	   	$firstInstanceDisplayed = true;
	?>
		<br />
		<?php if (empty($master['SchedulingMaster']['pricePointId'])): ?>
		    <?=$number->currency($master['SchedulingMaster']['retailValue'])?>
		<?php else: ?>
		    <?=$number->currency($master['PricePoint']['retailValue'])?><br />
		    <?php echo $master['PricePoint']['name']; ?><br />
		    Price Point ID: <?php echo $master['PricePoint']['pricePointId']; ?>
		<?php endif; ?>	
        <? } ?>


	<?php
		$prototip->tooltip('schedulingInstance'.$instance['schedulingInstanceId'], array('ajax' =>
		 																		array('url' => '/scheduling_instances/performanceTooltip/'.$instance['schedulingInstanceId'], 
																						'options' => array('method' => 'get')
																					),
																				'title' => 'Offer Performance'
																				));
	}
	?>
	</div>
<?  endforeach; //end instance loop ?>
</div>
<? 
endif;


endforeach; //end master loop ?>
</div>
</div>
<?
$prototip->tooltip('packageTitle'.$package['Package']['packageId'], array('ajax' =>
 																		array('url' => '/packages/performanceTooltip/'.$package['Package']['packageId'], 
																				'options' => array('method' => 'get')
																			),
																		'title' => 'Package Performance'
																		));
?>
<script type="text/javascript" language="javascript">
new Tip("masterListTarget<?=$package['Package']['packageId']?>", $("masterList<?=$package['Package']['packageId']?>").cloneNode(true), {
	title: "Scheduling Master List",
	target: $("masterListTarget<?=$package['Package']['packageId']?>"),
	hideOn: { element: 'closeButton', event: 'click' },
	stem: 'topRight',
	hook: { target: 'bottomMiddle', tip: 'topRight' },
	offset: { x: 6, y: 0 },
	width: '200px',
	style: 'toolboxblue',
	showOn: 'click'
});
</script>
