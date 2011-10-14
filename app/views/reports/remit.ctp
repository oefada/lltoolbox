<h2>Remit Packages Report</h2>


<?php
	$sortUrl = '/reports/remit/sortBy:';
?>

<div class="bids index referrals-index" style="margin-bottom: 40px;">
	<form id="csv" method="POST">
		<input type="hidden" name="csv" value="y" />
	</form>
	<a href="#" onClick="getElementById('csv').submit();">Export to CSV</a>

<?=$pagination->Paginate("/reports/remit/sortBy:$sortBy/sortDirection:$sortDirection/page:", $currentPage, $numPages)?>

<table>
	<tr>
		<th><?=$utilities->sortLink('lastSold', 'Days Since Last Sale', $this, $html, $sortUrl)?></th>
		<th><?=$utilities->sortLink('client.name', 'Client Name', $this, $html, $sortUrl)?></th>
		<th><?=$utilities->sortLink('package.packageId', 'Package ID', $this, $html, $sortUrl)?></th>
		<th><?=$utilities->sortLink('loa.loaId', 'LOA ID', $this, $html, $sortUrl)?></th>
		<th><?=$utilities->sortLink('loa.totalRemitted', 'Remitted Revenue', $this, $html, $sortUrl)?></th>
		<th><?=$utilities->sortLink('loaStart', 'LOA Start Date', $this, $html, $sortUrl)?></th>
		<th><?=$utilities->sortLink('loaEnd', 'LOA End Date', $this, $html, $sortUrl)?></th>
		<th><?=$utilities->sortLink('isLive', 'Is Live', $this, $html, $sortUrl)?></th>
		<th><?=$utilities->sortLink('client.managerUsername', 'Account Manager', $this, $html, $sortUrl)?></th>
	</tr>
	
	
<?php
	$i = 0;
	foreach ($packages AS $p) :
		$i++;
		if ($i % 2) {
			$rowClass = '';
		} else {
			$rowClass = 'class="altrow"';
		}
?>
	<tr <?=$rowClass;?> style="padding-top: 8px; padding-bottom: 8px;">
		<td><?=$p[0]['lastSold'];?></td>
		<td><?=$p['client']['name'];?></td>
		<td><a href="/clients/<?=$p['client']['clientId'];?>/packages/summary/<?=$p['ticket']['packageId'];?>"><?=$p['ticket']['packageId'];?></a></td>
		<td><?=$p['loa']['loaId'];?></td>
		<td><?=$number->currency($p['loa']['totalRemitted'], 'USD', array('places' => 0));?></td>
		<td><?=$p[0]['loaStart'];?></td>
		<td><?=$p[0]['loaEnd'];?></td>
		<td><?=$p[0]['isLive'] ? 'Yes' : 'No';?></td>
		<td><?=$p['client']['managerUsername'];?></td>
	</tr>
	
<?php
	endforeach;
?>

</table>

</div>