<?php $this->pageTitle = "Inventory Management Report" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'imr'))?>
<fieldset>
<h3 class='title'>SEARCH IMR BY:</h3>
<div class="fieldRow">
	<label>Live During</label>
<?echo $form->text('condition1.field', array('value' => 'liveDuring', 'type' => 'hidden'))?>
<div class="range">
	<?echo $datePicker->picker('condition1.value.between.0', array('label' => 'From'))?>
	<?echo $datePicker->picker('condition1.value.between.1', array('label' => 'To'))?>
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d')?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>Today</a> | 
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 day'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d', strtotime('-1 day'))?>"'>Yesterday</a> |
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 week'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d')?>"'>Last Week</a> |
	<a href="#" onclick='javascript: $("condition1valueBetween0").value = "<?=date('Y-m-d', strtotime('1 month'))?>"; $("condition1valueBetween1").value = "<?=date('Y-m-d', strtotime('2 month'))?>"'>Next Month</a>
</div>
</div>
<div class="fieldRow">
	<label>Validity End</label>
<?echo $form->text('condition7.field', array('value' => 'validityEndDate', 'type' => 'hidden'))?>
<div class="range">
	<?echo $datePicker->picker('condition7.value.between.0', array('label' => 'From'))?>
	<?echo $datePicker->picker('condition7.value.between.1', array('label' => 'To'))?>
</div>
</div>
<div class="fieldRow controlset" style="float: left; margin-right: 30px">
<label>LOA Track Type</label>
<?php echo $form->text('condition2.field', array('value' => 'auction_mstr.auction_wholesale', 'type' => 'hidden'))?>
<?php echo $form->select('condition2.value', array('keep' => 'Keep', 2 => 'Commission-free', 3 => 'Commision/Upgrade'), null, array('multiple' => 'checkbox'))?>
</div>

<div style="float: left; border-right: 1px solid #000; padding-right: 25px">
	<div class="fieldRow">
			<?echo $form->select('condition2.field', array('MATCH=managerUsername' => 'Manager Username', 'MATCH=teamName' => 'Team Name', 'LIKE=name' => 'Client Name', 'clientId' => 'Client Id'))?>
			<?echo $form->text('condition2.value')?>
	</div>


	<div class="fieldRow">
			<?echo $form->label('Country')?>
			<?echo $form->text('condition4.field', array('value' => 'countryId', 'type' => 'hidden'))?>
			<?echo $form->select('condition4.value', $countries)?>
	</div>
	<div class="fieldRow">
			<?echo $form->label('State/Province')?>
			<?echo $form->text('condition5.field', array('value' => 'stateId', 'type' => 'hidden'))?>
			<?echo $form->select('condition5.value', $states)?>
	</div>

</div>

<div style="float: left; clear: none; padding-left: 25px">
	<div class="fieldRow">
			<?echo $form->label('Status')?>
			<?echo $form->text('condition3.field', array('value' => 'status', 'type' => 'hidden'))?>
			<?echo $form->select('condition3.value', array('Live' => 'Live', 'Ended' => 'Not Live', 'Scheduled' => 'Scheduled'), null, array('multiple' => true))?>
	</div>
	
	<div class="fieldRow">
			<?echo $form->label('Offer Type')?>
			<?echo $form->text('condition6.field', array('value' => 'offerTypeId', 'type' => 'hidden'))?>
			<?echo $form->select('condition6.value', $offerTypeIds, null, array('multiple' => true))?>
	</div>
</div>

<div class="controlset fieldRow" style="border: 0">
<?php 		echo $form->checkbox('paging.disablePagination');
			echo $form->label('paging.disablePagination');?>
</div>
</fieldset>
<?php echo $form->submit('Search') ?>
</div>

<div class='index'>
<?php
//TODO: put this in a helper
function sortLink($field, $title, $currentPage, $serializedFormInput, $view, $html) {
	$url = "/reports/imr/filter:";
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
	<?=$pagination->Paginate("/reports/imr/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		<tr>
			<th>&nbsp;</th>
			<th><?=sortLink('Client.name', 'Client Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Manager', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Package Title', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Offer Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Room Nights', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Starting Bid', $currentPage, $serializedFormInput, $this, $html)?></th>
			
			<th><?=sortLink('a', 'Starting Bid - % retail', $currentPage, $serializedFormInput, $this, $html)?></th>
			
			<th><?=sortLink('a', 'Retail', $currentPage, $serializedFormInput, $this, $html)?></th>
			
			<th><?=sortLink('a', 'Status', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Validity End', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Bid History', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Open Date', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Close Date', $currentPage, $serializedFormInput, $this, $html)?></th>
			
			
			<th><?=sortLink('a', 'Package Notes', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'City', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'State', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('a', 'Country', $currentPage, $serializedFormInput, $this, $html)?></th>
			
		</tr>
		</thead>
<?php foreach ($results as $k => $r):
$r = $r['ImrReport'];
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$k+1?></td>
		<td><?=$html->link($r['name'], '/clients/'.$r['clientId'], array('target' => '_blank'))?></td>
		
		<td><?=$r['managerUsername']?></td>
		<td><?=$html->link($r['packageName']."(".$r['packageId'].")", '/clients/'.$r['clientId'].'/packages/edit/'.$r['packageId'], array('target' => '_blank'))?></td>
		<td><?=$r['offerTypeName']?></td>
		<td><?=$r['numNights']?></td>
		<td><?=$r['openingBid']?></td>
		
		<td><?=$r['startingBidPercentOfRetail']?></td>
		
		<td><?=$r['retailValue']?></td>
		<td><?=$r['status']?></td>
		<td><?=$r['validityEndDate']?></td>
		<td><?
		
		echo preg_replace("/([0-9]+):([0-9]+)/", "<a href='/reports/offer_search/offerId:\\1'>\\2</a>", $r['bidHistory']);
		
		?></td>
		<td><?=$r['startDate']?></td>
		<td><?=$r['endDate']?></td>
		<td>
			<a href="#" id="notes-<?=$k?>" onclick="return false;">View Notes</a>
			<?php $prototip->tooltip('notes-'.$k, array('ajax' =>
		 														array('url' => '/packages/tooltipNotes/'.$r['packageId'], 
																	  'options' => array('method' => 'get')
																		),
																	'title' => 'Packages Notes'
														));?>
		</td>
		<td><?=$r['city']?></td>
		<td><?=$r['state']?></td>
		<td><?=$r['country']?></td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/imr/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>

<?=$prototip->renderTooltips();?>
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
			<a href="#" onclick="return false;" target="_blank">Learn more</a>
		</p>
		<?=$html->image('blank_slate_examples/reports_bids.gif')?>
	</div>
<?php endif; ?>
</div>