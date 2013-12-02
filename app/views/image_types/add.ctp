<div class="imageTypes form">
<?php echo $form->create('ImageType');?>
	<fieldset>
 		<legend><?php __('Add ImageType');?></legend>
	<?php
		echo $form->input('imageTypeName');
		echo $form->input('imageWidth');
		echo $form->input('imageHeight');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List ImageTypes', true), array('action' => 'index'));?></li>
	</ul>
</div>
