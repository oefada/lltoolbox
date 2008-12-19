<? define('CELL_WIDTH',  100/$monthDays); ?>
<div id='package_<?=$package['Package']['packageId']?>' class='collapsible'>
<div class='handle'>&nbsp;</div>
<div class='pkgTitle clearfix'>
	<div class='title' id='packageTitle<?=$package['Package']['packageId']?>'>
		<?=$html->link($text->truncate(trim($package['Package']['packageName']), 55), "/clients/$clientId/packages/edit/{$package['Package']['packageId']}")?> <?=$html2->c($package['Package']['packageId'], 'ID')?>
	</div>
	<div class='scheduleThisPackage'>
		<strong>Schedule This Package</strong> - Start Date
		<input type="hidden" class="format-y-m-d divider-dash range-low-today fill-grid-no-select opacity-99" id="dp-package-<?=$package['Package']['packageId']?>" name="dp-normal-<?=$package['Package']['packageId']?>" value="<?=$year.'-'.$month.'-01'?>" maxlength="10"/>
		<script>
			var dp = datePickerController.datePickers["dp-package-<?=$package['Package']['packageId']?>"];
			new Form.Element.Observer($("dp-package-<?=$package['Package']['packageId']?>"), 0.2, function() { if($F("dp-package-<?=$package['Package']['packageId']?>") == '') { return; } openSchedulingOverlay("dp-package-<?=$package['Package']['packageId']?>", <?=$package['Package']['packageId']?>, '<?=htmlentities($package['Package']['packageName'])?>'); $("dp-package-<?=$package['Package']['packageId']?>").value = ''});
		</script>
	</div>
</div>
<div class='sGrid collapsibleContent disableAutoCollapse'>
	<?
	$validityEndDate = strtotime($package['Package']['validityEndDate']);
	$validityFlagDate = strtotime('-60 days', $validityEndDate);
	
	if (date('m', $validityFlagDate) == $month):
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
<? foreach($master['SchedulingInstance'] as $instance):

	$startDate = $instance['startDate'];
	$endDate = $instance['endDate'];
	$days = strtotime($endDate)-strtotime($startDate);
	
	$days = date('j', $days);
	$beginHour = date('G', strtotime($startDate));
	$endHour = date('G', strtotime($endDate));
	$beginOn = date('j', strtotime($startDate));
	
	$width = 100/$monthDays * $days;// + (100/$monthDays * $endHour/24);
	$lastMonthDays = date("t", strtotime($year . "-" . $month . "-01 -1 month"));

	if (date('m', strtotime($startDate)) != $month) {
		$left = 0-100/$monthDays*($lastMonthDays-$beginOn+1);
	} else {
		$left = 100/$monthDays*($beginOn-1); // + (100/$monthDays * $beginHour/24);
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
	if (substr($mstrStartDate, 0, 10) == substr($startDate, 0, 10)) {
	?>
		<div id='schedulingMaster<?=$master['SchedulingMaster']['schedulingMasterId']?>' style="width: <?=$width?>%; left: <?=$left?>%"<?=$class?> onclick="Modalbox.show('/scheduling_masters/edit/<?=$instance['schedulingMasterId']?>', {title: 'Edit Scheduling Master'});">	
		<strong>Retail Value</strong><br /><?=$number->currency($master['SchedulingMaster']['retailValue'])?>
		
		
	<?php
		$prototip->tooltip('schedulingMaster'.$master['SchedulingMaster']['schedulingMasterId'], array('ajax' =>
		 																		array('url' => '/scheduling_masters/performanceTooltip/'.$master['SchedulingMaster']['schedulingMasterId'], 
																						'options' => array('method' => 'get')
																					),
																				'title' => 'Scheduling Master Performance'
																				));
	} else {
	?>
		<div id="schedulingInstance<?=$instance['schedulingInstanceId']?>"style="width: <?=$width?>%; left: <?=$left?>%"<?=$class?>>	
		Iteration 
	<?php
		$prototip->tooltip('schedulingInstance'.$instance['schedulingInstanceId'], array('ajax' =>
		 																		array('url' => '/scheduling_instances/performanceTooltip/'.$instance['schedulingInstanceId'], 
																						'options' => array('method' => 'get')
																					),
																				'title' => 'Scheduling Instance Performance'
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