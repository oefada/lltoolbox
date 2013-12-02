<div class="packagePromos form">
<?php echo $form->create('PackagePromo');?>
	<fieldset>
 		<legend><?php __('Add PackagePromo');?></legend>
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
		<li><?php echo $html->link(__('List PackagePromos', true), array('action'=>'index'));?></li>
	</ul>
</div>
