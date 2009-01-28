<div class="loaItems form">
<?php echo $ajax->form('edit', 'post', array('url' => "/loas/{$this->data['LoaItem']['loaId']}/loa_items/edit/{$this->data['LoaItem']['loaItemId']}", 'update' => 'MB_content', 'model' => 'LoaItem', 'complete' => 'closeModalbox()'));?>
	<fieldset>
 		<legend><?php __('Edit LoaItem');?></legend>
	<?php
		echo $form->input('loaItemId');
		echo $form->input('loaItemTypeId');
		echo $form->input('loaId', array('type' => 'hidden'));
		echo $form->input('itemName');
		echo $form->input('merchandisingDescription', array('label' => 'Live Site Description'));
		echo $form->input('currencyId', array('disabled' => 'disabled'));
		echo $form->input('itemBasePrice');
		echo $form->input('Fee.feeId');
		echo $form->input('Fee.feeName');
		echo $form->input('Fee.feePercent');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>