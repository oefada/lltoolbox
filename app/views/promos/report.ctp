<div class="promos index">
<table cellpadding="0" cellspacing="0">
<tr>
	<th>Promotion Name</th><th>Promotion Code</th><th>Percent Off</th><th>Amount Off</th><th>Date Start</th><th>Date End</th><th># of Codes</th>
</tr>
<tr>
	<td><?php echo $promo['Promo']['promoName']; ?></td>
    <td><? if (count($promo['PromoCode']) == 1) echo $promo['PromoCode'][0]['promoCode']; else echo '-'; ?></td>
    <td><?php echo $promo['Promo']['percentOff']; ?></td>
    <td><?php echo $promo['Promo']['amountOff']; ?></td>
    <td><?php echo $promo['Promo']['startDate']; ?></td>
    <td><?php echo $promo['Promo']['endDate']; ?></td>
    <td><? echo count($promo['PromoCode']); ?></td>
</tr>
</table>
</div>
<?

if ($num_packages > 0) {
	echo "<p><b>Number of Uses:</b> $num_packages Packages | $num_auctions Auctions | $num_buynows Fixed Price Requests</p>";
	echo "<p><b>Average Sale Price:</b> $" . number_format($total_sales / $num_packages, 2) . "</p>";
	echo "<p><b>Total Package Sale:</b> $" . number_format($total_sales, 2) . "</p>";
}

?>

<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Promos', true), array('action'=>'index'));?></li>
	</ul>
</div>

<?
/*

<div class="promos view">
<h2><?php  __('Promo');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PromoId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promo['Promo']['promoId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PromoName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promo['Promo']['promoName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PercentOff'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promo['Promo']['percentOff']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('AmountOff'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promo['Promo']['amountOff']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MinPurchaseAmount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promo['Promo']['minPurchaseAmount']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MaxNumUsage'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promo['Promo']['maxNumUsage']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Promo', true), array('action'=>'edit', $promo['Promo']['promoId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Promo', true), array('action'=>'delete', $promo['Promo']['promoId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $promo['Promo']['promoId'])); ?> </li>
		<li><?php echo $html->link(__('List Promos', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Promo Codes', true), array('controller'=> 'promo_codes', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo Code', true), array('controller'=> 'promo_codes', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Promo Codes');?></h3>
	<?php if (!empty($promo['PromoCode'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('PromoCodeId'); ?></th>
		<th><?php __('PromoId'); ?></th>
		<th><?php __('PromoCode'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
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
			<td><?php echo $promoCode['promoId'];?></td>
			<td><?php echo $promoCode['promoCode'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'promo_codes', 'action'=>'view', $promoCode['promoCodeId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'promo_codes', 'action'=>'edit', $promoCode['promoCodeId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'promo_codes', 'action'=>'delete', $promoCode['promoCodeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $promoCode['promoCodeId'])); ?>
			</td>
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

*/
?>