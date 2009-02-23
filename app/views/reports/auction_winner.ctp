<?php $this->pageTitle = "Daily Auction Payment (Auction Winner)" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'auction_winner'))?>
<fieldset>
<h3 class='title'>SEARCH AUCTION WINNERS BY:</h3>

<div style="float: left; ">

<div class="fieldRow">
<label>Date Closed</label>
<?echo $form->text('condition1.field', array('value' => 'SchedulingInstance.endDate', 'type' => 'hidden'))?>
<div class="range">
	<?echo $datePicker->picker('condition1.value.between.0', array('label' => 'From'))?>
	<?echo $datePicker->picker('condition1.value.between.1', array('label' => 'To'))?>
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d')?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d', strtotime('+1 day'))?>"'>Today</a> | 
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 day'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>Yesterday</a> |
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 week'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>This Week</a>
</div>
</div>

<div class="controlset fieldRow" style="float: left; clear: none">
	<?php echo $form->label('Ticket Status')?>
	<?echo $form->text('condition2.field', array('value' => 'PaymentDetail.paymentDetailId', 'type' => 'hidden'))?>
	<?echo $form->text('condition2.explicit', array('value' => 'true', 'type' => 'hidden'))?>
	<?php echo $form->select('condition2.value', array('IS NOT NULL' => 'Payment Processed')).'(leave blank for All)';?>
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
	$url = "/reports/auction_winner/filter:";
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
	<?=$pagination->Paginate("/reports/auction_winner/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<tr>
			<th><?=sortLink('Offer.offerId', 'Booking Date', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Client.name', 'Payment Date', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Track.applyToMembershipBal', 'Ticket ID', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Offer.offerTypeName', 'Vendor ID', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('country', 'Vendor', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('state', 'Guest First Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('city', 'Guest Last Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('percentMinBid', 'Address1', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('percentClose', 'Address2', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.approvedRetailPrice', 'City', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.numNights', 'State', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('SchedulingInstance.endDate', 'Zip', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Country', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Phone', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Email', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'CC Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'CC Number', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'CC Exp', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Revenue', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Room Nights', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Auction Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Handling Fee', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Percent', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Remit Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Validity Start Date', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numBids', 'Validity End Date', $currentPage, $serializedFormInput, $this, $html)?></th>
		</tr>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$r['SchedulingInstance']['endDate']?></td>
		<td><?=$r['PaymentDetail']['ppResponseDate']?></td>
		<td><?=$r['Ticket']['ticketId']?></td>
		<td><?=$r['Client']['clientId']?></td>
		<td><?=$r['Client']['name']?></td>
		<td><?=$r['Ticket']['userFirstName']?></td>
		<td><?=$r['Ticket']['userLastName']?></td>
		<td><?=$r['Ticket']['userAddress1']?></td>
		<td><?=$r['Ticket']['userAddress2']?></td>
		<td><?=$r['Ticket']['userCity']?></td>
		<td><?=$r['Ticket']['userState']?></td>
		<td><?=$r['Ticket']['userZip']?></td>
		<td><?=$r['Ticket']['userCountry']?></td>
		<td><?=$r['Ticket']['userHomePhone']?></td>
		<td><?=$r['Ticket']['userEmail1']?></td>
		<td><?=$r['UserPaymentSetting']['ccType']?></td>
		<td><?=$r['PaymentDetail']['ppCardNumLastFour']?></td>
		<td><?=$r['PaymentDetail']['ppExpMonth'].'/'.$r['PaymentDetail']['ppExpYear']?></td>
		<td><?=$r[0]['revenue']?></td>
		<td><?=$r['Package']['numNights']?></td>
		<td><?=$r['OfferType']['offerTypeName']?></td>
		<td><?switch($r['OfferType']['offerTypeName']) {
			case 'Standard Auction':
			case 'Dutch Auction':
			case 'Best Shot':
				echo '$30';
				break;
			case 'Best Buy':
			case 'Exclusive':
				echo '$40';
				break;
		}?></td>
		<td><?=$r[0]['percentOfRetail']?></td>
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
					echo '';
					break;
		}
		?>
		</td>
		<td><?=$r['Package']['validityStartDate']?></td>
		<td><?=$r['Package']['validityEndDate']?></td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/auction_winner/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
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