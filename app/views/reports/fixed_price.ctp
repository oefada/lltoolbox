<?php $this->pageTitle = "Fixed Price Search" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'fixed_price'))?>
<fieldset>
<h3 class='title'>SEARCH FIXED PRICE REQUESTS BY:</h3>

<div style="float: left; ">

<div class="fieldRow">
<label>Date Closed</label>
<?echo $form->text('condition1.field', array('value' => 'Ticket.requestQueueDatetime', 'type' => 'hidden'))?>
<div class="range">
	<?echo $datePicker->picker('condition1.value.between.0', array('label' => 'From'))?>
	<?echo $datePicker->picker('condition1.value.between.1', array('label' => 'To'))?>
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d')?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d', strtotime('+1 day'))?>"'>Today</a> | 
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 day'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>Yesterday</a> |
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 week'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>This Week</a>
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
<label>Fixed Price Type</label>
<?echo $form->text('condition3.field', array('value' => 'Ticket.offerTypeId', 'type' => 'hidden'))?>
<div class="range">
	<?php
		echo $form->select('condition3.value', array(3 => 'Exclusive', 4 => 'Best Buy'), null, array('multiple' => 'checkbox'))?>
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
	$url = "/reports/fixed_price/filter:";
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
	<?=$pagination->Paginate("/reports/fixed_price/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<tr>
			<th><?=sortLink('Offer.offerId', 'Offer ID', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Client.name', 'Ticket ID', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Track.applyToMembershipBal', 'Client Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Offer.offerTypeName', 'Public User', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('country', 'Remit Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('state', 'Offer Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('city', 'Country', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('percentMinBid', 'State/Prov', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('percentClose', 'City', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.approvedRetailPrice', 'Request Date', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.numNights', 'Collection Date', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('SchedulingInstance.endDate', '$ Potential', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', '$ Collected', $currentPage, $serializedFormInput, $this, $html)?></th>
		</tr>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$r['Offer']['offerId']?></td>
		<td><?=$r['Ticket']['ticketId']?></td>
		<td><?=$r['Client']['name']?></td>
		<td><?=$r['Ticket']['userFirstName'].' '.$r['Ticket']['userLastName']?></td>
		<td><?
		switch($r[0]['remitStatus']) {
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
					echo 'Remit';
					break;
		}
		?></td>
		<td><?=$r['OfferType']['offerTypeName']?></td>
		<td><?=$r['Ticket']['userCountry']?></td>
		<td><?=$r['Ticket']['userState']?></td>
		<td><?=$r['Ticket']['userCity']?></td>
		<td><?=$r['Ticket']['requestQueueDateTime']?></td>
		<td><?=$r[0]['dateCollected']?></td>
		<td><?=$r['Ticket']['billingPrice']?></td>
		<td><?=$r[0]['moneyCollected']?></td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/fixed_price/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
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