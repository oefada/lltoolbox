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
		echo $form->input('Fee.feeName');
		echo $form->input('Fee.feePercent');
		echo $form->input('currencyId', array('value' => $currencyId, 'type' => 'hidden'));
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>