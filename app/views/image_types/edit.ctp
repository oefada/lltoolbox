<div class="imageTypes form">
<?php echo $form->create('ImageType');?>
	<fieldset>
 		<legend><?php __('Edit ImageType');?></legend>
	<?php
		echo $form->input('imageTypeId');
		echo $form->input('imageTypeName');
		echo $form->input('imageWidth');
		echo $form->input('imageHeight');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action' => 'delete', $form->value('ImageType.imageTypeId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('ImageType.imageTypeId'))); ?></li>
		<li><?php echo $html->link(__('List ImageTypes', true), array('action' => 'index'));?></li>
	</ul>
</div>
