<div class="promoCodes view">
<h2><?php  __('PromoCode');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PromoCodeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promoCode['PromoCode']['promoCodeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Promo'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($promoCode['Promo']['promoName'], array('controller'=> 'promos', 'action'=>'view', $promoCode['Promo']['promoId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PromoCode'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promoCode['PromoCode']['promoCode']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit PromoCode', true), array('action'=>'edit', $promoCode['PromoCode']['promoCodeId'])); ?> </li>
		<li><?php echo $html->link(__('Delete PromoCode', true), array('action'=>'delete', $promoCode['PromoCode']['promoCodeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $promoCode['PromoCode']['promoCodeId'])); ?> </li>
		<li><?php echo $html->link(__('List PromoCodes', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New PromoCode', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Promos', true), array('controller'=> 'promos', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo', true), array('controller'=> 'promos', 'action'=>'add')); ?> </li>
	</ul>
</div>
