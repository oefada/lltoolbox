<div class="loaItems form">
<?php echo $form->create('LoaItem');?>
	<fieldset>
 		<legend><?php __('Add LoaItem');?></legend>
	<?php
		echo $form->input('loaItemTypeId');
		echo $form->input('loaId');
		echo $form->input('itemName');
		echo $form->input('itemBasePrice');
		echo $form->input('perPerson');
		echo $form->input('Fee.feeTypeId');
		echo $form->input('Fee.feeName');
		echo $form->input('Fee.feePercent');
		/*echo $form->input('LoaItemRatePeriod.loaItemRatePeriodName');
		echo $form->input('LoaItemRatePeriod.startDate');
		echo $form->input('LoaItemRatePeriod.endDate');
		echo $form->input('LoaItemRatePeriod.approvedRetailPrice');
		echo $form->input('LoaItemRatePeriod.approved');
		echo $form->input('LoaItemRatePeriod.approvedBy');*/
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List LoaItems', true), array('action'=>'index'));?></li>
	</ul>
</div>
