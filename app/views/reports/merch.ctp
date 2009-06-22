<? $this->pageTitle = 'Merchandising Dashboard for ';
	if (date('m/d/y') == date('m/d/y', strtotime($date))) {
		$this->pageTitle .= ' Today, ';
	}
	$this->pageTitle .= date('m/d/y', strtotime($date)); 
?>

<style>
table {
	width: auto;
}
th {
	background:#545454;
	color: #fff;
	padding: 0 5px;
}
.dateChooser {
	float: right;
	clear: both;
	width: 300px;
}
.dateChooser input {
	width: 100px;
}
</style>

<div class="dateChooser clearfix">
	<form action="/reports/merch" method="post">
	<? echo $form->input('datePicker', array('class' => 'format-y-m-d divider-dash highlight-days-06 no-transparency range-high-today fill-grid-no-select',
											'label' => 'Select another date:',
											'readonly' => 'readonly',
											'onchange' => 'form.submit()')); ?>
	</form>
</div>
<div style="clear: both"></div>
<h2>Sales</h2>
<table style="width: auto">
	<tr>
		<th>&nbsp;</th>
		<th>Today</th>
		<th>Yesterday</th>
		<th nowrap>Last 7<br />(daily avg)</th>
		<th nowrap>Last 30<br />(daily avg)</th>
		<th nowrap>Last 90<br />(daily avg)</th>
		<th nowrap>Last 365<br />(daily avg)</th>
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
		<td>
			<? 
			$options = null;
			$options2 = null;
			if ($i == 1 || $i == 2 || $i == 5 || $i == 6) {
				$format = "precision";
				$options = 0;
			} else if ($i == 3) {
				$format = "toPercentage";
				$options = "1";
			} else {
				$format = "currency";
				$options = "USD";
				$options2 = array('places' => 0);
			}?>
		<?=@$number->$format($sales[$i][$j], $options, $options2)?>
		</td>
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
			<?=$number->currency($revenueMtd[0][0]['revenue'], 'USD', array('places' => 0));?>
		</td>
		<td>
			<?=$number->currency($revenueQtd[0][0]['revenue'], 'USD', array('places' => 0));?>
		</td>
		<td>
			<?=$number->currency($revenueYtd[0][0]['revenue'], 'USD', array('places' => 0));?>
		</td>
	</tr>
	<tr>
		<td style="width: 100px">
			Travel Revenue Goal
		</td>
		<td>
			?
		</td>
		<td>
			?
		</td>
		<td>
			?
		</td>
	</tr>
	<tr class="altrow">
		<td style="width: 100px">
			% Travel Revenue Goal
		</td>
		<td>
			?
		</td>
		<td>
			?
		</td>
		<td>
			?
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
			<a href="/reports/aging/#section-4"><?=$aging[1]['numClients']?></a>
		</td>
		<td>
			<?=round($aging[1]['numClients']/$aging[1]['totalClients']*100)?>
		</td>
		<td>
			<a href="/reports/aging/#section-3"><?=$aging[2]['numClients']?></a>
		</td>
		<td>
			<?=round($aging[2]['numClients']/$aging[2]['totalClients']*100)?>
		</td>
		<td>
			<a href="/reports/aging/#section-2"><?=$aging[3]['numClients']?></a>
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
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$distressedAuctions[1][1]['ids'])?>"><?=$distressedAuctions[1][1]['numOffers']?></a>
		</td>
		<td><?=round($distressedAuctions[1][1]['numOffers']/$distressedAuctions[1]['totalNumOffers']*100)?></td>
		<td>
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$distressedAuctions[1][2]['ids'])?>"><?=$distressedAuctions[1][2]['numOffers']?></a>
		</td>
		<td><?=round($distressedAuctions[1][2]['numOffers']/$distressedAuctions[1]['totalNumOffers']*100)?></td>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<td>
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$distressedAuctions[2][1]['ids'])?>"><?=$distressedAuctions[2][1]['numOffers']?></a>
		</td>
		<td><?=round($distressedAuctions[2][1]['numOffers']/$distressedAuctions[2]['totalNumOffers']*100)?></td>
		<td>
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$distressedAuctions[2][2]['ids'])?>"><?=$distressedAuctions[2][2]['numOffers']?></a>
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
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$distressedBuyNows[1][1]['ids'])?>"><?=$distressedBuyNows[1][1]['numOffers']?></a>
		</td>
		<td>
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$distressedBuyNows[1][2]['ids'])?>"><?=$distressedBuyNows[1][2]['numOffers']?></a>
		</td>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<td>
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$distressedBuyNows[2][1]['ids'])?>"><?=$distressedBuyNows[2][1]['numOffers']?></a>
		</td>
		<td>
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$distressedBuyNows[2][2]['ids'])?>"><?=$distressedBuyNows[2][2]['numOffers']?></a>
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
			<a href="/reports/imr/packageIds:<?=implode(',',$expiringPackages[1][1]['ids'])?>"><?=$expiringPackages[1][1]['numPackages']?></a></div>
		</td>
		<td>
			<a href="/reports/imr/packageIds:<?=implode(',',$expiringPackages[1][1]['ids'])?>"><?=$expiringPackages[1][2]['numPackages']?></a></div>
		</td>
		<td>
			<a href="/reports/imr/packageIds:<?=implode(',',$expiringPackages[1][1]['ids'])?>"><?=$expiringPackages[1][3]['numPackages']?></a></div>
		</td>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<td>
			<a href="/reports/imr/packageIds:<?=implode(',',$expiringPackages[1][1]['ids'])?>"><?=$expiringPackages[2][1]['numPackages']?></a></div>
		</td>
		<td>
			<a href="/reports/imr/packageIds:<?=implode(',',$expiringPackages[1][1]['ids'])?>"><?=$expiringPackages[2][2]['numPackages']?></a></div>
		</td>
		<td>
			<a href="/reports/imr/packageIds:<?=implode(',',$expiringPackages[1][1]['ids'])?>"><?=$expiringPackages[2][3]['numPackages']?></a></div>
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
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$noBuyNows[1][1]['ids'])?>"><?=$noBuyNows[1][1]['numPackages']?></a>
		</td>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<td>
			<a href="/reports/imr/schedulingMasterIds:<?=implode(',',$noBuyNows[2][1]['ids'])?>"><?=$noBuyNows[2][1]['numPackages']?></a>
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
		<?for ($i = 1; $i <= 4; $i++):?>
		<td>
			<a href="/reports/cmr/clientIds:<?=implode(',',(array)@$clientsNoPackages[1][$i]['clientIds'])?>">
			<?=$clientsNoPackages[1][$i]['numClients']?>
			</a>
		</td>
		<td>
			<?=@round($clientsNoPackages[1][$i]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
		<?endfor;?>
	</tr>
	<tr>
		<td style="width: 100px">
			Remit
		</td>
		<?for ($i = 1; $i <= 4; $i++):?>
		<td>
			<a href="/reports/cmr/clientIds:<?=implode(',',(array)@$clientsNoPackages[2][$i]['clientIds'])?>">
			<?=$clientsNoPackages[2][$i]['numClients']?>
			</a>
		</td>
		<td>
			<?=@round($clientsNoPackages[2][$i]['numClients']/$clientsNoPackages['totalClients']*100)?>
		</td>
		<?endfor;?>
	</tr>
	<tr class="altrow">
		<td style="width: 100px">
			Total
		</td>
		<?for ($i = 1; $i <= 4; $i++):?>
		<td>
			<?=@$clientsNoPackages[1][$i]['numClients']+@$clientsNoPackages[2][$i]['numClients']?>
		</td>
		<td>
			<?=@round(($clientsNoPackages[1][$i]['numClients']/$clientsNoPackages['totalClients']*100)+($clientsNoPackages[2][$i]['numClients']/$clientsNoPackages['totalClients']*100))?>
		</td>
		<?endfor;?>
	</tr>
</table>
</div>