<?php $this->pageTitle = "Auction Timeslots" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'auction_timeslot'))?>
<fieldset>
<h3 class='title'>SEARCH <?=strtoupper($this->pageTitle)?> BY:</h3>

<div style="float: left; ">

<div class="fieldRow">
<label>Date Closed</label>
<?echo $form->text('condition1.field', array('value' => "DATE_FORMAT(SchedulingInstance.endDate, '%Y-%m-%d')", 'type' => 'hidden'))?>
<div class="range">
	<?echo $datePicker->picker('condition1.value.between.0', array('label' => 'From'))?>
	<?echo $datePicker->picker('condition1.value.between.1', array('label' => 'To'))?>
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d')?>"; $("condition1valueBetween1").value = ""'>Today</a> | 
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 day'))?>"; $("condition1valueBetween1").value = ""'>Yesterday</a> |
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 week'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>This Week</a>
</div>
</div>
<div class="fieldRow">
    <label>Site</label>
    <?php foreach($siteIds as $siteId => $site): ?>
        <?php if (empty($data['condition2']['value']) && $siteId == 1): 
                    $checked = ' checked';
              elseif (!empty($data) && $siteId == $data['condition2']['value']):
                    $checked = ' checked';
              else:
                    $checked = '';
              endif;
        ?>
        <input type="hidden" id="Condition2Field" value="SchedulingMaster.siteId" name="data[condition2][field]" />
        <input id="Condition2Value" type="radio" value="<?php echo $siteId; ?>" name="data[condition2][value]" <?php echo $checked; ?>/> <?php echo $site ?> 
    <?php endforeach; ?>
</div>

</div>
</fieldset>
<?php echo $form->submit('Search') ?>
</div>

<div class='index related'> <?//related class to make the headers lighter?>
<?php
//TODO: put this in a helper
function sortLink($field, $title, $currentPage, $serializedFormInput, $view, $html) {
	$url = "/reports/bids/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:$field";

	if (isset($view->params['named']['sortBy']) && $view->params['named']['sortBy'] == $field) {
		$dir = ($view->params['named']['sortDirection'] == 'ASC') ? 'DESC' : 'ASC';
	} elseif(isset($view->params['named']['sortBy'])  && $view->params['named']['sortBy'] == $field) {
		$dir = 'DESC';
	} else {
		$dir = 'ASC';
	}
	
	$url .= "/sortDirection:$dir";
	
	return $html->link($title, $url);
}

if (!empty($results)): ?>
<?php foreach ($results as $dateKey => $dateRow): //loop through all dates selected ?>
<table>
	<tr>
		<th>Date</th>
		<th>Auction Type</th>
		<th>Before 7am</th>
		<th>7am to 8am</th>
		<th>8am to 9am</th>
		<th>9am to 10am</th>
		<th>10am to 11am</th>
		<th>11am to 12pm</th>
		<th>12pm to 1pm</th>
		<th>1pm to 2pm</th>
		<th>2pm to 3pm</th>
		<th>3pm to 4pm</th>
		<th>4pm to 5pm</th>
		<th>After 5pm</th>
		<th>Type Total</th>
	</tr>
	<tr>
		<th rowspan=<?=count($dateRow)+1?>><?=$dateKey?></th>
	</tr>
	<?php
	$i = 1;
	$totals = array();
	$dateTotals = 0;
	foreach ($dateRow as $k => $r): //loop through all auction type
	$rowTotals = 0;
	$class = ($i++ % 2) ? ' class="altrow"' : '';
		foreach($r as $hour => $col) {
			if (!isset($totals[$hour])) {
				$totals[$hour] = 0;
			}
			$rowTotals += $col;
			$dateTotals += $col;
			$totals[$hour] += $col;
		}
	?>
		<tr<?=$class?> style='text-align: center'>
			<td><?=$k?></td>
			<td><?=(isset($r[-1]) ? $r[-1] : 0 )?></td>
			<td><?=(isset($r[7]) ? $r[7] : 0 )?></td>
			<td><?=(isset($r[8]) ? $r[8] : 0 )?></td>
			<td><?=(isset($r[9]) ? $r[9] : 0 )?></td>
			<td><?=(isset($r[10]) ? $r[10] : 0 )?></td>
			<td><?=(isset($r[11]) ? $r[11] : 0 )?></td>
			<td><?=(isset($r[12]) ? $r[12] : 0 )?></td>
			<td><?=(isset($r[13]) ? $r[13] : 0 )?></td>
			<td><?=(isset($r[14]) ? $r[14] : 0 )?></td>
			<td><?=(isset($r[15]) ? $r[15] : 0 )?></td>
			<td><?=(isset($r[16]) ? $r[16] : 0 )?></td>
			<td><?=(isset($r[999]) ? $r[999] : 0 )?></td>
			<td><?=($rowTotals)?></td>
		</tr>
	<?php endforeach; ?>
	<tr style='text-align: center; font-weight: bold; border-top: 1px solid #ccc'>
		<td colspan=2 style='text-align: right;'>Time of day totals:</th>
		<td><?=(isset($totals[-1]) ? $totals[-1] : 0 )?></td>
		<td><?=(isset($totals[7]) ? $totals[7] : 0 )?></td>
		<td><?=(isset($totals[8]) ? $totals[8] : 0 )?></td>
		<td><?=(isset($totals[9]) ? $totals[9] : 0 )?></td>
		<td><?=(isset($totals[10]) ? $totals[10] : 0 )?></td>
		<td><?=(isset($totals[11]) ? $totals[11] : 0 )?></td>
		<td><?=(isset($totals[12]) ? $totals[12] : 0 )?></td>
		<td><?=(isset($totals[13]) ? $totals[13] : 0 )?></td>
		<td><?=(isset($totals[14]) ? $totals[14] : 0 )?></td>
		<td><?=(isset($totals[15]) ? $totals[15] : 0 )?></td>
		<td><?=(isset($totals[16]) ? $totals[16] : 0 )?></td>
		<td><?=(isset($totals[999]) ? $totals[999] : 0 )?></td>
		<td style="background: #ffc"><?=($dateTotals)?></td>
	</tr>
</table>
<?php endforeach; ?>
</table>
<?php elseif (!empty($data)): ?>
<p>No results were found for the entered filters.</p>
<p><strong>Tips:</strong> If searching by client or package name, enter four or more characters.
	<br />For client and package name you can make a search term required by adding a "+" before it, exclude it by adding a "-",
	or search a complete phrase by adding quotes "" around it. By default, offers that contain any of the search terms are returned.
</p>
<?php else: ?>
	<div class='blankExample'>
		<h1>Enter some search criteria above to search offers</h1>
		<p>This offer search report will search through all current and past offers using the search criteria entered above.</p>
		<p><strong>Tips:</strong> If searching by client or package name, enter four or more characters.
			<br />For client and package name you can make a search term required by adding a "+" before it, exclude it by adding a "-",
			or search a complete phrase by adding quotes "" around it. By default, offers that contain any of the search terms in client name or package name are returned.
			<a href="#" target="_blank">Learn more</a>
		</p>
		<?=$html->image('blank_slate_examples/reports_bids.gif')?>
	</div>
<?php endif; ?>
</div>