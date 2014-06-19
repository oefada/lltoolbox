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
							echo $form->select('condition2.value', array('keep' => 'Keep', 2 => 'Remit', 3 => 'Commission/Upgrade'), null, array('multiple' => 'checkbox'))
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
if (!empty($results) && isset($serializedFormInput)): 

	$url = "/reports/offer_search/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:$field";
	$url .= "/sortDirection:$dir";
	
?>
	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/offer_search/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		<tr>
			<th><?=$utilities->sortLink('SchedulingMaster.siteId', 'Site', $this, $html)?></th>
			<th><?=$utilities->sortLink('Client.name', 'Client Name', $this, $html)?></th>
			<th><?=$utilities->sortLink('OfferType.offerTypeName', 'Offer Type', $this, $html)?></th>
			<th><?=$utilities->sortLink('Package.packageId', 'Package ID', $this, $html)?></th>
			<th><?=$utilities->sortLink('Package.packageName', 'Offer Name', $this, $html)?></th>
			<th><?=$utilities->sortLink('Package.numNights', 'Room Nights', $this, $html)?></th>
			<th><?=$utilities->sortLink('Package.validityEndDate', 'Validity End', $this, $html)?></th>
			<th><?=$utilities->sortLink('offerStatus', 'Status', $this, $html)?></th>
			<th><?=$utilities->sortLink('startDate', 'Date Opened', $this, $html)?></th>
			<th><?=$utilities->sortLink('endDate', 'Date Closed', $this, $html)?></th>
			<th><?=$utilities->sortLink('SchedulingMaster.numDaysToRun', '# Days', $this, $html)?>
			<th><?=$utilities->sortLink('numberOfBids', '# Bids', $this, $html)?></th>
			<th><?=$utilities->sortLink('Package.approvedRetailPrice', 'Retail Value', $this, $html)?></th>
			<th><?=$utilities->sortLink('SchedulingMaster.openingBid', 'Opening Bid Amount', $this, $html)?></th>
			<th><?=$utilities->sortLink('Loa.endDate', 'LOA Term End', $this, $html)?></th>
			<th><?=$utilities->sortLink('Loa.membershipBalance', 'LOA Balance', $this, $html)?></th>
			<th><?=$utilities->sortLink('auction_mstr.auction_wholesale', 'LOA Track Type', $this, $html)?></th>
			<th><?=$utilities->sortLink('Client.managerUsername', 'Manager Username', $this, $html)?></th>
			<th><?=$utilities->sortLink('futureInstances', 'Will Repeat', $this, $html)?></th>
		</tr>
		</thead>
<?php foreach ($results as $k => $result):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?= $siteIds[$result['SchedulingMaster']['siteId']]?></td>
		<td><?=$html->link($result['Client']['name'], array('controller' => 'clients', 'action' => 'edit', $result['Client']['clientId']), array('target' => '_blank'))?></td>
		<td><?=$result['OfferType']['offerTypeName']?></td>
		<td><?=$result['Package']['packageId']?></td>
		<td><?
				$month = date('m', strtotime($result['SchedulingInstance']['endDate']));
				$year = date('Y', strtotime($result['SchedulingInstance']['endDate']));
				echo $html->link(strip_tags($result['Package']['packageName']), "/scheduling/index/clientId:{$result['Client']['clientId']}/month:$month/year:$year", array('target' => '_blank'))?></td>
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
                    echo 'Commission/Upgrade';
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
				<?php if (in_array($result['OfferType']['offerTypeId'], unserialize(OFFER_TYPES_AUCTION)) && !$result[0]['futureInstances']): ?>
					<br /><br />
					<?php
					echo $html->link('Extend For 1 More Iteration',
									"/scheduling_instances/auto_extend/schedulingMasterId:{$result['SchedulingMaster']['schedulingMasterId']}",
									array(
										'title' => 'Extend Offer',
										'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
										'complete' => 'closeModalbox()'
										),
									null,
									false
									);
					?>
				<?php endif;?>
			</div>
		</td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/offer_search/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>
<?php endif; ?>
</div>
