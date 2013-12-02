<div class="packagePromos form">
<?php echo $form->create('PackagePromo');?>
	<fieldset>
 		<legend><?php __('Edit PackagePromo');?></legend>
	<?php
		echo $form->input('packagePromoId');
		echo $form->input('packageId');
		echo $form->input('description');
		echo $form->input('promoCode');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('PackagePromo.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('PackagePromo.id'))); ?></li>
		<li><?php echo $html->link(__('List PackagePromos', true), array('action'=>'index'));?></li>
	</ul>
</div>
