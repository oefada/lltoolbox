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
<? 

$paginator->options(array('url' => $this->passedArgs));

?>
	<th><?=$paginator->sort('Purchaser User Id','userId');?></th>
	<th><?=$paginator->sort('Purchaser Username','UserSiteExtended.username');?></th>
	<th><?=$paginator->sort('Promo Code','PromoCode.promoCode');?></th>
	<th><?=$paginator->sort('Balance','balance');?></th>
	<th><?=$paginator->sort('Last Transaction', 'datetime');?></th>
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
			<? if ($giftCertBalance['User']['userId'] > 0) { ?>
				<?php echo $giftCertBalance['PromoCode']['promoCode']; ?>
			<? } else { ?>
				<a href="/gift_cert_balances/assign_user/<?= $giftCertBalance['GiftCertBalance']['promoCodeId']; ?>"><?php echo $giftCertBalance['PromoCode']['promoCode']; ?></a>
			<? } ?>
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
