<?php $this->pageTitle = "Check-in Date (Confirmed Reservation Arrival)" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'check_in_date'))?>
<fieldset>
<h3 class='title'>SEARCH CONFIRMED RESERVATION ARRIVAL DATE BY:</h3>

<div style="float: left; ">

<div class="fieldRow">
<label>Date Closed</label>
<?echo $form->text('condition1.field', array('value' => 'Reservation.arrivalDate', 'type' => 'hidden'))?>
<div class="range">
	<?echo $datePicker->picker('condition1.value.between.0', array('label' => 'From'))?>
	<?echo $datePicker->picker('condition1.value.between.1', array('label' => 'To'))?>
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d')?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d', strtotime('+1 day'))?>"'>Today</a> | 
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 day'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>Yesterday</a> |
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 week'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>This Week</a>
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

Client ID <input type="text" name="data[Client][clientId]" /><br /><br />
<?php echo $form->submit('Search') ?>
</div>

<div class='index'>
<?php

if (!empty($results) && isset($serializedFormInput)):

	$url = "/reports/check_in_date/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:";


?>
	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/check_in_date/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		<tr>
			<th><?=$utilities->sortLink('Ticket.siteId', 'Site',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('clientNames', 'Client Name',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('clientIds', 'Client ID',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Reservation.ticketId', 'Ticket ID',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Ticket.userFirstName', 'First Name',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Ticket.userLastName', 'Last Name',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('UserSiteExtended.username', 'Username',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Reservation.arrivalDate', 'Arrival Date',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Reservation.departureDate', 'Departure Date',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Reservation.reservationConfirmNum', 'Confirmation #',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Ticket.billingPrice', 'Ticket Price',  $this, $html,$url)?></th>
		</tr>
		</thead>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$siteIds[$r['Ticket']['siteId']]?></td>
		<td><?=$r[0]['clientNames']?></td>
		<td><?=$r[0]['clientIds']?></td>
		<td><?=$r['Reservation']['ticketId']?></td>
		<td><?=$r['Ticket']['userFirstName']?></td>
		<td><?=$r['Ticket']['userLastName']?></td>
		<td><?=$r['UserSiteExtended']['username']?></td>
		<td><strong><?=$r['Reservation']['arrivalDate']?></strong></td>
		<td><?=$r['Reservation']['departureDate']?></td>
		<td><?=$r['Reservation']['reservationConfirmNum']?></td>
		<td><?=$r['Ticket']['billingPrice']?></td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/check_in_date/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
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
