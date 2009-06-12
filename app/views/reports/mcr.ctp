<? $this->pageTitle = "Account Manager Client Report"; ?>
<script>
function initializeFunctions(){
	
	$$(".genericTable thead th a").each(
		function(node, i){

			node.onclick = function(){

				// determine what column will be sorted by
				columnIndex = $(node).id.replace("sort","");

				// sort!
				sortTable(columnIndex);

				// remove the ants and don't follow the href
				this.blur();
				return false;

			}

		}
	);

}


function sortTable(columnIndex){
	unsortedRows = $$(".genericTable tbody tr");
	sortedRows = unsortedRows.sortBy(function(node, i){
		var value = node.childElements()[columnIndex].collectTextNodes();
					if(!isNaN(value)){
						return parseInt(node.childElements()[columnIndex].collectTextNodes());
					}
					return node.childElements()[columnIndex].collectTextNodes();
	});

	// reverse
	if(unsortedRows.first() == sortedRows.first()){
		sortedRows.reverse();
	}

	// make the HTML
	sortedRowsHTML = "";
	sortedRows.each(function(node, i){
		if (i % 2 == 0) {
			class = ' class="altrow"';
		} else {
			class = '';
		}
		sortedRowsHTML += "<tr"+class+">"+ node.innerHTML + "</tr>";
	});

	// write the HTML to the page
	Element.update($$(".genericTable tbody").first(), sortedRowsHTML);

}


window.onload = function(){
	initializeFunctions();
}
</script>
<style>
.darkBlackBg a {
	background: #000;
}
</style>
<div class="advancedSearch" style="width: 360px; float: left;">
	<?php echo $form->create('', array('url' => '/reports/mcr'))?>
	<fieldset>
	<div class="fieldRow"><label>Account Manager:</label>
		<?echo $form->hidden('condition1.field', array('value' => 'MATCH=managerUsername'))?>
		<?= $form->select('condition1.value', $managerUsernames)?>
	</div>
	<div class="fieldRow">	
	<label>Region:</label>
		<?= $form->hidden('condition2.field', array('value' => 'ClientDestinationRel.destinationId'))?>
		<?= $form->select('condition2.value', $regions)?>
	</div>
	</fieldset>
	
	<fieldset style="border-top: 1px solid #ccc">
	<div class="fieldRow">
		<?echo $form->select('condition3.field', array('LIKE=Client.name' => 'Client Name', 'Client.clientId' => 'Client ID'))?>
		<?= $form->text('condition3.value')?>
	</div>
	</fieldset>
	
	<fieldset>
	<div class="fieldRow controlset" style="padding-left: 135px">
		<?php 		
					echo $form->hidden('condition4.field', array('value' => 'Loa.loaLevelId'));
					if ($this->data['condition4']['value'] == 2) {
						$checked = ' checked="checked"';
					} else {
						$checked = '';
					}
					echo '<input type="checkbox" name="data[condition4][value]" value="2"'.$checked.'>';
					echo $form->label('hideWholesale');?>
	</div>
	
	
	</fieldset>
	
	<?php echo $form->submit('Search') ?>
	<?php echo $form->end()?>
</div>
<div class="advancedSearch" style="margin-left: 20px; width: 430px; float: left;">
	<h4>Quick Links</h4>
	<?php
	echo $html->link('LOAs expiring in less than 60 days', '/reports/mcr/ql:1')."<br />";
	echo $html->link('Clients with zero packages live Today', '/reports/mcr/ql:2')."<br />";
	echo $html->link('Clients with packages expiring in less than 60 days', '/reports/mcr/ql:3')."<br />";
	echo $html->link('Clients with Zero phone calls in the last Month', '/reports/mcr/ql:4')."<br />";
	echo $html->link('Clients with zero packages sold in last 30 days', '/reports/mcr/ql:5')."<br />";
	echo $html->link('Clients with zero fixed price requests in last 60 days', '/reports/mcr/ql:6')."<br />";
	?>
</div>
<div style="clear: both;"></div>
<table class="rowBorderDark genericTable" style="text-align: center">
	<thead>
		<tr>
	    <th rowspan="2" class="blackBg" style="text-align: center"><a href="0" id="sort0">Client Name</a></td>
	    <th colspan="8" class="darkBlackBg rowBorderWhite" style="border-top: 1px solid #000; text-align: center; padding: 5px 0">
			Package Revenue
			<div style="float: right;">
				<?php echo $form->create('', array('url' => $_SERVER['REQUEST_URI']))?>
					<?php echo $form->hidden('condition1.field');
						  echo $form->hidden('condition1.value');
						echo $form->hidden('condition2.field');
						echo $form->hidden('condition2.value');
						echo $form->hidden('condition3.field');
						echo $form->hidden('condition3.value');
						echo $form->hidden('condition4.field');
						echo $form->hidden('condition4.value');?>
				<?=$form->select('MCR.pkgRevenueRange', $pkgRevenueRanges, null, array('onchange' => '$("spinner").show(); submit()'), false); ?>
				<?php echo $form->end(); ?>
			</div>
		</td>
	    <th colspan="5" class="blackBg" style="text-align: center">LOA Information</td>
	    <th colspan="6" class="darkBlackBg rowBorderWhite" style="border-top: 1px solid #000; text-align: center">Referrals/Impressions</td>
	  </tr>
	  <tr>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="1" id="sort1">Packages Live Today</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="2" id="sort2">Packages Up Time</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="3" id="sort3">Total Sold</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="4" id="sort4">Total $$</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="5" id="sort5">Auctions Live Today</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="6" id="sort6">Auctions Close Rate</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="7" id="sort7">FP Live Today</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="8" id="sort8"># of FP Requests</a></td>
	    <th class="blackBg" style="text-align: center"><a href="9" id="sort9">Exp. Date</a></td>
	    <th class="blackBg" style="text-align: center"><a href="10" id="sort10">Renewed<br />(LOA Start)</a></td>
	    <th class="blackBg" style="text-align: center"><a href="11" id="sort11">LOA Type</a></td>
	    <th class="blackBg" style="text-align: center"><a href="12" id="sort12">LOA Bal</a></td>
	    <th class="blackBg" style="text-align: center"><a href="13" id="sort13">Days until keep ends</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="14" id="sort14">Web</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="15" id="sort15">Phone</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="16" id="sort16">Portfolio</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="17" id="sort17">Search</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="18" id="sort18">Email</a></td>
	    <th class="darkBlackBg rowBorderWhite" style="text-align: center"><a href="19" id="sort19">Home/Dest</a></td>
	  </tr>
	</thead>
	<tbody>
<?php foreach($clients as $k => $row): 
	if ($k %2 == 0) {
		$class = ' class="altrow"';
	} else {
		$class = '';
	}
?>
	  <tr<?=$class?>>
	    <td style="text-align: left;"><?=$row['Client']['name']?></td>
		<? if ($k == 0) echo "<div id='packageRevenue'>"?>
	    <td><?=(int)$row['packagesLiveToday']?></td>
	    <td><?=(int)$row['packageUptime']?></td>
	    <td><?=(int)$row['totalSold']?></td>
	    <td><?=(int)$row['totalRevenue']?></td>
	    <td><?=(int)$row['auctionsLiveToday']?></td>
	    <td><?=(int)$row['auctionCloseRate']?></td>
	    <td><?=(int)$row['fpLiveToday']?></td>
	    <td><?=(int)$row['fpRequests']?></td>
		<? if ($k == count($clients) - 1) echo "</div>"?>
	    <td><?=date('m/d/Y', strtotime($row['Loa']['endDate']))?></td>
	    <td><?=date('m/d/Y', strtotime($row['Loa2']['startDate']))?></td>
	    <td><?=($row['Loa']['loaLevelId'] == 2) ? 'Sponsorship' : 'Wholesale' ;?></td>
	    <td><?=(int)$row['Loa']['membershipBalance']?></td>
	    <td><?=(int)$row[0]['daysUntilKeepEnd']?></td>
	    <td><?=(int)@$row['Referrals']['webRefer']?></td>
	    <td><?=(int)@$row['Referrals']['phone']?></td>
	    <td><?=(int)@$row['Referrals']['productView']?></td>
	    <td><?=(int)@$row['Referrals']['searchView']?></td>
	    <td><?=(int)@$row['Referrals']['email']?></td>
	 	<td><?=(int)@$row['Referrals']['destinationView']?></td>
	  </tr>
<?php endforeach; ?>
	</tbody>
</table>