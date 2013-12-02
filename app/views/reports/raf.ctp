<style type="text/css">
tr { border-bottom: 1px solid #ccc; }
form div { clear: none; }
</style>

<script type="text/javascript">
jQuery(function() {
	jQuery('#startDate').click(function() {
		showCalendar('startDate', '%Y-%m-%d');
	});
	jQuery('#endDate').click(function() {
		showCalendar('endDate', '%Y-%m-%d');
	});
});
</script>


<form name="report-params" method="POST" action="/reports/raf">
	<div style="float: left;">
		<b>Select site:</b>
		<select name="siteId">
 		<option value="1">Luxury Link</option>
		<option value="2">Family Getaway</option>
		</select>
	</div>

	<div style="clear: both;"></div>

	<div style="width: 200px; float: left;">
		<?=$datePicker->picker('startDate', array('label' => '<b>Start Date:</b> ','value'=>(isset($startDate)?$startDate:'')));?>
	</div>
	<div style="width: 200px; float: left; margin-left: 40px;">
		<?=$datePicker->picker('endDate', array('label' => '<b>End Date:</b> ','value'=>(isset($endDate)?$endDate:'')));?>
	</div>
	
	<div style="float: left; margin-top: 15px;">
		<input type="submit" value="Go" />
	</div>
	<div style="clear: both;"></div>
</form>


<? if (isset($startDate) && isset($endDate)) : ?>
<h2><?=$startDate;?> to <?=$endDate;?></h2>


	<table cellpadding="0" cellspacing="0" style="width: 350px;">
	<tbody>
		<tr>
			<td># Referrers</td>
			<td><?=@$resultArr['numReferrers'];?></td>
		</tr>
		<tr>
			<td># Invitees</td>
			<td><?=@$resultArr['numReferred'];?></td>
		</tr>
		<tr>
			<td># Invitees w/ purchase</td>
			<td><?=@$resultArr['numWithPurchase'];?></td>
		</tr>
		<tr>
			<td>$ credit given to invitees</td>
			<td>$<?=number_format($resultArr['referredCredit']);?></td>
		</tr>
		<tr>
			<td>$ credit given to referrers</td>
			<td>$<?=number_format($resultArr['referrerCredit']);?></td>
		</tr>
		<tr>
			<td>Avg # invites sent per referrer</td>
			<td><?=number_format($resultArr['avgInvitesSent'], 2);?></td>
		</tr>
		<tr>
			<td>Avg # invites signed up per referrer</td>
			<td><?=number_format($resultArr['avgInvitesComplete'], 2);?></td>
		</tr>
		<tr>
			<td>Total gross sales made by all invitees</td>
			<td>$<?=number_format($resultArr['totalSales']);?></td>
		</tr>
		<tr>
			<td>Avg # days from invitees signup to purchase</td>
			<td><?=number_format($resultArr['avgDays'], 2);?></td>
		</tr>
	</tbody>
	</table>
<? endif; ?>