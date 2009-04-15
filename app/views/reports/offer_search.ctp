<?php $this->pageTitle = "Offer Search" ?>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'offer_search'))?>
	<fieldset>
		<h3 class='title'>SEARCH OFFERS BY:</h3>

		<div>
			<div class="fieldRow">
				<?echo $form->select('condition1.field', $condition1Options)?>
			<?echo $form->text('condition1.value', array('style' => 'width: 250px'))?>
			</div>
			
			<div class="fieldRow lastFieldRow">
				<?echo $form->select('condition3.field', $condition3Options)?>
				<div class='range'>

					<?echo $datePicker->picker('condition3.value.between.0', array('label' => 'From'))?>
					<?echo $datePicker->picker('condition3.value.between.1', array('label' => 'To'))?>
					<a href="#" onclick='javascript: $("condition3valueBetween0").value = "<?=date('Y-m-d')?>"; $("condition3valueBetween1").value = "<?=date('Y-m-d')?>"'>Today</a> | 
					<a href="#" onclick='javascript: $("condition3valueBetween0").value = "<?=date('Y-m-d', strtotime('-1 week'))?>"; $("condition3valueBetween1").value = "<?=date('Y-m-d')?>"'>Last Week</a>
				</div>
			</div>
			
			<div class="fieldRow">
				<label>Retail Price</label>
				<?echo $form->text('condition2.field', array('value' => 'Package.approvedRetailPrice', 'type' => 'hidden'))?>
				<div class="range">
					<label>From</label><?echo $form->text('condition2.value.between.0')?>
					<label style='padding-left: 20px'>To</label><?echo $form->text('condition2.value.between.1')?>
				</div>
			</div>
	
			<div class="fieldRow" style="margin-bottom: 10px;">
				<label># of Room Nights</label>
				<?php echo $form->text('condition5.field', array('value' => 'Package.numNights', 'type' => 'hidden'))?>
				<?php echo $form->text('condition5.value')?>
			</div>
				<div class="fieldRow controlset3" style="float: left;">
					<label>Offer Type</label>
					<?php echo $form->text('condition4.field', array('value' => 'SchedulingMaster.offerTypeId', 'type' => 'hidden'))?>
					<?php echo $form->select('condition4.value', $condition4Options, null, array('multiple' => 'checkbox'))?>
				</div>
				<div class="fieldRow controlset3" style="float: left; clear: none; margin-right: 30px">
					<label>LOA Track Type</label>
					<?echo $form->text('condition2.field', array('value' => 'ExpirationCriteria.expirationCriteriaId', 'type' => 'hidden'))?>
					<div class="range">
						<?php
							echo $form->select('condition2.value', array('keep' => 'Keep', 2 => 'Remit', 3 => 'Commision/Upgrade'), null, array('multiple' => 'checkbox'))
						?>
					</div>
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
			<th><?=sortLink('startDate', 'Date Opened', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('endDate', 'Date Closed', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('SchedulingMaster.numDaysToRun', '# Days', $currentPage, $serializedFormInput, $this, $html)?>
			<th><?=sortLink('numberOfBids', '# Bids', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Package.approvedRetailPrice', 'Retail Value', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('SchedulingMaster.openingBid', 'Opening Bid Amount', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.endDate', 'LOA Term End', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Loa.membershipBalance', 'LOA Balance', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('auction_mstr.auction_wholesale', 'LOA Track Type', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('Client.managerUsername', 'Manager Username', $currentPage, $serializedFormInput, $this, $html)?></th>
			<th><?=sortLink('futureInstances', 'Will Repeat', $currentPage, $serializedFormInput, $this, $html)?></th>
		</tr>
		</thead>
<?php foreach ($results as $k => $result):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$html->link($result['Client']['name'], array('controller' => 'clients', 'action' => 'edit', $result['Client']['clientId']), array('target' => '_blank'))?></td>
		<td><?=$result['OfferType']['offerTypeName']?></td>
		<td><?=$result['Package']['packageId']?></td>
		<td><?
				$month = date('m', strtotime($result['SchedulingInstance']['endDate']));
				$year = date('Y', strtotime($result['SchedulingInstance']['endDate']));
				echo $html->link(strip_tags($result['SchedulingMaster']['packageName']), "/scheduling/index/clientId:{$result['Client']['clientId']}/month:$month/year:$year", array('target' => '_blank'))?></td>
		<td style="text-align: center"><?=$result['Package']['numNights']?></td>
		<td><div<?php 
					switch($result[0]['validityEndApproaching']) {
						case 1:
							$color = '#ff0';
						break;
						case 2:
							$color = '#f60';
						break;
						case 3:
							$color = '#f00';
						break;
					}
					echo ($result[0]['validityEndApproaching']) ? ' style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid '.$color.';"' : '' ?>>
				<?=date('M j, Y', strtotime($result['Package']['validityEndDate']))?>
			</div>
		</td>
		<td><?php echo ($result[0]['offerStatus']) ? 'Open' : 'Closed' ?></td>
		<td><?=date('M j, Y h:i:s A', strtotime($result['SchedulingInstance']['startDate']))?></td>
		<td><?=date('M j, Y h:i:s A', strtotime($result['SchedulingInstance']['endDate']))?></td>
		<td><?=$result['SchedulingMaster']['numDaysToRun']?></td>
		<td style="text-align: center">
			<div<?php echo ($result[0]['flagBids']) ? 'style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid #c00;"': '' ; ?>>
				<?=$html->link($result[0]['numberOfBids'], '/bids/search?query='.$result['Offer']['offerId'], array('target' => '_blank'))?>
			</div>
		</td>
		<td><?=$number->currency($result['Package']['approvedRetailPrice'], 'USD', array('places' => 0))?></td>
		<td><?=$result['SchedulingMaster']['openingBid']?></td>
		<td><div<?php 
				if ($result[0]['lastInstance']) {
					$color = '#c00';
				} else {
					$color = '#ff0';
				}
				echo ($result[0]['loaEndApproaching'] || $result[0]['lastInstance']) ? ' style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid '.$color.';"' : '' ?>><?=$html->link(date('M j, Y', strtotime($result['Loa']['endDate'])), array('controller' => 'loas', 'action' => 'edit', $result['Loa']['loaId']), array('target' => '_blank'))?></div></td>
		<td>
			<div<?php if (isset($result[0]['loaBalanceFlag'])) { 
							echo " style='min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid {$result[0]['loaBalanceFlag']};'"; 
							}?>>
			<?=$html->link($number->currency($result['Loa']['membershipBalance'], 'USD', array('places' => 0)), array('controller' => 'loas', 'action' => 'edit', $result['Loa']['loaId']), array('target' => '_blank'))?>
			</div>
		</td>
		<td><?
		switch($result['ExpirationCriteria']['expirationCriteriaId']) {
            case 1:
			case 4:
                    echo 'Keep';
                    break;

            case 2:
                    echo 'Remit';
                    break;

            case 3:
                    echo 'Commision/Upgrade';
                    break;
			default:
					echo '';
					break;
		}
		?></td>
		<td><?=$result['Client']['managerUsername']?></td>
		<td>
			<div<?php echo ($result[0]['futureInstances'] || in_array($result['OfferType']['offerTypeId'], unserialize(OFFER_TYPES_FIXED_PRICED))) ? '': ' style="min-height: 20px; line-height: 20px; padding: 4px; border: 4px solid #c00;"' ; ?>>
				<?php
					if (in_array($result['OfferType']['offerTypeId'], unserialize(OFFER_TYPES_FIXED_PRICED))): 
						echo "N/A";
					else:
						echo ($result[0]['futureInstances']) ? 'YES' : 'NO';
				 	endif;
				?>
				<?php if (in_array($result['OfferType']['offerTypeId'], unserialize(OFFER_TYPES_AUCTION))): ?>
					<br /><br /><a href="/scheduling_instances/add/schedulingMasterId:<?=$result['SchedulingMaster']['schedulingMasterId']?>" target="_blank">Extend For 1 More Iteration</a>
				<?php endif;?>
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