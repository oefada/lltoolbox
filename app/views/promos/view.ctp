<div class="related">
	<h3><?php __('Related Promo Codes');?></h3>
	<?php if (!empty($promo['PromoCode'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('PromoCodeId'); ?></th>
		<th><?php __('PromoCode'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($promo['PromoCode'] as $promoCode):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $promoCode['promoCodeId'];?></td>
			<td><?php echo $promoCode['promoCode'];?></td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Promo Code', true), array('controller'=> 'promo_codes', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
