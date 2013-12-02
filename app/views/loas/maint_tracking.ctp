<?php
$loa = $this->data;
echo $this->element("loas_subheader", array("loa"=>$loa,"client"=>$client));
$this->searchController = 'Clients';
$this->set('clientId', $this->data['Client']['clientId']);
?>
<style>
.c{ #fff;	} 
.a{ text-align:center;
}
</style>

<h2 class="title"><?php __('Loa Maintenance Tracking');?> <?=$html2->c($loa['Loa']['loaId'], 'LOA Id:')?></h2>
<div id="loa-index" class="loas index">

	<!-- MAIN LOA DATA -->
	<br />
	<table cellpadding="0" cellspacing="0" style="font-size:11px;">
	<tr>
	<th class="c">Start Date</th>
	<th class="c">End Date</th>
	<th class="c">Membership Fee</th>
	<th class="c">Membership Balance</th>
	<th class="c">Total Revenue</th>
	<th class="c">Total Kept</th>
	<th class="c">Total Remitted</th>
	<th class="c">Num. Email Inclusions</th>
	<th class="c">Upgraded</th>
	<th class="c">Membership Total Packages</th>
	<th class="c">Membership Packages Remaining</th>
	<th class="c">Loa Number Packages</th>
	<th class="c">Num. Packages Remaining</th>
	</tr>
	<tr class="altrow">
	<td class="a"><?=date('M d, Y', strtotime($loa['Loa']['startDate']));?></td>
	<td class="a"><?=date('M d, Y', strtotime($loa['Loa']['endDate']));?></td>
	<td class="a"><?=$number->currency($loa['Loa']['membershipFee']);?></td>
	<td class="a"><?=$number->currency($loa['Loa']['membershipBalance']);?></td>
	<td class="a"><?=$number->currency($loa['Loa']['totalRevenue']);?></td>
	<td class="a"><?=$number->currency($loa['Loa']['totalKept']);?></td>
	<td class="a"><?=$number->currency($loa['Loa']['totalRemitted']);?></td>
	<td class="a"><?=$loa['Loa']['numEmailInclusions'];?></td>
	<td class="a"><?=($loa['Loa']['upgraded']) ? 'Yes' : 'No';?></td>
	<td class="a"><?=$loa['Loa']['membershipTotalPackages'];?></td>
	<td class="a"><?=$loa['Loa']['membershipPackagesRemaining'];?></td>
	<td class="a"><?=$loa['Loa']['loaNumberPackages'];?></td>
	<td class="a"><?=$loa['Loa']['numberPackagesRemaining'];?></td>
	</tr>
	</table>
	<!-- END MAIN LOA DATA-->

	<p align="center" style='margin-bottom:-20px;'>
	<a href="javascript:void(0);" id="showRows"><b>Show rows without ticketId's</b></a>
	&#183;   
	<a href="javascript:void(0);" id="hideRows"><b>Hide rows without ticketId's</b></a>
	</p>


	<!-- TRACKS DATA -->
	<?php 

	if (!empty($tracks)) {

	?>

		<!-- TRACK HEADER -->	
		<?php 

		foreach ($tracks as $track) { 

		?>
		
		<br /><h2><?=$track['trackName'];?></h2><br />
		<table cellpadding="0" cellspacing="0" style="font-size:11px;">
		<tr>
		<th class="c">Track Id</th>
		<th class="c">Track Name</th>
		<th class="c">Apply To Membership Balance</th>
		<th class="c">Keep Balance Due</th>
		<th class="c">Keep Percentage</th>
		</tr>
		<tr class="altrow">
		<td class="a"><?=$track['trackId'];?></td>
		<td class="a"><?=$track['trackName'];?></td>
		<td class="a"><?=$track['applyToMembershipBal'];?></td>
		<td class="a"><?=$track['keepBalDue'];?></td>
		<td class="a"><?=$track['keepPercentage'];?></td>
		</tr>
		</table>
		<!-- END TRACK HEADER -->


		<!-- OFFERS UNDER TRACK HEADER-->
		<?php 

		if (!empty($track['offers'])) { 

		?>

			<table cellpadding="0" cellspacing="0" border="1" style="font-size:11px;">
			<tr>

				<th style="color:#FFF;">Offer Id</th>
				<th style="color:#FFF;">Package Id</th>
				<th style="color:#FFF;">Offer Type</th>
				<th style="color:#FFF;">Start Date</th>
				<th style="color:#FFF;">End Date</th>
				<th style="color:#FFF;">Retail Value</th>
				<th style="color:#FFF;">Opening Bid</th>
				<th style="color:#FFF;">Min Bid % Retail</th>
				<th style="color:#FFF;">Closing % Retail</th>
				<th style="color:#FFF;">Num. Nights</th>
				<th style="color:#FFF;">Track Detail Id</th>
				<th class="c" class="c"><?=$utilities->clickSort($this, 'ticketId', 'Ticket Id', $html);?></th>
				<th class="c">Ticket Amount</th>
				<th class="c">Allocated Amount</th>
				<th class="c">Cycle</th>
				<th class="c">Amount Kept</th>
				<th class="c">Amount Remitted</th>
			</tr>
			<!-- END OFFERS UNDER TRACK HEADER-->

			<!-- OFFER DETAIL-->
			<? 

			foreach ($track['offers'] as $o) { 

					if ($o['ticketId']==0){
						echo "<tr style='display:none;' class='noTicketId'>";
					}else{
						echo "<tr style='background-color:#FFFFEE;'>";
					}
				
					?>

					<td class="a"><?=$o['offerId'];?></td>
					<td class="a"><?=$o['packageId'];?></td>
					<td class="a"><?=$o['offerTypeName'];?></td>
					<td class="a"><?=date('M d, Y', strtotime($o['startDate']));?></td>
					<td class="a"><?=date('M d, Y', strtotime($o['endDate']));?></td>
					<td class="a"><?=$number->currency($o['retailValue']);?></td>
					<td class="a"><?=$number->currency($o['openingBid']);?></td>
					<td class="a"><?=$number->toPercentage($o['openingBid'] / $o['retailValue'] * 100);?></td>
					<td class="a"><?=$number->toPercentage($o['ticketAmount'] / $o['retailValue'] * 100);?></td>

				<? if ($o['ticketId']){ ?>

					<td class="a"><?=isset($o['numNights']) ? $o['numNights'] : '-'; ?></td>
					<td class="a"><a href="/tickets/<?=$o['ticketId'];?>/trackDetails/edit/<?=$o['trackDetailId'];?>" target="_BLANK"><?=$o['trackDetailId'];?></a></td>
					<td class="a"><a href="/tickets/view/<?=$o['ticketId'];?>" target="_BLANK"><?=$o['ticketId'];?></a></td>
					<td class="a"><?=$number->currency($o['ticketAmount']);?></td>
					<td class="a"><?=$number->currency($o['allocatedAmount']);?></td>
					<td class="a"><?=$o['cycle'];?></td>
					<td class="a"><?=$number->currency($o['amountKept']);?></td>
					<td class="a"><?=$number->currency($o['amountRemitted']);?></td>		 

				<? }else{ ?>

					<td class="a"> - </td>
					<td class="a"> - </td>
					<td class="a"> - </td>
					<td class="a"> - </td>
					<td class="a"> - </td>
					<td class="a"> - </td>
					<td class="a"> - </td>
					<td class="a"> - </td>

				<? } ?>

				</tr>

			<?php } ?><!--END $track['offers'] foreach-->

			</table>


	<?php } ?><!--END !empty($track['offer']) conditional -->

	<?php } ?><!--END $tracks foreach loop -->

	<?php } ?><!-- END !empty($tracks) conditional-->
	

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
