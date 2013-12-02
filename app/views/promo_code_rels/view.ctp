<div class="promoCodeRels view">
<h2><?php  __('PromoCodeRel');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PromoCodeRelId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promoCodeRel['PromoCodeRel']['promoCodeRelId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Promo'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($promoCodeRel['Promo']['promoName'], array('controller'=> 'promos', 'action'=>'view', $promoCodeRel['Promo']['promoId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PromoCodeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $promoCodeRel['PromoCodeRel']['promoCodeId']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit PromoCodeRel', true), array('action'=>'edit', $promoCodeRel['PromoCodeRel']['promoCodeRelId'])); ?> </li>
		<li><?php echo $html->link(__('Delete PromoCodeRel', true), array('action'=>'delete', $promoCodeRel['PromoCodeRel']['promoCodeRelId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $promoCodeRel['PromoCodeRel']['promoCodeRelId'])); ?> </li>
		<li><?php echo $html->link(__('List PromoCodeRels', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New PromoCodeRel', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Promos', true), array('controller'=> 'promos', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo', true), array('controller'=> 'promos', 'action'=>'add')); ?> </li>
	</ul>
</div>
