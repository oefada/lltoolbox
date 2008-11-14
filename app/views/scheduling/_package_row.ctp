<div id='package_<?=$package['Package']['packageId']?>'>
<div class='pkgTitle clearfix'>
	<div class='title'>
		<strong>Package <?=$row?></strong> - <?=$package['Package']['packageName']?> <?=$html2->c($package['Package']['packageId'], 'ID')?>
	</div>
	<div class='scheduleThisPackage'>
		<strong>Schedule This Package</strong> - Start Date
		<input type="hidden" class="format-y-m-d divider-dash" id="dp-package-<?=$package['Package']['packageId']?>" name="dp-normal-<?=$package['Package']['packageId']?>" value="<?=$year.'-'.$month.'-01'?>" maxlength="10"/>
		<script>
			var dp = datePickerController.datePickers["dp-package-<?=$package['Package']['packageId']?>"];
			new Form.Element.EventObserver($("dp-package-<?=$package['Package']['packageId']?>"), function() { openSchedulingOverlay("dp-package-<?=$package['Package']['packageId']?>", <?=$package['Package']['packageId']?>); });
		</script>
	</div>
</div>
<div class='sGrid'>
<?php
if (!is_array($package['Scheduling'])) { $package['Scheduling'] = array(); }
foreach($package['Scheduling'] as $master):
	$mstrStartDate = $master['SchedulingMaster']['startDate'];
	if (count($master['SchedulingInstance'])):
?>
<div class='masterRow'>
<?= $this->renderElement('../scheduling/_days', array('grid' => true)) ?>

<? foreach($master['SchedulingInstance'] as $instance):
	$startDate = $instance['startDate'];
	$endDate = $instance['endDate'];
	$days = strtotime($endDate)-strtotime($startDate);
	
	$days = date('j', $days);
	$beginOn = date('j', strtotime($startDate));
	
	$width = 100/$monthDays * $days;
	$lastMonthDays = date("t", strtotime($year . "-" . $month . "-01 -1 month"));

	if (date('m', strtotime($startDate)) != $month) {
		$left = 0-100/$monthDays*($lastMonthDays-$beginOn+1);
	} else {
		$left = 100/$monthDays*($beginOn-1);
	}

	$classes = array('sItem', 'oType'.$master['SchedulingMaster']['offerTypeId']);

	if (substr($mstrStartDate, 0, 10) == substr($startDate, 0, 10)) {
		$classes[] = 'sMaster';
	} else {
		$classes[] = 'sIteration';
	}
	
	$class = " class='".implode($classes, ' ')."'";
	
	?>
	<div style="width: <?=$width?>%; left: <?=$left?>%"<?=$class?>>	
	<?php 
	if (substr($mstrStartDate, 0, 10) == substr($startDate, 0, 10)) {
	?>
		RV: <?=$number->currency($master['SchedulingMaster']['retailValue'])?>
	<?php
	} else {
	?>
		Iteration 
	<?php
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