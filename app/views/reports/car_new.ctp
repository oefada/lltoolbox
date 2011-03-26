<? $this->pageTitle = 'Client Activity Report (internal use)'?>
<style>
#ClientName {
	width: 500px;
}
td, th {
	padding: 5px;
}
th {
	font-size: 10px;
}
td {
	text-align: right;
}
</style>
<div class='advancedSearch' style="width: 900px">
	<?php echo $form->create('Client', array('url' => '/reports/car_new'))?>
<fieldset>
<h3 class='title'>GENERATE CLIENT ACTIVITY REPORT BY:</h3>

<div class="fieldRow">
	<div style="float: left; clear: none;">
	<?php
	echo $strictAutocomplete->autoComplete('clientName', '/clients/auto_complete', array('indicator' => 'clientSearchSpinner'));
	?>
	</div>
	<div style="float: left; clear: none; display: none" id="clientSearchSpinner"><img src="/img/spinner_small.gif"></div>
	<div style="float: right; clear: none;" id="siteSelect"><? echo $form->select('site', array_merge($sites, array('combined'=>'Combined')), null, null, false); ?></div>
</div>

<?php if(isset($clientDetails)):?>
<div class="fieldRow">
	<label>Country, State, City</label><?=$clientDetails['Client']['locationDisplay']?>
</div>

<div class="fieldRow">
	<label>Most Recent LOA start date</label><?=date('M d, Y', strtotime($clientDetails['Loa']['startDate'])).' '.$html2->c($clientDetails['Loa']['loaId'])?>
</div>

<div class="fieldRow">
	<label>Membership Fee</label><?=$number->currency($clientDetails['Loa']['membershipFee'], 'USD', array('places' => 0))?>
</div>

<div class="fieldRow lastRow">
	<label>Account Manager</label><?=$clientDetails['Client']['managerUsername']?>
</div>
<?php endif; ?>
</fieldset>
<div class="controlset fieldRow">
	<?php echo $form->checkbox('download.csv');
			echo $form->label('download.csv', 'Download as CSV');?></div>
<?php echo $form->submit('Search') ?>
</div>

<?if(isset($clientDetails)):?>

<table border="1">
  <tr>
    <td>&nbsp;</td>
	<?php for($i = 0; $i <= 12; $i++) { 
		if ($i == 12) { echo "<th class='blackBg'>Last 12 Months</th>"; }
		echo "<th class='blackBg'>".$monthNames[$i]."</th>";
	      }
	?>
  </tr>
  <tr>
    <td style="text-align:left"><strong>calls</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo "<td><strong>".@$number->format($totals['phone'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['phone'])."</td>";
	  }
	?>
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>clicks</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
    		if ($i == 12) { echo "<td><strong>".@$number->format($totals['webRefer'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['webRefer'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td style="text-align:left"><strong>portfolio</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
    		if ($i == 12) { echo "<td><strong>".@$number->format($totals['productView'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['productView'])."</td>";
	  }
	?>
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>search</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
    		if ($i == 12) { echo "<td><strong>".@$number->format($totals['searchView'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['searchView'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td style="text-align:left"><strong>home/destination</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo "<td><strong>".@$number->format($totals['destinationView'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['destinationView'])."</td>";
	  }
	?>
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>email</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo "<td><strong>".@$number->format($totals['email'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['email'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <?php for($i = 0; $i <= 13; $i++)
			echo "<td>&nbsp;</td>";
	?>
  </tr>
  <tr>
    <td style="text-align:left"><strong>auctions live</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo "<td><strong>".@$number->format($totals['aucTotals'])."</strong></td>"; }
			$style = '';
				if(!@$results[$months[$i]]['numberAuctions']) {
					$style = " style='background-color: red; color: #fff'";
				}
			echo "<td$style>".@$number->format($results[$months[$i]]['numberAuctions'])."</td>";
		}
	?>
    
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>auctions sold</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
    		if ($i == 12) { echo "<td><strong>".@$number->format($totals['aucTickets'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['aucTickets'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td style="text-align:left"><strong>auctions $$</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
    		if ($i == 12) { echo "<td><strong>".@$number->currency($totals['aucRevenue'], 'USD', array('places' => 0))."</strong></td>"; }
		echo "<td>".@$number->currency($results[$months[$i]]['aucRevenue'], 'USD', array('places' => 0))."</td>";
	  }
	?>
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>auctions nights</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo "<td><strong>".@$number->format($totals['aucNights'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['aucNights'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <?php for($i = 0; $i <= 13; $i++)
			echo "<td>&nbsp;</td>";
	?>
  </tr>
  <tr>
    <td style="text-align:left"><strong>fixed price live</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo "<td><strong>".@$number->format($totals['fpTotals'])."</strong></td>"; }
			$style = '';
			if(!@$results[$months[$i]]['numberPackages']) {
				$style = " style='background-color: red; color: #fff'";
			}
			echo "<td$style>".@$number->format($results[$months[$i]]['numberPackages'])."</td>";
	  }
	?>
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>fixed price sold</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
    		if ($i == 12) { echo "<td><strong>".@$number->format($totals['fpTickets'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['fpTickets'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td style="text-align:left"><strong>fixed price $$</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo "<td><strong>".@$number->currency($totals['fpRevenue'], 'USD', array('places' => 0))."</strong></td>"; }
		echo "<td>".@$number->currency($results[$months[$i]]['fpRevenue'], 'USD', array('places' => 0))."</td>";
	  }
	?>
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>fixed price nights</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo "<td><strong>".@$number->format($totals['fpNights'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['fpNights'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <?php for($i = 0; $i <= 13; $i++)
			echo "<td>&nbsp;</td>";
	?>
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>total sold</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
    		if ($i == 12) { echo "<td><strong>".@$number->format($totals['aucTickets']+$totals['fpTickets'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['aucTickets']+$results[$months[$i]]['fpTickets'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td style="text-align:left"><strong>total $$</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo "<td><strong>".@$number->currency($totals['aucRevenue']+$totals['fpRevenue'], 'USD', array('places' => 0))."</strong></td>"; }
		echo "<td>".@$number->currency($results[$months[$i]]['aucRevenue']+$results[$months[$i]]['fpRevenue'], 'USD', array('places' => 0))."</td>";
	  }
	?>
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>total nights</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
    		if ($i == 12) { echo "<td><strong>".@$number->format($totals['aucNights']+$totals['fpNights'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['aucNights']+$results[$months[$i]]['fpNights'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <?php for($i = 0; $i <= 13; $i++)
			echo "<td>&nbsp;</td>";
	?>
  </tr>
  <tr class="altrow">
    <td style="text-align:left"><strong>hotel offer</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo "<td><strong>".@$number->format($totals['hotelOfferTotal'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['numberOffers'])."</td>";
	  }
	?>
  </tr>
  <tr>
    <td style="text-align:left"><strong>hotel offer clicks</strong></td>
    <?php for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo "<td><strong>".@$number->format($totals['event12'])."</strong></td>"; }
		echo "<td>".@$number->format($results[$months[$i]]['event12'])."</td>";
	  }
	?>
  </tr>
</table>
<? endif; ?>
