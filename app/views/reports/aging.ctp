<?php $this->pageTitle = "LOA Quarterly Aging Report" ?>

<div class='index'>
<?php
//TODO: put this in a helper
function sortLink($field, $title, $view, $html) {
	$url = "/reports/bids/filter:";
	$url .= "/sortBy:$field";

	if (isset($view->params['named']['sortBy']) && $view->params['named']['sortBy'] == $field) {
		$dir = ($view->params['named']['sortDirection'] == 'ASC') ? 'DESC' : 'ASC';
	} elseif(isset($view->params['named']['sortBy'])  && $view->params['named']['sortBy'] == $field) {
		$dir = 'DESC';
	} else {
		$dir = 'ASC';
	}
	
	$url .= "/sortDirection:$dir";
	
	return $html->link($title, $url);
}

if (!empty($results)): 
$i = 1;
?>
<?php foreach($results as $periodName => $timeperiod): ?>
	<div><h2>Accounts aged <?=$periodName?> days</h2><?=count($timeperiod)?> records found</div>
	<table style="margin-top: 20px">
		<tr>
			<th>&nbsp;</th>
			<th><?=sortLink('Offer.offerId', 'Age (Days)', $this, $html)?></th>
			<th><?=sortLink('Offer.offerId', 'Client ID', $this, $html)?></th>
			<th><?=sortLink('Client.name', 'Client Name', $this, $html)?></th>
			<th><?=sortLink('Track.applyToMembershipBal', 'LOA ID', $this, $html)?></th>
			<th><?=sortLink('Offer.offerTypeName', 'Start Date', $this, $html)?></th>
			<th><?=sortLink('country', 'End Date', $this, $html)?></th>
			<th><?=sortLink('state', 'Membership Fee', $this, $html)?></th>
			<th><?=sortLink('city', 'Remaining Balance', $this, $html)?></th>
		</tr>
<?php foreach ($timeperiod as $k => $r):
$class = ($k % 2) ? ' class="altrow"' : '';
?>
	<tr<?=$class?>>
		<td><?=$i++?></td>
		<td><?=$r[0]['age']?></td>
		<td><?=$r['Client']['clientId']?></td>
		<td><?=$r['Client']['name']?></td>
		<td><?=$r['Loa']['loaId']?></td>
		<td><?=$r['Loa']['startDate']?></td>
		<td><?=$r[0]['loaEndDate']?></td>
		<td><?=$r['Loa']['membershipFee']?></td>
		<td><?=$r['Loa']['membershipBalance']?></td>
	</tr>
<?php endforeach; //TODO: add totals ?>
</table>
<?php endforeach; //end periods?>

<?php else: ?>
	<div class='blankExample'>
		<h1>No results found</h1>
	</div>
<?php endif; ?>
</div>