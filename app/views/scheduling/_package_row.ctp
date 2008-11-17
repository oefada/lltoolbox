<div id='package_<?=$package['Package']['packageId']?>'>
<div class='pkgTitle clearfix'>
	<div class='title'>
		<strong>Package <?=$row?></strong> - <?=$package['Package']['packageName']?> <?=$html2->c($package['Package']['packageId'], 'ID')?>
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
<div class='sGrid'>
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
	<?php 
	if (substr($mstrStartDate, 0, 10) == substr($startDate, 0, 10)) {
	?>
		<div style="width: <?=$width?>%; left: <?=$left?>%"<?=$class?> ondblclick="Modalbox.show('/scheduling_masters/edit/<?=$instance['schedulingMasterId']?>', {title: 'Edit Scheduling Master'});">	
		RV: <?=$number->currency($master['SchedulingMaster']['retailValue'])?>
	<?php
	} else {
	?>
		<div style="width: <?=$width?>%; left: <?=$left?>%"<?=$class?>>	
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