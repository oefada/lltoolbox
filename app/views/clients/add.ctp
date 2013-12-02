<div class="clients form">
<?php echo $ajax->form('add', 'post', array('url' => "/clients/add/{$this->data['Client']['parentClientId']}", 'update' => 'MB_content', 'model' => 'Client', 'complete' => 'closeModalbox()'));?>
	<fieldset>
	<?php echo $form->input('clientTypeId', array('label' => 'Client Type')); ?>
	<?php
		echo $form->input('parentClientId', array('readonly' => true));
		
		echo $form->input('name');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>