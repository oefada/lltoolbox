<?php $this->pageTitle = "Active Loa and Packages Check" ?>
<style>
tr:nth-child(2n) {background-color:#dddddd}
</style>

<div style="position:absolute; right:20px; top:70px; font-weight:bold;">
<a href="/reports/active_loa_and_packages_check?sort=<?= $sortKey; ?>&csv=1">Download CSV</a>
</div>

<table>
<tr style='font-weight:bold;'>
<td><a href="/reports/active_loa_and_packages_check?sort=clientName">Client</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=sites">Sites</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=startDateSort">LOA Start Date</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=endDateSort">LOA End Date</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=managerUsername">Account Manager</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=destinations">Destinations</td>
<td><a href="/reports/active_loa_and_packages_check?sort=llLastOfferDaysSort">LL Days Down</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=llLastOfferDaysSort">LL Last Package</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=fgLastOfferDaysSort">FG Days Down</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=fgLastOfferDaysSort">FG Last Package</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=ticketCount">LOA Packages Sold</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=grossRevenue">LOA Sales Revenue</a></td>
<td><a href="/reports/active_loa_and_packages_check?sort=balanceSort">LOA Balance</a></td>
</tr>

<? foreach($results as $r) { ?>
	<tr>
	<td><a href="/clients/edit/<?= $r['clientId']; ?>"><?= $r['clientName']." : ".$r['clientId']; ?></a></td>
	<td><?= $r['sites']; ?></td>
	<td><?= $r['startDate']; ?></td>
	<td><?= $r['endDate']; ?></td>
	<td><?= $r['managerUsername']; ?></td>
	<td><?= $r['destinations']; ?></td>
	<td align="right"><?= $r['llLastOfferDays']; ?></td>
	<td align="right"><?= $r['llLastOffer']; ?></td>
	<td align="right"><?= $r['fgLastOfferDays']; ?></td>	
	<td align="right"><?= $r['fgLastOffer']; ?></td>
	<td align="right"><?= $r['ticketCount']; ?></td>
	<td align="right">$<?= number_format($r['grossRevenue']); ?></td>
	<td align="right"><?= $r['balance']; ?></td>
	</tr>
<? } ?>

</table>
