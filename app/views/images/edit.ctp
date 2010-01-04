<div class="images form">
<?php echo $form->create('Image');?>
	<fieldset>
 		<legend><?php __('Edit Image');?></legend>
	<?php
		echo $form->input('imageId');
		echo $form->input('imagePath');
		echo $form->input('caption');
		echo $form->input('altTag');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action' => 'delete', $form->value('Image.imageId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Image.imageId'))); ?></li>
		<li><?php echo $html->link(__('List Images', true), array('action' => 'index'));?></li>
	</ul>
</div>
