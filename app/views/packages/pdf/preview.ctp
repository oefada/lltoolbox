<?php
$this->pageTitle = strtolower(str_replace(array('\'','"',' '), '', $package['Client']['name'])).'_package_'.$package['Package']['packageId'];
?>
<style>
<? include('css/pdf.css'); ?>
</style>
<div style="border-bottom: 1px solid #000;padding: 10px;"><img src="http://www.luxurylink.com/images/shared/ll_logo2.gif"></div>
<div style="text-align: center">
	<h1><?=$package['Client']['name']?></h1>
	<?=$package['Client']['locationDisplay']?><br />
	<?=$package['Client']['url']?>
</div>
<table class="leftcol">
		<tr><th>LL PID (Internal Use)</th><td><?=$package['Package']['packageId']?></td></tr>
		<tr><th>Date Created</th><td><?=date('F d, Y')?></td></tr>
</table>
<div style="text-align: center">
	<h1 style="text-transform: uppercase">Luxury Link Suggested Package</h1>
	All Prices In <?=$package['Currency']['currencyName']?>
</div>
<table class="leftcol">
		<tr><th>Offer Type</th><td><?=$package['Package']['packageName']?></td></tr>
		<tr><th>Room Nights</th><td><?=$package['Package']['numNights']?></td></tr>
		<tr><th>Number of Guests</th><td><?=$package['Package']['numGuests']?></td></tr>
</table>
<? foreach ($ratePeriods as $k => $rooms): ?>

<? foreach ($rooms['PackageRatePeriod'] as $ratePeriod): ?>
<table style="width: 100%; border: 1px solid #000; margin: 10px 0 10px 0; " class='leftcol'>
	<tr>
		<th style="width: 150px;">Room Type</th>
		<td><?=$items[$k]['LoaItem']['itemName']?></td>
		<td style="width: 250px;"><strong><?=date('M d, Y', strtotime($ratePeriod['startDate']))?> - <?=date('M d, Y', strtotime($ratePeriod['endDate']))?></strong></td>
	</tr>
	<tr>
		<th>Rates, PP, Per Night</th>
		<td><?=$number->currency($ratePeriod['ratePeriodPrice'], $package['Currency']['currencyName'])?></td>
		<td></td>
	</tr>
	<tr>
		<th>Taxes &amp; Gratuities</th>
		<td><?=$number->toPercentage($items[$k]['Fee']['feePercent'])?></td>
		<td style="text-align: right">Total Accomodations:&nbsp;&nbsp;
		<strong><?
		$subtotal = $ratePeriod['ratePeriodPrice']*$package['Package']['numNights'];
		$total = $subtotal + $subtotal*($items[$k]['Fee']['feePercent']/100);
		echo $number->currency($total, $package['Currency']['currencyName'])?>
		</strong>
		</td>
	</tr>
</table>
<? endforeach; ?>
<? endforeach; ?>
<table class='leftcol'>
		<tr><th>Inclusions</th><td><?=$package['Package']['inclusions']?></td></tr>
		<tr><th>Valid for Travel</th><td><?=date('F d, Y', strtotime($package['Package']['validityStartDate']))?> - <?=date('F d, Y', strtotime($package['Package']['validityEndDate']))?></td></tr>
		<tr><th>Blackout Dates</th><td>
		<?
		$days = array('Sundays','Mondays','Tuesdays','Wednesdays','Thursdays','Fridays','Saturdays');
		$blackoutDays = explode(',', $package['Package']['blackoutDays']);
		foreach($blackoutDays as $k => $blackoutDay): ?>
		
			<? echo $days[$blackoutDay];
				if ($k < count($blackoutDays)-1) {
					echo ', ';
				}
			?>
		<? endforeach;
		if (count($blackoutDays)) {
			echo "<br />";
		}
		?>
		
		<?
			$blackoutPeriods = array();
			foreach ($package['PackageValidityPeriod'] as $blackout):
			if ($blackout['isWeekDayRepeat'] == 0 && $blackout['startDate'] == $blackout['endDate']) {
				$blackoutPeriods[] = date('M d, Y', strtotime($blackout['startDate']));
			} elseif ($blackout['isWeekDayRepeat'] == 0) {
				$blackoutPeriods[] = date('M d, Y', strtotime($blackout['startDate'])).' to '. date('M d, Y', strtotime($blackout['endDate']));
			}
			endforeach;
			echo implode('; ', $blackoutPeriods);
		?>
		<? if(!count($blackoutDays) && !count($blackoutPeriods)) { echo 'Subject to availability at time of booking'; }?>
		</td></tr>
		<tr><th>Restrictions</td><td><?=$package['Package']['validityDisclaimer']?><br /></td></tr>
</table>

<? $ratePeriodGrid = $this->renderElement('../packages/package_rate_periods');
if (strpos($ratePeriodGrid, 'No package rate periods') === false) {
	echo $ratePeriodGrid;
}

?>