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
<label>From</label><?echo $form->text('condition2.value.between.0')?>
<label style='padding-left: 20px'>To</label><?echo $form->text('condition2.value.between.1')?>
</div>
</div>

<div class="fieldRow lastFieldRow">
<?echo $form->select('condition3.field', $condition3Options)?>
<div class='range'>

<?echo $datePicker->picker('condition3.value.between.0', array('label' => 'From'))?>
<?echo $datePicker->picker('condition3.value.between.1', array('label' => 'To'))?>
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
	$url = "/reports/offer_search/filter:";
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
	<?=$pagination->Paginate("/reports/offer_search/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		<tr>
			<th><?=sortLink('Client.name', 'Client Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('OfferType.offerTypeName', 'Offer Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.packageId', 'Package ID', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('SchedulingMaster.packageName', 'Offer Name', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.numNights', 'Room Nights', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.validityEndDate', 'Validity End', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('offerStatus', 'Status', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('dateOpenedOrClosed', 'Date Opened/Closed', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('numberOfBids', '# Bids', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.approvedRetailPrice', 'Retail Value', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('SchedulingMaster.openingBid', 'Opening Bid Amount', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.endDate', 'LOA Term End', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.membershipBalance', 'LOA Balance', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('auction_mstr.auction_wholesale', 'Remit Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Client.managerUsername', 'Manager Username', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('futureInstances', 'Will Repeat', $currentPage, $serializedFormInput, $this, $html)?></th>
		</tr>
		</thead>
<?php foreach ($results as $k => $result):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$html->link($result['Client']['name'], array('controller' => 'clients', 'action' => 'edit', $result['Client']['clientId']))?></td>
		<td><?=$result['OfferType']['offerTypeName']?></td>
		<td><?=$result['Package']['packageId']?></td>
		<td><?
				$month = date('m', strtotime($result['SchedulingInstance']['endDate']));
				$year = date('Y', strtotime($result['SchedulingInstance']['endDate']));
				echo $html->link(strip_tags($result['SchedulingMaster']['packageName']), "/scheduling/index/clientId:{$result['Client']['clientId']}/month:$month/year:$year")?></td>
		<td style="text-align: center"><?=$result['Package']['numNights']?></td>
		<td><div<?php echo ($result[0]['loaEndApproaching']) ? ' style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid #ff0;"' : '' ?>>
				<?=date('M j, Y', strtotime($result['Package']['validityEndDate']))?>
			</div>
		</td>
		<td><?php echo ($result[0]['offerStatus']) ? 'Open' : 'Closed' ?></td>
		<td><?=date('M j, Y h:i:s A', strtotime($result[0]['dateOpenedOrClosed']))?></td>
		<td style="text-align: center">
			<div<?php echo ($result[0]['flagBids']) ? 'style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid #c00;"': '' ; ?>>
				<?=$html->link($result[0]['numberOfBids'], '/bids/search?query='.$result['Offer']['offerId'])?>
			</div>
		</td>
		<td><?=$number->currency($result['Package']['approvedRetailPrice'], 'USD', array('places' => 0))?></td>
		<td><?=$result['SchedulingMaster']['openingBid']?></td>
		<td><div<?php echo ($result[0]['loaEndApproaching']) ? ' style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid #ff0;"' : '' ?>><?=$html->link(date('M j, Y', strtotime($result['Loa']['endDate'])), array('controller' => 'loas', 'action' => 'edit', $result['Loa']['loaId']))?></div></td>
		<td><div<?php if ($result['Loa']['membershipBalance'] <= 0) { echo ' style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid #c00;"'; } else if($result['Loa']['membershipBalance'] <= 1000) { echo ' style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid #ff0;"';} ?>><?=$html->link($number->currency($result['Loa']['membershipBalance'], 'USD', array('places' => 0)), array('controller' => 'loas', 'action' => 'edit', $result['Loa']['loaId']))?></div></td>
		<td><?
		switch($result['auction_mstr']['remitStatus']) {
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
		<td><?=$result['Client']['managerUsername']?></td>
		<td>
			<div<?php echo ($result[0]['futureInstances']) ? '': ' style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid #c00;"' ; ?>>
				<?php echo ($result[0]['futureInstances']) ? 'YES' : 'NO' ?>
			</div>
		</td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/offer_search/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
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
		<?=$html->image('blank_slate_examples/reports_offer_search.gif')?>
	</div>
<?php endif; ?>
</div>