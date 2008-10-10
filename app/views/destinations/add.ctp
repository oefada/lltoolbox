<div class="destinations form">
<?php echo $form->create('Destination');?>
	<fieldset>
 		<legend><?php __('Add Destination');?></legend>
	<?php
		echo $form->input('destinationName');
		echo $form->input('parentId');
		echo $form->input('tagId');
		echo $form->input('leftValue');
		echo $form->input('rightValue');
		echo $form->input('includeInNav');
		echo $form->input('display');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Destinations', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
