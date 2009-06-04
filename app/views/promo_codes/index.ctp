<div class="promoCodes index">
<h2><?php __('PromoCodes');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
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
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New PromoCode', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Promos', true), array('controller'=> 'promos', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo', true), array('controller'=> 'promos', 'action'=>'add')); ?> </li>
	</ul>
</div>
