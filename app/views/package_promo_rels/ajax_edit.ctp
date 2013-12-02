<div class="loaItems form">
<?php echo $ajax->form('ajax_edit', 'post', array('url' => "/package_promo_rels/ajax_edit/{$this->data['PackagePromoRel']['packagePromoRelId']}", 'update' => 'MB_content', 'model' => 'PackagePromoRel', 'complete' => 'closeModalbox()'));?>
	<fieldset>
 		<legend><?php __('Edit Package Promo Rel');?></legend>
	<?php
		echo $form->input('packagePromoRelId');
		echo $form->input('benefitCopy');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>