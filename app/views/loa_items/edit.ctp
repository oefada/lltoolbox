<div class="loaItems form">
<?php echo $form->create('LoaItem');?>
	<fieldset>
 		<legend><?php __('Edit LoaItem');?></legend>
	<?php
		echo $form->input('loaItemId');
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
		
		echo $form->input('Fee.feeId', array('type'=>'hidden', 'value' => $this->data['Fee']['feeId']));
		/*echo $form->input('LoaItemRatePeriod.loaItemRatePeriodId', array('type'=>'hidden', 'value' => $this->data['LoaItemRatePeriod']['loaItemRatePeriodId']));*/
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('LoaItem.loaItemId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('LoaItem.loaItemId'))); ?></li>
		<li><?php echo $html->link(__('List LoaItems', true), array('action'=>'index'));?></li>
	</ul>
</div>
