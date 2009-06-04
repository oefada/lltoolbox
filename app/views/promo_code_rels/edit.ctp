<div class="promoCodeRels form">
<?php echo $form->create('PromoCodeRel');?>
	<fieldset>
 		<legend><?php __('Edit PromoCodeRel');?></legend>
	<?php
		echo $form->input('promoCodeRelId');
		echo $form->input('promoId');
		echo $form->input('promoCodeId');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('PromoCodeRel.promoCodeRelId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('PromoCodeRel.promoCodeRelId'))); ?></li>
		<li><?php echo $html->link(__('List PromoCodeRels', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Promos', true), array('controller'=> 'promos', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo', true), array('controller'=> 'promos', 'action'=>'add')); ?> </li>
	</ul>
</div>
