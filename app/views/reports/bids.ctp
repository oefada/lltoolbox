<?php $this->pageTitle = "Bid Search" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'bids'))?>
<fieldset>
<h3 class='title'>SEARCH BIDS BY:</h3>

<div style="float: left; ">

<div class="fieldRow">
<label>Date Closed</label>
<?echo $form->text('condition1.field', array('value' => 'SchedulingInstance.endDate', 'type' => 'hidden'))?>
<div class="range">
	<?echo $datePicker->picker('condition1.value.between.0', array('label' => 'From'))?>
	<?echo $datePicker->picker('condition1.value.between.1', array('label' => 'To'))?>
	<a href="#" onclick='javascript: $(&quot;condition1valueBetween0&quot;).value = &quot;<?=date('Y-m-d')?>&quot;; $(&quot;condition1valueBetween1&quot;).value = &quot;<?=date('Y-m-d', strtotime('+1 day'))?>&quot;'>Today</a> | 
	<a href="#" onclick='javascript: $(&quot;condition1valueBetween0&quot;).value = &quot;<?=date('Y-m-d', strtotime('-1 day'))?>&quot;; $(&quot;condition1valueBetween1&quot;).value = &quot;<?=date('Y-m-d')?>&quot;'>Yesterday</a> |
	<a href="#" onclick='javascript: $(&quot;condition1valueBetween0&quot;).value = &quot;<?=date('Y-m-d', strtotime('-1 week'))?>&quot;; $(&quot;condition1valueBetween1&quot;).value = &quot;<?=date('Y-m-d')?>&quot;'>This Week</a>
</div>
</div>

<div class="fieldRow" style="float: left; margin-right: 30px">
<label>Remit Type</label>
<?echo $form->text('condition2.field', array('value' => 'auction_mstr.auction_wholesale', 'type' => 'hidden'))?>
<div class="range">
	<?php
		echo $form->select('condition2.value', array(2 => 'Keep', 0 => 'Remit'), null, array('multiple' => 'checkbox'))
	?>
</div>
</div>

<div class="fieldRow" style="float: left; clear: none">
<label>Auction Type</label>
<?echo $form->text('condition3.field', array('value' => 'SchedulingMaster.offerTypeId', 'type' => 'hidden'))?>
<div class="range">
	<?php
		echo $form->select('condition3.value', array(1 => 'Standard', 2 => 'Best Shot', 6 => 'Dutch'), null, array('multiple' => 'checkbox'))?>
</div>
</div>
<div class="controlset fieldRow" style="border: 0">
<?php 		echo $form->checkbox('paging.disablePagination');
			echo $form->label('paging.disablePagination');
			
			echo $form->checkbox('download.csv');
			echo $form->label('download.csv', 'Download as CSV');
?>
</div>
</div>
</fieldset>

<?php echo $form->submit('Search') ?>
</div>

<div class='index'>
<?php

if (!empty($results)): 

	$url = "/reports/bids/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:";

	?>

	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/bids/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		<tr>
			<th><?=$utilities->sortLink('Ticket.siteId', 'Site', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Offer.offerId', 'Offer Id', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Client.name', 'Client Name', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Track.applyToMembershipBal', 'Remit Type', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Offer.offerTypeName', 'Offer Type', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('country', 'Country', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('state', 'State/Prov', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('city', 'City', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('percentMinBid', '% Min Bid', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('percentClose', '% Close', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Package.approvedRetailPrice', 'Retail', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Package.numNights', '# Room Nights', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('SchedulingInstance.endDate', 'Date Close', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('numBids', '# Bids', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('uniqueBids', 'Unique Bids', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('numTickets', '# Tickets', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('numTicketsCollected', '# Collected', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('moneyPotential', '$ Potential', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('moneyCollected', '$ Collected', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('ticketIds', 'Ticket Ids', $this, $html,$url)?></th>
		</tr>
		</thead>
<?php
$bids = 0;
$uniqueBids = 0;
$tickets = 0;
$numTicketsCollected = 0;
$moneyPotential = 0;
$moneyCollected = 0;
foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';

$collectedStyle = '';
if (0 != $r[0]['numTickets'] && 1 !== $r[0]['numTicketsCollected']/$r[0]['numTickets']) {
	$collectedStyle = ' style="background: #c00; color: #fff"';
}
$bids += $r[0]['numBids'];
$uniqueBids += $r[0]['uniqueBids'];
$tickets += $r[0]['numTickets'];
$numTicketsCollected += $r[0]['numTicketsCollected'];
$moneyPotential += $r[0]['moneyPotential'];
$moneyCollected += $r[0]['moneyCollected'];
?>
	<tr<?=$class?>>
		<td><?=$siteIds[$r['SchedulingMaster']['siteId']]?></td>
		<td><?=$r['Offer']['offerId']?></td>
		<td><?=$r[0]['clientNames']?></td>
		<td><?
		switch($r['Track']['expirationCriteriaId']) {
            case 1:
            case 4:
                    echo 'Keep';
                    break;
            case 2:
            case 3:
                   	echo 'Remit';
                    break;
			default:
					echo '';
					break;
		}
		?></td>
		<td><?=$r['OfferType']['offerTypeName']?></td>
		<td><?=$r[0]['country']?></td>
		<td><?=$r[0]['state']?></td>
		<td><?=$r[0]['city']?></td>
        <?php switch ($r['SchedulingMaster']['siteId']) {
                case 2: ?>
                    <td><?=$number->toPercentage($r[0]['familyPercentMinBid'], 0)?></td>
                    <td><?=$number->toPercentage($r[0]['familyPercentClose'], 0)?></td>
                    <td><?=$r['OfferFamily']['familyRetailValue']?></td>
                    <td><?=$r['OfferFamily']['familyRoomNights']?></td>
                    <?php break;
                case 1:
                default: ?>
                    <td><?=$number->toPercentage($r[0]['llPercentMinBid'], 0)?></td>
                    <td><?=$number->toPercentage($r[0]['llPercentClose'], 0)?></td>
                    <td><?=$r['OfferLuxuryLink']['llRetailValue']?></td>
                    <td><?=$r['OfferLuxuryLink']['llRoomNights']?></td>
                    <?php break;
        } ?>
		<td><?=$r['SchedulingInstance']['endDate']?></td>
		<td><?=$r[0]['numBids']?></td>
		<td><?=$r[0]['uniqueBids']?></td>
		<td><?=$r[0]['numTickets']?></td>
		<td><?=$r[0]['numTicketsCollected']?></td>
		<td><?=$number->currency($r[0]['moneyPotential'], 'USD', array('places' => 0))?></td>
		<td<?=$collectedStyle?>><?=$number->currency($r[0]['moneyCollected'], 'USD', array('places' => 0))?></td>
		<td><?php foreach(explode(',', $r[0]['ticketIds']) as $ticketId) { echo "<a href='/tickets/view/$ticketId' target='_BLANK'>$ticketId</a><br />";  }  ?></td>
	</tr>
<?php endforeach; ?>
	<tr class='related'>
		<th colspan=13 rowspan=2 style="text-align:right;">Sheet Totals:</th>
		<th># Of Bids</td>
		<th># Of Unique Bids</td>
		<th># Of Tickets</td>
		<th># Of Tickets Collected</td>
		<th>$ Potential</td>
		<th>$ Collected</td>
	</tr>
	<tr>
		<td><?=$bids?></td>
		<td><?=$uniqueBids?></td>
		<td><?=$tickets?></td>
		<td><?=$numTicketsCollected?></td>
		<td><?=$number->currency($moneyPotential, 'USD', array('places' => 0))?></td>
		<td><?=$number->currency($moneyCollected, 'USD', array('places' => 0))?></td>
	</tr>
</table>
<?=$pagination->Paginate("/reports/bids/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
<?php elseif (!empty($data)): ?>
<p>No results were found for the entered filters.</p>
<p><strong>Tips:</strong> If searching by client or package name, enter four or more characters.
	<br />For client and package name you can make a search term required by adding a &quot;+&quot; before it, exclude it by adding a &quot;-&quot;,
	or search a complete phrase by adding quotes &quot;&quot; around it. By default, offers that contain any of the search terms are returned.
</p>
<?php else: ?>
	<div class='blankExample'>
		<h1>Enter some search criteria above to search offers</h1>
		<p>This offer search report will search through all current and past offers using the search criteria entered above.</p>
		<p><strong>Tips:</strong> If searching by client or package name, enter four or more characters.
			<br />For client and package name you can make a search term required by adding a &quot;+&quot; before it, exclude it by adding a &quot;-&quot;,
			or search a complete phrase by adding quotes &quot;&quot; around it. By default, offers that contain any of the search terms in client name or package name are returned.
			<a href="#" target="_blank">Learn more</a>
		</p>
		<?=$html->image('blank_slate_examples/reports_bids.gif')?>
	</div>
<?php endif; ?>
</div>
