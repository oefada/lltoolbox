<?php
$loa = $this->data;
echo $this->element("loas_subheader", array("loa"=>$loa,"client"=>$client));
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
			<th style="color:#FFF;">Total Revenue</th>
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
			<td style="text-align:center;"><?php echo $number->currency($loa['Loa']['totalRevenue']);?></td>
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


	<p align="center" style='margin-bottom:-20px;'>
		<a href="javascript:void(0);" id="showRows"><b>Show rows without ticketId's</b></a>
		 &#183;   
		<a href="javascript:void(0);" id="hideRows"><b>Hide rows without ticketId's</b></a>
	</p>

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
					<th style="color:#FFF;"><?=($utilities->clickSort($this, 'offerId'));?></a></th>
					<th style="color:#FFF;"><?=$utilities->clickSort($this,'packageId');?></th>
					<th style="color:#FFF;"><?=$utilities->clickSort($this, 'offerTypeName');?></th>
					<th style="color:#FFF;"><?=$utilities->clickSort($this,'startDate');?></th>
					<th style="color:#FFF;"><?=$utilities->clickSort($this, 'endDate');?></th>
					<th style="color:#FFF;"><?=$utilities->clickSort($this, 'retailValue');?></th>
					<th style="color:#FFF;"><?=$utilities->clickSort($this, 'openingBid');?></th>
					<th style="color:#FFF;">Min Bid % Retail</th>
					<th style="color:#FFF;">Closing % Retail</th>
		  		<th style="color:#FFF;">Num. Nights</th>
					<th style="color:#FFF;">Track Detail Id</th>
					<th style="color:#FFF;"><?=$utilities->clickSort($this, 'ticketId', 'Ticket Id');?></th>
					<th style="color:#FFF;">Ticket Amount</th>
					<th style="color:#FFF;">Allocated Amount</th>
					<th style="color:#FFF;">Cycle</th>
					<th style="color:#FFF;">Amount Kept</th>
					<th style="color:#FFF;">Amount Remitted</th>
				</tr>

				<?php foreach ($track['offers'] as $tid=>$offer) : 

					$td_rows='<td style="text-align:center;">'.$offer['offerId'].'</td>
					<td style="text-align:center;">'.$offer['packageId'].'</td>
					<td style="text-align:center;">'.$offer['offerTypeName'].'</td>
					<td style="text-align:center;">'.$offer['startDate'].'</td>
					<td style="text-align:center;">'.$offer['endDate'].'</td>
					<td style="text-align:center;">'.($number->currency($offer['retailValue'])).'</td>
					<td style="text-align:center;">'.($number->currency($offer['openingBid'])).'</td>
					<td style="text-align:center;">'.($number->toPercentage($offer['openingBid'] / $offer['retailValue'] * 100)).'</td>';


						if (isset($offer['ticketId'])){ ?>

							<tr style="background-color:#FFFFEE">
							<?=$td_rows;?>
							<td style="text-align:center;"><?php echo $number->toPercentage($offer['ticketAmount'] / $offer['retailValue'] * 100);?></td>
							<td style="text-align:center;"><?php echo isset($offer['numNights']) ? $offer['numNights'] : '-'; ?></td>
							<td style="text-align:center;"><a href="/tickets/<?php echo $offer['ticketId'];?>/trackDetails/edit/<?php echo $offer['trackDetailId'];?>" target="_BLANK"><?php echo $offer['trackDetailId'];?></a></td>
							<td style="text-align:center;"><a href="/tickets/view/<?php echo $offer['ticketId'];?>" target="_BLANK"><?php echo $offer['ticketId'];?></a></td>
							<td style="text-align:center;"><?php echo $number->currency($offer['ticketAmount']);?></td>
							<td style="text-align:center;"><?php echo $number->currency($offer['allocatedAmount']);?></td>
							<td style="text-align:center;"><?php echo $offer['cycle'];?></td>
							<td style="text-align:center;"><?php echo $number->currency($offer['amountKept']);?></td>
							<td style="text-align:center;"><?php echo $number->currency($offer['amountRemitted']);?></td>
						<? }else{ ?>

							<tr class='noTicketId' style="display:none;">
							<?=$td_rows;?>
							<td style="text-align:center;"> - </td>
							<td style="text-align:center;"> - </td>
							<td style="text-align:center;"> - </td>
							<td style="text-align:center;"> - </td>
							<td style="text-align:center;"> - </td>
							<td style="text-align:center;"> - </td>
							<td style="text-align:center;"> - </td>
							<td style="text-align:center;"> - </td>
							<td style="text-align:center;"> - </td>
							</tr>			

						<? } ?>

						</tr>

				<?php endforeach; ?>

			</table>
		<?php endif; ?>
		
	<?php endforeach; ?>
	<?php endif; ?>
	
	<?php if ($trackWarning) { echo $trackWarning; } ?>
	
</div>



<script>

jQuery(function(){
	jQuery("#showRows").click(function(){
		jQuery(".noTicketId").show();
	});
	jQuery("#hideRows").click(function(){
		jQuery(".noTicketId").hide();
	});
});

</script>
