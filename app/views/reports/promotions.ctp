
<div class="index">
<h1>Promotions</h1>

<form id="promo_chooser" action="<?php echo $this->webroot ?>reports/promotions" method="POST">
	<select id="promotions" name="promotions" onChange="getElementById('promo_chooser').submit()">
		<option>Select a promotion</option>
		
		<? foreach ($promotions AS $p): ?>
		<option value="<?=$p['Promotions']['id'];?>" <? if (isset($displayId) && $displayId == $p['Promotions']['id']) echo ' SELECTED '; ?>><?=$p['Promotions']['promotionName'];?></option>
		<? endforeach; ?>
		
	</select>
</form>

<br /><br />


<? if (isset($promotionEntries) && is_array($promotionEntries) && count($promotionEntries) > 0) : ?>

	<form id="promo_export" action="<?php echo $this->webroot ?>reports/promotions" method="POST">
		<input type="hidden" id="promotions" name="promotions" value="<?=$displayId;?>" />
		<input type="hidden" id="csv" name="csv" value="1" />
	</form>

	<a href="#" onClick="getElementById('promo_export').submit()">
		Export to CSV
	</a>
	<br /><br />
	
	<table cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<th>Created</th>
			<th>First Name</th>
			<th>Last Name</th>
			<th>E-mail Address</th>
			<th>Zip Code</th>
			<?
				$extraDataArr = json_decode($promotionEntries[0]['PromotionEntries']['extraData']);
				foreach ($extraDataArr AS $k => $v) {
					echo '<th>' . $k . '</th>';
				}
			?>
		</tr>
		
		<? foreach ($promotionEntries AS $p) : ?>
		<? $extraDataArr = json_decode($p['PromotionEntries']['extraData']); ?>
		<tr>
			<td><?=$p['PromotionEntries']['createdDt'];?></td>
			<td><?=$p['PromotionEntries']['firstName'];?></td>
			<td><?=$p['PromotionEntries']['lastName'];?></td>
			<td><?=$p['PromotionEntries']['email'];?></td>
			<td><?=$p['PromotionEntries']['zip'];?></td>
			<?
				foreach ($extraDataArr AS $k => $v) {
					echo '<td>' . $v . '</td>';
				}
			?>
		</tr>
		<? endforeach; ?>
		
	</tbody>
	</table>
<? endif; ?>

</div>