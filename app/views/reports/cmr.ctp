<?php $this->pageTitle = "Client Management Report" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'cmr'))?>
<fieldset>
<h3 class='title'>SEARCH CLIENTS BY:</h3>
<div class="fieldRow">
	<label>Loa Balance</label>
	<?echo $form->text('condition1.field', array('value' => 'Loa.membershipBalance', 'type' => 'hidden'))?>
	<?echo $form->select('condition1.value', array('> 0' => 'Has Balance', '=0' => 'No Balance'))?>
</div>
<div class="fieldRow">
		<label>Client auction search criteria</label>
		<?echo $form->select('condition2.value', array(1 => 'Has live auctions', 'Live fixed priced offers', 'No live auctions', 'No live fixed price', 'No live offers of any kind', 'Live fixed price AND live auctions'))?>
</div>

<div class="fieldRow">
		<label>Loa Level</label>
		<?echo $form->text('condition3.field', array('value' => 'Loa.loalevelId', 'type' => 'hidden'))?>
		<?echo $form->select('condition3.value', array(1 => 'Wholesale', 2 => 'Sponsorship'))?>
</div>
<div class="fieldRow">
		<label>Site</label>
		<?echo $form->text('condition6.field', array('value' => 'LIKE=MultiSite.sites', 'type' => 'hidden'))?>
		<?echo $form->select('condition6.value', $sites, null, array(), false)?>
</div>
<div class="fieldRow">
		<label>Manager Username</label>
		<?echo $form->text('condition4.field', array('value' => 'LIKE=Client.managerUsername', 'type' => 'hidden'))?>
		<?echo $form->text('condition4.value')?>
</div>
<div class="fieldRow">
		<label>Client name</label>
		<?echo $form->select('condition5.field', array('LIKE=Client.name' => 'Client Name', 'Client.clientId' => 'Client ID'))?>
		<?echo $form->text('condition5.value')?>
</div>

<div class="controlset fieldRow" style="border: 0">
<?php
	echo $form->checkbox('paging.disablePagination');
	echo $form->label('paging.disablePagination');

	echo $form->checkbox('download.csv');
	echo $form->label('download.csv', 'Download as CSV');
?>
</div>

</fieldset>
<?php echo $form->submit('Search') ?>
</div>

<div class='index'>
<?php

if (!empty($results)): 

	$url = "/reports/cmr/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:";

	?>
	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/cmr/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		<tr>
			<th>&nbsp;</th>
			<th><?=$utilities->sortLink('MultiSite.sites', 'Sites',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Client.name', 'Client Name',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Client.managerUsername', 'Manager',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.loaTypeId', 'LOA Type',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.startDate', 'LOA Term Start',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.endDate', 'LOA Term End',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.membershipFee', 'Membership Fee',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.membershipBalance', 'Balance',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.upgraded', 'Upgraded',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.loaNumberPackages', 'Remit Packages',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.loaNumberPackages', 'Remit Packages Sold Current',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('Loa.numberPackagesRemaining', 'Remit Packages Left',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('loaNumberOfDaysActive', 'Number of Days Active',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('dailyMembershipFee', 'Daily Membership Rate',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('numDaysPaid', '# Days Paid',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('paidThru', 'Paid Thru',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('daysBehindSchedule', 'Days Behind Schedule',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('city', 'City',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('state', 'State',  $this, $html,$url)?></th>
			<th><?=$utilities->sortLink('country', 'Country',  $this, $html,$url)?></th>
		</tr>
		</thead>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$k+1?></td>
		<td><?=str_replace(",", ", ", $r['MultiSite']['sites'])?></td>
		<td><?=$html->link($r['Client']['name'].', '.$r['Client']['clientId'], array('controller' => 'clients', 'action' => 'edit', $r['Client']['clientId']), array('target' => '_blank'))?></td>
		<td><?=$r['Client']['managerUsername']?></td>
		<td><?=$html->link($r['Loa']['loaId'], '/clients/'.$r['Client']['clientId'].'/loas/edit/'.$r['Loa']['loaId'], array('target' => '_blank'))?>, <?=$r['LoaLevel']['loaLevelName']?></td>
		<td><?=$r['Loa']['startDate']?></td>
		<td><?=$r['Loa']['endDate']?></td>
		<td><?=$r['Loa']['membershipFee']?></td>
		<td><?=$r['Loa']['membershipBalance']?></td>
		<td><?=$html->image($r['Loa']['upgraded'] ? 'tick.png' : 'cross.png')?></td>
		<td><?=$r['Loa']['loaNumberPackages']?></td>
		<td><?=$r['Loa']['loaNumberPackages']-$r['Loa']['numberPackagesRemaining']?></td>
		<td><?=$r['Loa']['numberPackagesRemaining']?></td>
		<td><?=$r[0]['loaNumberOfDaysActive']?></td>
		<td><?=$r[0]['dailyMembershipFee']?></td>
		<td><?=$r[0]['numDaysPaid']?></td>
		<td><?=$r[0]['paidThru']?></td>
		<td><?=$r[0]['daysBehindSchedule']?></td>
		<td><?=$r[0]['city']?></td>
		<td><?=$r[0]['state']?></td>
		<td><?=$r[0]['country']?></td>	
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/cmr/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
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
