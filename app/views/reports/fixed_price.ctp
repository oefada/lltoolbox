<?php $this->pageTitle = "Fixed Price Search" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'fixed_price'))?>
<fieldset>
<h3 class='title'>SEARCH FIXED PRICE REQUESTS BY:</h3>

<div style="float: left; ">

<div class="fieldRow">
<label>Date Closed</label>
<?echo $form->text('condition1.field', array('value' => 'Ticket.created', 'type' => 'hidden'))?>
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
<?echo $form->text('condition2.field', array('value' => 'Track.expirationCriteriaId', 'type' => 'hidden'))?>
<div class="range">
	<?php
		echo $form->select('condition2.value', array('keep' => 'Keep', 'remit' => 'Remit'), null, array('multiple' => 'checkbox'))
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
if (!empty($results) && isset($serializedFormInput)): 

//function $utilities->sortLink($field, $title, $view, $html,$url) {
	$url = "/reports/fixed_price/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:";

/*	if (isset($view->params['named']['sortBy']) && $view->params['named']['sortBy'] == $field) {
		$dir = ($view->params['named']['sortDirection'] == 'ASC') ? 'DESC' : 'ASC';
	} elseif(isset($view->params['named']['sortBy'])  && $view->params['named']['sortBy'] == $field) {
		$dir = 'DESC';
	} else {
		$dir = 'ASC';
	}
*/	
	//$url .= "/sortDirection:$dir";
	
	//return $html->link($title, $url);
//}

?>
	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/fixed_price/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<tr>
			<th><?=$utilities->sortLink('Ticket.siteId', 'Transaction Site', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Offer.offerId', 'Offer ID', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Client.name', 'Ticket ID', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Track.applyToMembershipBal', 'Client Name', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Offer.offerTypeName', 'Public User', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('country', 'Remit Type', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('state', 'Offer Type', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('city', 'Country', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('percentMinBid', 'State/Prov', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('percentClose', 'City', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Package.approvedRetailPrice', 'Request Date', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Package.numNights', 'Collection Date', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('SchedulingInstance.endDate', '$ Potential', $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('numBids', '$ Collected', $this, $html,$url)?></th>
		</tr>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?= $siteIds[$r['Ticket']['siteId']]?></td>
		<td><?=$r['Offer']['offerId']?></td>
		<td><a href="/tickets/view/<?=$r['Ticket']['ticketId']?>" target="_BLANK" /><?=$r['Ticket']['ticketId']?></a></td>
		<td><?=$r[0]['clientNames']?></td>
		<td><?=$r['Ticket']['userFirstName'].' '.$r['Ticket']['userLastName']?></td>
		<td><?
		switch($r['Track']['expirationCriteriaId']) {
            case 1:
			case 4:
                    echo 'Keep';
                    break;

            case 2:
                    echo 'Remit';
                    break;

            case 3:
                    echo 'Commission/Upgrade';
                    break;
			default:
					echo '';
					break;
		}
		?></td>
		<td><?=$r['OfferType']['offerTypeName']?></td>
		<td><?=$r['Ticket']['userCountry']?></td>
		<td><?=$r['Ticket']['userState']?></td>
		<td><?=$r['Ticket']['userCity']?></td>
		<td><?=$r['Ticket']['created']?></td>
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
