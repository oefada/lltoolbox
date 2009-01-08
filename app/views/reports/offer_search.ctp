<?php $this->pageTitle = "Offer Search" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'offer_search'))?>
<fieldset>
<h3 class='title'>SEARCH OFFERS BY:</h3>

<div style="float: left; border-right: 1px solid #000; padding-right: 25px">
	<div class="fieldRow">
<?echo $form->select('condition1.field', $condition1Options)?>
<?echo $form->text('condition1.value', array('style' => 'width: 250px'))?>
	</div>
	<div class="fieldRow">
<label>Retail Price</label>
<?echo $form->text('condition2.field', array('value' => 'Package.approvedRetailPrice', 'type' => 'hidden'))?>
<div class="range">
<label>From</label>
<?echo $form->text('condition2.value.between.0')?>
<label>To</label>
<?echo $form->text('condition2.value.between.1')?>
</div>
</div>
<div class="fieldRow lastFieldRow">
<?echo $form->select('condition3.field', $condition3Options)?>
<div class='range'>

<?echo $datePicker->picker('condition3.value.between.0', array('label' => 'From'))?>
<?echo $datePicker->picker('condition3.value.between.1', array('label' => 'To'))?>
</div>
</div>
</div>

<div style="float: left; clear: none; padding-left: 25px">
	<div class="fieldRow">
	<label>Offer Type</label>
	<?php echo $form->text('condition4.field', array('value' => 'SchedulingMaster.offerTypeId', 'type' => 'hidden'))?>
	<?php echo $form->select('condition4.value', $condition4Options)?>
	</div>
	
	<div class="fieldRow">
	<label># of Room Nights</label>
	<?php echo $form->text('condition5.field', array('value' => 'Package.numNights', 'type' => 'hidden'))?>
	<?php echo $form->text('condition5.value')?>
	</div>
	
	<div class="fieldRow">
	<label>Loa Track</label>
	<?php echo $form->text('condition6.field', array('value' => 'ClientLoaPackageRel.trackId', 'type' => 'hidden'))?>
	<?php echo $form->text('condition6.value')?>
	</div>
</div>
</fieldset>
<?php echo $form->submit('Search') ?>
</div>

<?php if (!empty($results)): ?>
	<table style="margin-top: 20px">
		<tr>
			<th>Client Name</th>
			<th>Offer Type</th>
			<th>Offer Name</th>
			<th>Room Nights</th>
			<th>Status</th>
			<th>Date Opened/Closed</th>
			<th># Bids</th>
			<th>Retail Value</th>
			<th>Opening Bid Amount</th>
			<th>LOA Term End</th>
			<th>LOA Track End</th>
			<th>LOA Balance</th>
			<th>Will Repeat</th>
		</tr>
<?php foreach ($results as $k => $result):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>><td><?=$result['Client']['name']?></td>
		<td><?=$result['OfferType']['offerTypeName']?></td>
		<td><?=$result['Package']['packageName']?></td>
		<td><?=$result['Package']['numNights']?></td>
		<td><?php echo ($result[0]['offerStatus']) ? 'Open' : 'Closed' ?></td>
		<td><?=$result[0]['dateOpenedOrClosed']?></td>
		<td><?=$result[0]['numberOfBids']?></td>
		<td><?=$result['Package']['approvedRetailPrice']?></td>
		<td><?=$result['SchedulingMaster']['openingBid']?></td>
		<td><?=$result['Loa']['endDate']?></td>
		<td>?</td>
		<td><?=$result['Loa']['membershipBalance']?></td>
		<td>Y/N</td>
<?php endforeach; ?>
</table>
<?php endif; ?>