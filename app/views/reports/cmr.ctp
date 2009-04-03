<?php $this->pageTitle = "Client Management Report" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'cmr'))?>
<fieldset>
<h3 class='title'>SEARCH CLIENTS BY:</h3>
<div class="fieldRow">
	<label>Loa Balance</label>
	<?echo $form->text('condition1.field', array('value' => 'Loa.membershipBalance', 'type' => 'hidden'))?>
	<?echo $form->select('condition1.value', array('!= 0' => 'Has Balance', '=0' => 'No Balance'))?>
</div>
<div class="fieldRow">
		<label>Client auction search criteria</label>
		<?echo $form->select('condition2.value', array(1 => 'Has live auctions', 'Live fixed priced offers', 'All', 'No live offers'))?>
</div>
<div class="fieldRow">
		<label>Loa Level</label>
		<?echo $form->text('condition3.field', array('value' => 'Loa.loalevelId', 'type' => 'hidden'))?>
		<?echo $form->select('condition3.value', array(1 => 'Wholesale', 2 => 'Sponsorship'))?>
</div>
<div class="fieldRow">
		<label>Manager Username</label>
		<?echo $form->text('condition4.field', array('value' => 'LIKE=Client.managerUsername', 'type' => 'hidden'))?>
		<?echo $form->text('condition4.value')?>
</div>
<div class="fieldRow">
		<label>Client name</label>
		<?echo $form->text('condition5.field', array('value' => 'LIKE=Client.name', 'type' => 'hidden'))?>
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
//TODO: put this in a helper
function sortLink($field, $title, $currentPage, $serializedFormInput, $view, $html) {
	$url = "/reports/cmr/filter:";
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
	<?=$pagination->Paginate("/reports/cmr/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		<tr>
			<th>&nbsp;</th>
			<th><?=sortLink('Client.name', 'Client Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.loaTypeId', 'LOA Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.endDate', 'LOA Term End', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Remit Packages', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Remit Packages Sold Current', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Remit Packages Left', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.upgraded', 'Upgraded', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Remit Packages Sold Current($)', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('city', 'City', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('state', 'State', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('country', 'Country', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.membershipBalance', 'Balance', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.membershipFee', 'Membership Fee', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.startDate', 'LOA Term Start', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('loaNumberOfDaysActive', 'Number of Days Active', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('dailyMembershipFee', 'Daily Membership Rate', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numDaysPaid', '# Days Paid', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('paidThru', 'Paid Thru', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('daysBehindSchedule', 'Days Behind Schedule', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Client.managerUsername', 'Manager', $currentPage, $serializedFormInput, $this, $html)?></th>
		</tr>
		</thead>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$k+1?></td>
		<td><?=$html->link($r['Client']['name'].', '.$r['Client']['clientId'], array('controller' => 'clients', 'action' => 'edit', $r['Client']['clientId']))?></td>
		<td><?=$html->link($r['Loa']['loaId'], '/clients/'.$r['Client']['clientId'].'/loas/edit/'.$r['Loa']['loaId'])?>, <?=$r['LoaLevel']['loaLevelName']?></td>
		<td><?=$r['Loa']['endDate']?></td>
		<td>?</td>
		<td>?</td>
		<td>?</td>
		<td><?=$html->image($r['Loa']['upgraded'] ? 'tick.png' : 'cross.png')?></td>
		<td>?</td>
		<td><?=$r[0]['city']?></td>
		<td><?=$r[0]['state']?></td>
		<td><?=$r[0]['country']?></td>
		<td><?=$r['Loa']['membershipBalance']?></td>
		<td><?=$r['Loa']['membershipFee']?></td>
		<td><?=$r['Loa']['startDate']?></td>
		<td><?=$r[0]['loaNumberOfDaysActive']?></td>
		<td><?=$r[0]['dailyMembershipFee']?></td>
		<td><?=$r[0]['numDaysPaid']?></td>
		<td><?=$r[0]['paidThru']?></td>
		<td><?=$r[0]['daysBehindSchedule']?></td>
		<td><?=$r['Client']['managerUsername']?></td>
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