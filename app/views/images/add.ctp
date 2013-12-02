<div class="images form">
<?php echo $form->create('Image');?>
	<fieldset>
 		<legend><?php __('Add Image');?></legend>
	<?php
		echo $form->input('imagePath');
		echo $form->input('caption');
		echo $form->input('altTag');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Images', true), array('action' => 'index'));?></li>
	</ul>
</div>
