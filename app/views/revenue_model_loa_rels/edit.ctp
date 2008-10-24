<div class="revenueModelLoaRels form">
<?php echo $form->create('RevenueModelLoaRel');?>
	<fieldset>
 		<legend><?php __('Edit RevenueModelLoaRel');?></legend>
	<?php
		echo $form->input('revenueModelLoaRelId');
		echo $form->input('loaId');
		echo $form->input('revenueModelId');
		echo $form->input('expirationCriteriaId');
		echo $form->input('tierNum');
		echo $form->input('isUpgrade');
		echo $form->input('fee');
		echo $form->input('x');
		echo $form->input('y');
		echo $form->input('iteration');
		echo $form->input('cycle');
		echo $form->input('balanceDue');
		echo $form->input('keepPercentage');
		echo $form->input('pending');
		echo $form->input('collected');
		echo $form->input('expMaxOffers');
		echo $form->input('expDate');
		echo $form->input('expFee');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('RevenueModelLoaRel.revenueModelLoaRelId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('RevenueModelLoaRel.revenueModelLoaRelId'))); ?></li>
		<li><?php echo $html->link(__('List RevenueModelLoaRels', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Expiration Criteria', true), array('controller'=> 'expiration_criteria', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Expiration Criterium', true), array('controller'=> 'expiration_criteria', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Revenue Models', true), array('controller'=> 'revenue_models', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Revenue Model', true), array('controller'=> 'revenue_models', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Revenue Model Loa Rel Details', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Revenue Model Loa Rel Detail', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'add')); ?> </li>
	</ul>
</div>
