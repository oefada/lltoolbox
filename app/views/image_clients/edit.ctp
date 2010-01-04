<div class="imageClients form">
<?php echo $form->create('ImageClient');?>
	<fieldset>
 		<legend><?php __('Edit ImageClient');?></legend>
	<?php
		echo $form->input('clientImageId');
		echo $form->input('siteId');
		echo $form->input('imageId');
		echo $form->input('imageTypeId');
		echo $form->input('clientId');
		echo $form->input('caption');
		echo $form->input('sortOrder');
		echo $form->input('isHidden');
		echo $form->input('inactive');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action' => 'delete', $form->value('ImageClient.clientImageId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('ImageClient.clientImageId'))); ?></li>
		<li><?php echo $html->link(__('List ImageClients', true), array('action' => 'index'));?></li>
	</ul>
</div>
