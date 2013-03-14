<div class="promoCodeRels index">
    <div style="float:right;">
    <?=$html->link('<span><b class="icon"></b>Export Report</span>', array(
        'controller' => 'PromoCodeRels',
        'action' => 'index/'.$id,
        'format' => 'csv',
    ), array(
        'escape' => false,
        'class' => 'button excel',
    ));
    ?>
    </div>
<h2><?php __('Promo Codes for ' . $promo['Promo']['promoName']);?></h2>
<p>
<?php

$paginator->options(array('url' => $this->passedArgs));

echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('promoCodeId');?></th>
	<th>Promo Code</th>
	<th></th>
</tr>
<?php
$i = 0;
foreach ($promoCodeRels as $promoCodeRel):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $promoCodeRel['PromoCodeRel']['promoCodeId']; ?>
		</td>
		<td>
			<?php echo $promoCodeRel['PromoCode']['promoCode']; ?>
		</td>
		<td align="center">
			<? if ($promoCodeRel['PromoCode']['inactive'] == 1) { ?>
			    INACTIVE  :: <a href="/promo_code_rels/index/<?= $id; ?>?pc_inactive=0&pc_id=<?= $promoCodeRel['PromoCodeRel']['promoCodeId']; ?>">TURN ON</a>
			<? } else { ?>
			    <a href="/promo_code_rels/index/<?= $id; ?>?pc_inactive=1&pc_id=<?= $promoCodeRel['PromoCodeRel']['promoCodeId']; ?>">TURN OFF</a>
			<? } ?>
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

