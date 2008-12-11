<?php
$this->pageTitle = strtolower(str_replace(array('\'','"',' '), '', $client['Client']['name'])).'_package_'.$this->data['Package']['packageId'];
?>
<style>
	* {
		font-family: Verdana;
		font-size: 12px;
	}
	th {
		text-align: left;
		font-weight: bold;
	}
	
</style>
<?php foreach($clientLoaDetails as $k => $clientLoaDetail) {?>
	<?=$clientLoaDetail['Client']['name']?><br />
	<? if(!empty($clientLoaDetail['Client']['locationDisplay'])): ?>
	<?=$clientLoaDetail['Client']['locationDisplay']?><br />
	<? endif; ?>
	Percent Revenue: <?=$number->topercentage($clientLoaDetail['ClientLoaPackageRel']['percentOfRevenue'])?>
<?php } ?>

<div style='text-align: center'>
<h1>Luxury Link Suggested Package</h1>
All Prices In <?=$package['Currency']['currencyName']?>
</div>
<table>
	<tr>
		<th>Room Nights</th>
		<td><?=$package['Package']['numNights']?></td>
	</tr>
	<tr>
		<th>Number of Guests</th>
		<td><?=$package['Package']['numGuests']?></td>
	</tr>
	<tr>
		<td colspan=2>&nbsp;</td>
	</tr>
	<tr>
		<th>Inclusions</th>
		<td>
		<? foreach($package['PackageLoaItemRel'] as $itemRel): ?>
			<?=$itemRel['overrideDisplayName']?><br />
		<? endforeach; ?>
		</td>
	</tr>
	<tr>
		<td colspan=2>&nbsp;</td>
	</tr>
	<tr>
		<th>Valid for Travel</th>
		<td>
			<?=date('F d, Y', strtotime($package['Package']['validityStartDate']))?> - <?=date('F d, Y', strtotime($package['Package']['validityEndDate']))?>
		</td>
	</tr>
	<tr>
		<th>Blackout Dates</th>
		<td>
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
		</td>
	</tr>
</table>

<?= $this->renderElement('../packages/package_rate_periods') ?>
		
<table style='width: 100%'>
	<tr>
		<td style="text-align:right">
			Package Retail Value:
		</td>
		<td style="width: 100px">
			<?=$number->currency($this->data['Package']['approvedRetailPrice'], $package['Currency']['currencyCode'])?>
		</td>
	</tr>
	<tr>
		<td style="text-align:right">
			Currency Conversion to USD
		</td>
		<td style="width: 100px">
			$xxx.xx <br />
			1 <?=$package['Currency']['currencyName']?> = xxx.xx USD 
		</td>
	</tr>
</table>

<h3 class="handle">Formats</h3>
		<?php foreach($formats as $formatId => $format): ?>
				<? if(@in_array($formatId, $this->data['Format']['Format'])): ?>
					<?=$this->renderElement('../packages/pdf/format_defaults_'.$formatId); ?>
				<? endif; ?>
		<?php endforeach; ?>