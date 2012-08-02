
<?php $this->pageTitle = "Suspicious Credit Card Activity (past 180 days)" ?>

<table>
<tr style="border: 1px solid #ddd; height:20px;">
	<td align="center">Ticket</td>
	<td align="center">Date Created</td>
	<td align="center">CC Failures</td>
	<td align="center">Last CC Failure</td>		
	<td align="center">Date Cleared</td>
	<td align="center">&nbsp;</td>
</tr>

<?  $count = 0;
	foreach ($results as $r) { 

		$ticketCleared = false;
		if ($r['f']['dateCleared'] != '' && (strtotime($r['f']['dateCleared']) > strtotime($r[0]['declinedLast']))) {
			$ticketCleared = true;
		}
		
		$count++;
		$bgColor = ($count % 2 == 0) ? 'ddd' : 'fff';
		if ($ticketCleared) { $bgColor = 'aaFBaa'; }
	?>
	<tr style="background-color: #<?= $bgColor; ?>";>
		<td align="center"><a href="/tickets/view/<?= $r['t']['ticketId']; ?>"><?= $r['t']['ticketId']; ?></a></td>
		<td align="right"><?= $r['t']['created']; ?></td>
		<td align="right"><?= $r[0]['declinedCount']; ?></td>
		<td align="right"><?= $r[0]['declinedLast']; ?></td>		
		<td align="right"><?= $r['f']['dateCleared']; ?> <?= $r['f']['clearedBy']; ?></td>
		<td align="center">
			<? if (!$ticketCleared) { ?>
				<a href="/reports/fraud_check?c=<?= $r['t']['ticketId']; ?>">CLEAR TICKET</a>
			<? } ?>
		</td>
	</tr>
<? } ?>

</table>