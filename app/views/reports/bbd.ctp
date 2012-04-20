<?php $this->pageTitle = "Bookings By Destination" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'bbd'))?>
<fieldset>

<div style="float: left; ">

<div class="fieldRow">
<?echo $form->text('condition1.field', array('value' => 'Ticket.created', 'type' => 'hidden'))?>
<div class="range">
	<?echo $datePicker->picker('condition1.value.between.0', array('label' => 'From'))?>
	<?echo $datePicker->picker('condition1.value.between.1', array('label' => 'To'))?>
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d')?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d', strtotime('+1 day'))?>"'>Today</a> | 
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 day'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>Yesterday</a> |
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 week'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>This Week</a>
</div>
</div>

<div class="controlset fieldRow" style="border: 0">
<?php 		
			// echo $form->checkbox('download.csv');
			// echo $form->label('download.csv', 'Download as CSV');
			?>
</div>

</div>
</fieldset>
<?php echo $form->submit('Search') ?>
</div>

<div class='index'>
<?php

if (!empty($results) && isset($serializedFormInput)): 

	$url = "/reports/auction_winner/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:";//$field";

	?>
	<table style="margin-top: 20px">
<?php foreach ($results as $k => $r): ?>

	<? $rowpad = 100 * $r['level']; ?>

	<tr style="border-top: 1px solid #ddd;">
		<td style="padding-left:<?= $rowpad; ?>px;"><?=$r['destinationName']; ?></td>
		<td align="right">$<?= number_format($r['bookingsTotal'], 0); ?></td>
	</tr>
	<? foreach ($r['locations'] as $k=>$v) { ?>
		<tr style="background-color: #eee; border-top: 1px solid #ddd;">
			<td style="padding-left:<?= $rowpad+30; ?>px;"><?=$k; ?></td>
			<td align="right">$<?= number_format($v, 0); ?></td>
		</tr>
	<? } ?>
<?php endforeach; //TODO: add totals ?>
</table>


<?php elseif (!empty($data)): ?>
<p>No results were found for the entered filters.</p>
<?php else: ?>
	<div class='blankExample'>
		<h1>Enter some search criteria above to search offers</h1>
		<?=$html->image('blank_slate_examples/reports_bids.gif')?>
	</div>
<?php endif; ?>
</div>
