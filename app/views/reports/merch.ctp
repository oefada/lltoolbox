<? $this->pageTitle = 'Mercandising Dashboard for Today, '.date('m/d/y') ?>

<style>
table {
	width: auto;
}
th {
	background:#545454;
	color: #fff;
	padding: 0 5px;
}
</style>
<h2>Sales</h2>
<table style="width: auto">
	<tr>
		<th>&nbsp;</th>
		<th>Today</th>
		<th>Yesterday</th>
		<th nowrap>Last 7<br />(daily avg)</th>
		<th nowrap>Last 30<br />(daily avg)</th>
		<th><?=$lastMonthDisplay?>/<?=$twoMonthsAgoDisplay?></th>
		<th><?=$lastMonthDisplay?>/<?=$lastMonthLastYearDisplay?></th>
	</tr>
	<?php
	$cols = array(1 => 'Auctions Closing',
					'Auctions Closed w/bid',
					'% Auctions Close w/bid',
					'Avg. Sale Price for Auctions',
					'Fixed Price Requests',
					'FP Funded',
					'Avg. Sale Price for FP',
					'Travel Revenue');
	
	for($i = 1; $i <= 8; $i++): ?>
	<tr<?=($i % 2) ? ' class="altrow"': ""?>>
		<td><?=$cols[$i]?></td>
		<?php for($j = 1; $j <= 6; $j++): ?>
		<td><?=@$sales[$i][$j]?></td>
		<?php endfor; ?>
	</tr>
	<?php endfor; ?>
</table>
<h3>Revenue</h3>
<table style="width: auto">
	<tr>
		<th>&nbsp;</th>
		<th nowrap>MTD</th>
		<th nowrap>QTD</th>
		<th nowrap>YTD</th>
	</tr>
	<tr>
		<td style="width: 100px">
			Travel Revenue
		</td>
		<td>
			<?=$aging[1]['numClients']?>
		</td>
		<td>
			<?=$aging[2]['numClients']?>
		</td>
		<td>
			<?=$aging[3]['numClients']?>
		</td>
	</tr>
	<tr>
		<td style="width: 100px">
			Travel Revenue Goal
		</td>
		<td>
			<?=$aging[1]['numClients']?>
		</td>
		<td>
			<?=$aging[2]['numClients']?>
		</td>
		<td>
			<?=$aging[3]['numClients']?>
		</td>
	</tr>
	<tr class="altrow">
		<td style="width: 100px">
			% Travel Revenue Goal
		</td>
		<td>
			<?=$aging[1]['numClients']?>
		</td>
		<td>
			<?=$aging[2]['numClients']?>
		</td>
		<td>
			<?=$aging[3]['numClients']?>
		</td>
	</tr>
</table>
<h2>Aging</h2>
<table style="width: auto">
	<tr>
		<th>&nbsp;</th>
		<th nowrap>61 - 90 Days<br />into Contract</th>
		<th>%</th>
		<th nowrap>91 - 120 Days<br/>into Contract</th>
		<th>%</th>
		<th nowrap>121+ Days<br />into Contract</th>
		<th>%</th>
	</tr>
	<tr class="altrow">
		<td style="width: 100px">
			# OF CLIENTS WITH A BALANCE
		</td>
		<td>
			<?=$aging[1]['numClients']?>
		</td>
		<td>
			<?=round($aging[1]['numClients']/$aging[1]['totalClients']*100)?>
		</td>
		<td>
			<?=$aging[2]['numClients']?>
		</td>
		<td>
			<?=round($aging[2]['numClients']/$aging[2]['totalClients']*100)?>
		</td>
		<td>
			<?=$aging[3]['numClients']?>
		</td>
		<td>
			<?=round($aging[3]['numClients']/$aging[3]['totalClients']*100)?>
		</td>
	</tr>
</table>

<h2>Inventory Management</h2>
<div style="clear: both" class="clearfix">
<div style="float: left; margin-right: 40px">
<h3>Distressed Auctions</h3>
<table style="width: auto">
	<tr>
		<th>&nbsp;</th>
		<th nowrap>5 - 9 Runs<br />with no bids</th>
		<th>%</th>
		<th nowrap>10+ Runs<br/>with no bids</th>
		<th>%</th>
	</tr>
	<tr>
		<td style="width: 100px">
			Keep
		</td>
		<td>
			<a href="<?=implode(',',$distressedAuctions[1][1]['ids'])?>"><?=$distressedAuctions[1][1]['numOffers']?></a>
		</td>
		<td><?=round($distressedAuctions[1][1]['numOffers']/$distressedAuctions[1]['totalNumOffers']*100)?></td>
		<td>
			<a href="<?=implode(',',$distressedAuctions[1][2]['ids'])?>"><?=$distressedAuctions[1][2]['numOffers']?></a>
		</td>
		<td><?=round($distressedAuctions[1][2]['numOffers']/$distressedAuctions[1]['totalNumOffers']*100)?></td>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<td>
			<a href="<?=implode(',',$distressedAuctions[2][1]['ids'])?>"><?=$distressedAuctions[2][1]['numOffers']?></a>
		</td>
		<td><?=round($distressedAuctions[2][1]['numOffers']/$distressedAuctions[2]['totalNumOffers']*100)?></td>
		<td>
			<a href="<?=implode(',',$distressedAuctions[2][2]['ids'])?>"><?=$distressedAuctions[2][2]['numOffers']?></a>
		</td>
		<td><?=round($distressedAuctions[2][2]['numOffers']/$distressedAuctions[2]['totalNumOffers']*100)?></td>
	</tr>
	<tr class="altrow">
		<td style="width: 100px">
			Total
		</td>
		<td>
			<?=$distressedAuctions[1][1]['numOffers']+$distressedAuctions[2][1]['numOffers']?>
		</td>
		<td><?=round(($distressedAuctions[1][1]['numOffers']/$distressedAuctions[1]['totalNumOffers']
				+$distressedAuctions[2][1]['numOffers']/$distressedAuctions[2]['totalNumOffers'])*100)?></td>
		<td>
			<?=$distressedAuctions[1][2]['numOffers']+$distressedAuctions[2][2]['numOffers']?>
		</td>
		<td><?=round(($distressedAuctions[1][2]['numOffers']/$distressedAuctions[1]['totalNumOffers']
			+$distressedAuctions[2][2]['numOffers']/$distressedAuctions[2]['totalNumOffers'])*100)?></td>
	</tr>
</table>
</div>
<div style="float: left;">
<h3>Distressed Buy Nows</h3>
<table style="width: auto">
	<tr>
		<th>&nbsp;</th>
		<th nowrap>21 - 32 Days<br />w/o Request</th>
		<th nowrap>43+ Days<br/>w/o Requests</th>
	</tr>
	<tr>
		<td style="width: 100px">
			Keep
		</td>
		<td>
			<a href="<?=implode(',',$distressedBuyNows[1][1]['ids'])?>"><?=$distressedBuyNows[1][1]['numOffers']?></a>
		</td>
		<td>
			<a href="<?=implode(',',$distressedBuyNows[1][2]['ids'])?>"><?=$distressedBuyNows[1][2]['numOffers']?></a>
		</td>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<td>
			<a href="<?=implode(',',$distressedBuyNows[2][1]['ids'])?>"><?=$distressedBuyNows[2][1]['numOffers']?></a>
		</td>
		<td>
			<a href="<?=implode(',',$distressedBuyNows[2][2]['ids'])?>"><?=$distressedBuyNows[2][2]['numOffers']?></a>
		</td>
	</tr>
	<tr class="altrow">
		<td style="width: 100px">
			Total
		</td>
		<td>
			<?=$distressedBuyNows[1][1]['numOffers']+$distressedBuyNows[2][1]['numOffers']?>
		</td>
		<td>
			<?=$distressedBuyNows[1][2]['numOffers']+$distressedBuyNows[2][2]['numOffers']?>
		</td>
	</tr>
</table>
</div>
</div>
<h3>Packages with x days Validity Left</h3>
<table style="width: auto">
	<tr>
		<th>&nbsp;</th>
		<th nowrap>60 - 45 Days</th>
		<th nowrap>45 - 30 Days</th>
		<th nowrap>&lt; 30 Days</th>
	</tr>
	<tr>
		<td style="width: 100px">
			Keep
		</td>
		<td>
			<?=$expiringPackages[1][1]['numPackages']?>
		</td>
		<td>
			<?=$expiringPackages[1][2]['numPackages']?>
		</td>
		<td>
			<?=$expiringPackages[1][3]['numPackages']?>
		</td>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<td>
			<?=$expiringPackages[2][1]['numPackages']?>
		</td>
		<td>
			<?=$expiringPackages[2][2]['numPackages']?>
		</td>
		<td>
			<?=$expiringPackages[2][3]['numPackages']?>
		</td>
	</tr>
	<tr class="altrow">
		<td style="width: 100px">
			Total
		</td>
		<td>
			<?=$expiringPackages[1][1]['numPackages']+$expiringPackages[2][1]['numPackages']?>
		</td>
		<td>
			<?=$expiringPackages[1][2]['numPackages']+$expiringPackages[2][2]['numPackages']?>
		</td>
		<td>
			<?=$expiringPackages[1][3]['numPackages']+$expiringPackages[2][3]['numPackages']?>
		</td>
	</tr>
</table>
<div style="float: left; margin-right: 40px">
<h3>Auctions w/o buy now</h3>
<table style="width: auto">
	<tr>
		<th>&nbsp;</th>
		<th>Total</th>
	</tr>
	<tr>
		<td style="width: 100px">
			Keep
		</td>
		<td>
			<a href="<?=implode(',',$noBuyNows[1][1]['ids'])?>"><?=$noBuyNows[1][1]['numPackages']?></a>
		</td>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<td>
			<a href="<?=implode(',',$noBuyNows[2][1]['ids'])?>"><?=$noBuyNows[2][1]['numPackages']?></a>
		</td>
	</tr>
	<tr class="altrow">
		<td style="width: 100px">
			Total
		</td>
		<td>
			<?=$noBuyNows[1][1]['numPackages']+$noBuyNows[2][1]['numPackages']?>
		</td>
	</tr>
</table>
</div>
<div style="float: left;">
<h3>Clients with No Packages</h3>
<table style="width: auto;">
	<tr>
		<th>&nbsp;</th>
		<th>7 - 13 days</th>
		<th>%</th>
		<th>14 - 20 days</th>
		<th>%</th>
		<th>21 - 27 days</th>
		<th>%</th>
		<th>28+ days</th>
		<th>%</th>
	</tr>
	<tr>
		<td style="width: 100px">
			Keep
		</td>
		<td>
			<?=$clientsNoPackages[1][1]['numClients']?>
		</td>
		<td>
			<?=@round($clientsNoPackages[1][1]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
		<td>
			<?=$clientsNoPackages[1][2]['numClients']?>
		</td>
		<td>
			<?=@round($clientsNoPackages[1][2]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
		<td>
			<?=$clientsNoPackages[1][3]['numClients']?>
		</td>
		<td>
			<?=@round($clientsNoPackages[1][3]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
		<td>
			<?=$clientsNoPackages[1][4]['numClients']?>
		</td>
		<td>
			<?=@round($clientsNoPackages[1][4]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<td>
			<?=@$clientsNoPackages[2][1]['numClients']?>
		</td>
		<td>
			<?=@round($clientsNoPackages[2][1]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
		<td>
			<?=@$clientsNoPackages[2][2]['numClients']?>
		</td>
		<td>
			<?=@round($clientsNoPackages[2][2]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
		<td>
			<?=@$clientsNoPackages[2][3]['numClients']?>
		</td>
		<td>
			<?=@round($clientsNoPackages[2][3]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
		<td>
			<?=@$clientsNoPackages[2][4]['numClients']?>
		</td>
		<td>
			<?=@round($clientsNoPackages[2][4]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
	</tr>
	<tr class="altrow">
		<td style="width: 100px">
			Total
		</td>
		<td>
			<?=@$clientsNoPackages[1][1]['numClients']+@$noBuyNows[2][1]['numClients']?>
		</td>
		<td>&nbsp;</td>
		<td>
			<?=@$clientsNoPackages[1][2]['numClients']+@$noBuyNows[2][2]['numClients']?>
		</td>
		<td>&nbsp;</td>
		<td>
			<?=@$clientsNoPackages[1][3]['numClients']+@$noBuyNows[2][3]['numClients']?>
		</td>
		<td>&nbsp;</td>
		<td>
			<?=@$clientsNoPackages[1][4]['numClients']+@$noBuyNows[2][4]['numClients']?>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
</div>