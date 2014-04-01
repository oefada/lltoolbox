<?php $this->pageTitle = "Daily Sales Report" ?>

<div style="float:right;">
    <?if (!empty($results)): ?>
        <?=$html->link('<span><b class="icon"></b>Export Report</span>', array(
                'controller' => 'reports',
                'action' => $this->action.'/filter:'.urlencode($serializedFormInput),
                'ext' => 'csv',
                'format'=>'csv',
            ), array(
                'escape' => false,
                'class' => 'button excel',
            ));
        ?>
    <?endif;?>
</div>

<div class='advancedSearch' style="width: 800px">
	<?php echo $form->create('', array('action' => 'auction_winner'))?>
<fieldset>
<h3 class='title'>SEARCH DAILY SALES BY:</h3>

<div style="float: left; ">

<div class="fieldRow">
<label>Date Closed</label>
<?echo $form->text('condition1.field', array('value' => 'PaymentDetail.ppResponseDate', 'type' => 'hidden'))?>
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
<?php echo $form->submit('Search') ?>
</div>

<div class='index'>
<?php

if (!empty($results) && isset($serializedFormInput)): 

	$url = "/reports/auction_winner/filter:";
	$url .= urlencode($serializedFormInput);
	$url .= "/page:$currentPage";
	$url .= "/sortBy:";//$field";

	?>
	<div style='float: right'><?=$numRecords?> records found</div>
	<?=$pagination->Paginate("/reports/auction_winner/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:",$currentPage,$numPages)?>
	<table style="margin-top: 20px">
		<thead class='fixedHeader'>
		<tr>
		<th><?=$utilities->sortLink('Ticket.siteId', 'Site',$this, $html,$url)?></th>
        <th><?=$utilities->sortLink('Ticket.tldId', 'Locale', $this, $html, $url)?></th>
		<th><?=$utilities->sortLink('Offer.offerId', 'Booking Date',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('Client.name', 'Payment Date',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('Track.applyToMembershipBal', 'Ticket ID',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('Offer.offerTypeName', 'Vendor ID',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('country', 'Vendor',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('state', 'Guest First Name',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('city', 'Guest Last Name',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('percentMinBid', 'Address1',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('percentClose', 'Address2',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('Package.approvedRetailPrice', 'City',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('Package.numNights', 'State',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('SchedulingInstance.endDate', 'Zip',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Country',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Phone',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Email',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'CC Type',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'CC Number',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'CC Exp',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Revenue',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Room Nights',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Auction Type',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Handling Fee',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Percent',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Remit Type',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Validity Start Date',$this, $html,$url)?></th>
		<th><?=$utilities->sortLink('numBids', 'Validity End Date',$this, $html,$url)?></th>
		</tr>
		</thead>
<?php foreach ($results as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$siteIds[$r['Ticket']['siteId']]?></td>
        <td><?=$r['Locale']['code']?></td>
        <td><?=date('M d, Y h:m:s', strtotime($r[0]['endDate']))?></td>
        <td><?=date('M d, Y', strtotime($r['PaymentDetailFull'][0]['pd']['ppResponseDate']))?></td>
		<td>
            <?php
            if (
                isset($r['isPegasus']) &&
                $r['isPegasus'] == 1
            ){
                //pagasus ticket link
                echo $html->link($r['Ticket']['ticketId'],
                    array('controller'=>'PgBookings', 'action'=>'view',$r['Ticket']['ticketId']),
                    array('class' => '', 'target' => '_blank'));

            }else{
            echo $html->link($r['Ticket']['ticketId'],
                    array('controller'=>'tickets', 'action'=>'view',$r['Ticket']['ticketId']),
                    array('class' => '', 'target' => '_blank'));

            }
            ?>
        </td>
		<td><?=$r[0]['clientIds']?></td>
		<td><?=$r[0]['clientNames']?></td>
		<td>
            <?php echo $html->link($r['Ticket']['userFirstName'],
                array('controller'=>'users', 'action'=>'edit',$r['Ticket']['userId']),
                array('class' => '', 'target' => '_blank')); ?>
            </td>
		<td>
            <?php echo $html->link($r['Ticket']['userLastName'],
                array('controller'=>'users', 'action'=>'edit',$r['Ticket']['userId']),
                array('class' => '', 'target' => '_blank')); ?>
        </td>
		<td><?=$r['PaymentDetailFull'][0]['pd']['ppBillingAddress1']; ?></td>
		<td>&nbsp;</td>
		<td><?=$r['PaymentDetailFull'][0]['pd']['ppBillingCity']?></td>
		<td><?=$r['PaymentDetailFull'][0]['pd']['ppBillingState']?></td>
		<td><?=$r['PaymentDetailFull'][0]['pd']['ppBillingZip']?></td>

		<td><?
            if (!empty($r['Country']['countryName'])) {
                echo $r['Country']['countryName'];
            } else {
                echo $r['PaymentDetailFull'][0]['pd']['ppBillingCountry'];
            }
           ?></td>
		<td><?=$r['Ticket']['userHomePhone']?></td>
		<td><?=$r['Ticket']['userEmail1']?></td>
		<td><?=$r['PaymentDetailFull'][0]['pd']['ccType']?></td>
		<td>xxxx<?=$r['PaymentDetailFull'][0]['pd']['ppCardNumLastFour']?></td>
		<td><?=$r['PaymentDetailFull'][0]['pd']['ppExpMonth'].'/'.$r['PaymentDetailFull'][0]['pd']['ppExpYear']?></td>
		<td><?=$r[0]['revenue']?></td>
		<td><?=($r['Ticket']['numNights'] * $r['Package']['numRooms'])?></td>
		<td><?=$r['OfferType']['offerTypeName']?></td>
		<td><?switch($r['OfferType']['offerTypeName']) {
			case 'Standard Auction':
			case 'Dutch Auction':
			case 'Best Shot':
				echo '$40';
				break;
			case 'Best Buy':
            case 'Instant Conf':
			case 'Exclusive':
				echo '$40';
				break;
		}?></td>
		<td><?=$r[0]['percentOfRetail']?></td>
		<td><?
		switch($r['ExpirationCriteria']['expirationCriteriaId']) {
            case 1:
			case 4:
                    echo 'Keep';
                    break;
            case 2:
			case 3:
                    echo 'Remit';
                    break;
			default:
					echo '';
					break;
		}
		?>
		</td>
		<td><?=$r['PricePoint']['validityStart']?></td>
		<td><?=$r['PricePoint']['validityEnd']?></td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?=$pagination->Paginate("/reports/auction_winner/filter:".urlencode($serializedFormInput)."/sortBy:$sortBy/sortDirection:$sortDirection/page:",$currentPage, $numPages)?>
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
