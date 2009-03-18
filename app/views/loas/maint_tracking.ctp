<?php
$loa = $this->data;
$this->pageTitle = $loa['Client']['name'].$html2->c($loa['Client']['clientId'], 'Client Id:');
$this->searchController = 'Clients';
$this->set('clientId', $this->data['Client']['clientId']);
?>
<h2 class="title"><?php __('Loa Maintenance Tracking');?> <?=$html2->c($loa['Loa']['loaId'], 'LOA Id:')?></h2>
<div id="loa-index" class="loas index">

	<br />
	<table cellpadding="0" cellspacing="0" style="font-size:11px;">
		<tr>
			<th style="color:#FFF;">Start Date</th>
			<th style="color:#FFF;">End Date</th>
			<th style="color:#FFF;">Membership Fee</th>
			<th style="color:#FFF;">Membership Balance</th>
			<th style="color:#FFF;">Loa Value</th>
			<th style="color:#FFF;">Total Kept</th>
			<th style="color:#FFF;">Total Remitted</th>
			<th style="color:#FFF;">Num. Email Inclusions</th>
			<th style="color:#FFF;">Upgraded</th>
			<th style="color:#FFF;">Membership Total Packages</th>
			<th style="color:#FFF;">Membership Packages Remaining</th>
			<th style="color:#FFF;">Loa Number Packages</th>
			<th style="color:#FFF;">Num. Packages Remaining</th>
		</tr>
		<tr class="altrow">
			<td style="text-align:center;"><?=$loa['Loa']['startDate'];?></td>
			<td style="text-align:center;"><?=$loa['Loa']['endDate'];?></td>
			<td style="text-align:center;"><?php echo $number->currency($loa['Loa']['membershipFee']);?></td>
			<td style="text-align:center;"><?php echo $number->currency($loa['Loa']['membershipBalance']);?></td>
			<td style="text-align:center;"><?php echo $number->currency($loa['Loa']['loaValue']);?></td>
			<td style="text-align:center;"><?php echo $number->currency($loa['Loa']['totalKept']);?></td>
			<td style="text-align:center;"><?php echo $number->currency($loa['Loa']['totalRemitted']);?></td>
			<td style="text-align:center;"><?=$loa['Loa']['numEmailInclusions'];?></td>
			<td style="text-align:center;"><?php echo ($loa['Loa']['upgraded']) ? 'Yes' : 'No';?></td>
			<td style="text-align:center;"><?=$loa['Loa']['membershipTotalPackages'];?></td>
			<td style="text-align:center;"><?=$loa['Loa']['membershipPackagesRemaining'];?></td>
			<td style="text-align:center;"><?=$loa['Loa']['loaNumberPackages'];?></td>
			<td style="text-align:center;"><?=$loa['Loa']['numberPackagesRemaining'];?></td>
		</tr>
	</table>

	<?php if (!empty($tracks)) :?>
	<?php foreach ($tracks as $track) : ?>
	
		<br /><h2><?php echo $track['trackName'];?></h2><br />
		<table cellpadding="0" cellspacing="0" style="font-size:11px;">
			<tr>
				<th style="color:#FFF;">Track Id</th>
				<th style="color:#FFF;">Track Name</th>
				<th style="color:#FFF;">Apply To Membership Balance</th>
				<th style="color:#FFF;">Keep Balance Due</th>
				<th style="color:#FFF;">Keep Percentage</th>
			</tr>
			<tr class="altrow">
				<td style="text-align:center;"><?=$track['trackId'];?></td>
				<td style="text-align:center;"><?=$track['trackName'];?></td>
				<td style="text-align:center;"><?=$track['applyToMembershipBal'];?></td>
				<td style="text-align:center;"><?=$track['keepBalDue'];?></td>
				<td style="text-align:center;"><?=$track['keepPercentage'];?></td>
			</tr>
		</table>
		
		<?php if (!empty($track['offers'])) : ?>
			<table cellpadding="0" cellspacing="0" border="1" style="font-size:11px;">
				<tr>
					<th style="color:#FFF;">Offer Id</th>
					<th style="color:#FFF;">Package Id</th>
					<th style="color:#FFF;">Offer Type</th>
					<th style="color:#FFF;">Start Date</th>
					<th style="color:#FFF;">End Date</th>
					<th style="color:#FFF;">Retail Value</th>
					<th style="color:#FFF;">Opening Bid</th>
					<th style="color:#FFF;">Ticket Id</th>
					<th style="color:#FFF;">Ticket Amount</th>
					<th style="color:#FFF;">Allocated Amount</th>
					<th style="color:#FFF;">Cycle</th>
					<th style="color:#FFF;">Iteration</th>
					<th style="color:#FFF;">Amount Kept</th>
					<th style="color:#FFF;">Amount Remitted</th>
				</tr>
		<?php foreach ($track['offers'] as $offer) : ?>			
				<tr <?php echo (isset($track_details[$offer['offerId']])) ? 'style="background-color:#FFFFEE"' : '';?>>
					<td style="text-align:center;"><?=$offer['offerId'];?></td>
					<td style="text-align:center;"><?=$offer['packageId'];?></td>
					<td style="text-align:center;"><?=$offer['offerTypeName'];?></td>
					<td style="text-align:center;"><?=$offer['startDate'];?></td>
					<td style="text-align:center;"><?=$offer['endDate'];?></td>
					<td style="text-align:center;"><?php echo $number->currency($offer['retailValue']);?></td>
					<td style="text-align:center;"><?php echo $number->currency($offer['openingBid']);?></td>
					
					<?php if (isset($track_details[$offer['offerId']])) :?>
						<td style="text-align:center;"><a href="/tickets/view/<?php echo $track_details[$offer['offerId']]['ticketId'];?>" target="_BLANK"><?php echo $track_details[$offer['offerId']]['ticketId'];?></a></td>
						<td style="text-align:center;"><?php echo $number->currency($track_details[$offer['offerId']]['ticketAmount']);?></td>
						<td style="text-align:center;"><?php echo $number->currency($track_details[$offer['offerId']]['allocatedAmount']);?></td>
						<td style="text-align:center;"><?php echo $track_details[$offer['offerId']]['cycle'];?></td>
						<td style="text-align:center;"><?php echo $track_details[$offer['offerId']]['iteration'];?></td>
						<td style="text-align:center;"><?php echo $number->currency($track_details[$offer['offerId']]['amountKept']);?></td>
						<td style="text-align:center;"><?php echo $number->currency($track_details[$offer['offerId']]['amountRemitted']);?></td>					
					<?php else :?>
						<td style="text-align:center;"> - </td>
						<td style="text-align:center;"> - </td>
						<td style="text-align:center;"> - </td>
						<td style="text-align:center;"> - </td>
						<td style="text-align:center;"> - </td>
						<td style="text-align:center;"> - </td>
						<td style="text-align:center;"> - </td>
						<td style="text-align:center;"> - </td>
					<?php endif; ?>
					
				</tr>			
		<?php endforeach; ?>
			</table>
		<?php endif; ?>
		
	<?php endforeach; ?>
	<?php endif; ?>
	
	<?php if ($trackWarning) { echo $trackWarning; } ?>
	
</div>