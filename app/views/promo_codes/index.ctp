<div class="promoCodes index">
<h2><?php __('PromoCodes');?></h2>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New PromoCode', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Promos', true), array('controller'=> 'promos', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo', true), array('controller'=> 'promos', 'action'=>'add')); ?> </li>
	</ul>
</div>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('promoCodeId');?></th>
	<th><?php echo $paginator->sort('promoCode');?></th>
</tr>
<?php
$i = 0;
foreach ($promoCodes as $promoCode):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $promoCode['PromoCode']['promoCodeId']; ?>
		</td>
		<td>
			<?php echo $promoCode['PromoCode']['promoCode']; ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>
