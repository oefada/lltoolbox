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
	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/bids/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<tr>
			<th><?=sortLink('Offer.offerId', 'Offer Id', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Client.name', 'Client Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Track.applyToMembershipBal', 'Remit Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Offer.offerTypeName', 'Offer Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('country', 'Country', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('state', 'State/Prov', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('city', 'City', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('percentMinBid', '% Min Bid', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('percentClose', '% Close', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.approvedRetailPrice', 'Retail', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.numNights', '# Room Nights', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('SchedulingInstance.endDate', 'Date Close', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', '# Bids', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('uniqueBids', 'Unique Bids', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numTickets', '# Tickets', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numTicketsCollected', '# Collected', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('moneyPotential', '$ Potential', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('moneyCollected', '$ Collected', $currentPage, $serializedFormInput, $this, $html)?></th>
		</tr>
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
		<td><?=$r['Offer']['offerId']?></td>
		<td><?=$r['Client']['name']?></td>
		<td><?
		switch($r['auction_mstr']['remitStatus']) {
            case 0:
                    echo 'Remit';
                    break;

            case 1:
                    echo 'Wholesale';
                    break;

            case 2:
                    echo 'Keep';
                    break;

            case 3:
                   	echo 'PFP';
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
		<td><?=$number->toPercentage($r[0]['percentMinBid'], 0)?></td>
		<td><?=$number->toPercentage($r[0]['percentClose'], 0)?></td>
		<td><?=$r['Package']['approvedRetailPrice']?></td>
		<td><?=$r['Package']['numNights']?></td>
		<td><?=$r['SchedulingInstance']['endDate']?></td>
		<td><?=$r[0]['numBids']?></td>
		<td><?=$r[0]['uniqueBids']?></td>
		<td><?=$r[0]['numTickets']?></td>
		<td><?=$r[0]['numTicketsCollected']?></td>
		<td><?=$number->currency($r[0]['moneyPotential'], 'USD', array('places' => 0))?></td>
		<td<?=$collectedStyle?>><?=$number->currency($r[0]['moneyCollected'], 'USD', array('places' => 0))?></td>
	</tr>
<?php endforeach; ?>
	<tr class='related'>
		<th colspan=12 rowspan=2 style="text-align:right;">Sheet Totals:</th>
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