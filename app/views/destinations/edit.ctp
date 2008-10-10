<div class="destinations form">
<?php echo $form->create('Destination');?>
	<fieldset>
 		<legend><?php __('Edit Destination');?></legend>
	<?php
		echo $form->input('destinationId');
		echo $form->input('parentId');
		echo $form->input('destinationName');
		echo $form->input('includeInNav');
		echo $form->input('display');
		echo $form->input('leftValue');
		echo $form->input('rightValue');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Destination.destinationId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Destination.destinationId'))); ?></li>
		<li><?php echo $html->link(__('List Destinations', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
