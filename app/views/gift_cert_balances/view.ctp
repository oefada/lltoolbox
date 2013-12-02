<div class="creditTrackings index">
<h2>Gift Certificate Tracking for Promo Code: <span style="color:black;"><?php echo $giftCertBalances[0]['PromoCode']['promoCode']; ?></span></h2>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>giftCertBalanceId</th>
	<th>User Id</th>
	<th>Amount ($)</th>
	<th>Running Balance ($)</th>
	<th>Notes</th>
	<th>Transaction Date</th>
</tr>
<?php
$i = 0;
foreach ($giftCertBalances as $giftCertBalance):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $giftCertBalance['GiftCertBalance']['giftCertBalanceId']; ?>
		</td>
		<td>
			<?php echo $giftCertBalance['User']['userId']; ?>
		</td>
		<td>
			<?php echo $giftCertBalance['GiftCertBalance']['amount']; ?>
		</td>
		<td>
			<?php echo $giftCertBalance['GiftCertBalance']['balance']; ?>
		</td>
		<td>
			<?php echo $giftCertBalance['GiftCertBalance']['note']; ?>
		</td>
		<td>
			<?php echo $giftCertBalance['GiftCertBalance']['datetime']; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>

<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List All Gift Certificates', true), array('action'=>'index')); ?></li>
	</ul>
</div>
