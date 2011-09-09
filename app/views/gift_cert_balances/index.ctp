<div class="giftCertBalances index">
<h2><?php __('Gift Certificates');?></h2>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Gift Certificate', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Promo Codes', true), array('controller'=> 'promo_codes', 'action'=>'index')); ?> </li>
	</ul>
</div>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Purchaser User Id</th>
	<th>Purchaser Username</th>
	<th>Promo Code</th>
	<th>Balance</th>
	<th>Last Transaction</th>
	<th class="actions"><?php __('Actions');?></th>
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
			<?php echo $giftCertBalance['User']['userId']; ?>
		</td>
		<td>
			<?php echo $giftCertBalance['UserSiteExtended']['username']; ?>
		</td>
		<td>
			<?php echo $giftCertBalance['PromoCode']['promoCode']; ?>
		</td>
		<td>
			$<?php echo $giftCertBalance['GiftCertBalance']['balance']; ?>
		</td>
		<td>
			<?php echo $giftCertBalance['GiftCertBalance']['datetime']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $giftCertBalance['GiftCertBalance']['promoCodeId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
