<div class="loaItems form">
	<?php $session->flash(); ?>
<?php echo $ajax->form('add', 'post', array('url' => "/loas/{$this->data['LoaItem']['loaId']}/loa_items/add", 'update' => 'MB_content', 'model' => 'LoaItem', 'complete' => 'closeModalbox()'));?>
	<fieldset>
 		<legend><?php __('Add LoaItem');?></legend>
	<?php
		echo $form->input('loaItemTypeId');
		echo $form->input('loaId', array('type' => "hidden"));
		echo $form->input('itemName');
		echo $form->input('itemBasePrice');
		echo $form->input('perPerson');
//		echo $form->input('Fee.feeTypeId');
//		echo $form->input('Fee.feeName');
//		echo $form->input('Fee.feePercent');
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

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>