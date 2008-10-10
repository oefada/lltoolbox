<div class="tags form">
<?php echo $form->create('Tag');?>
	<fieldset>
 		<legend><?php __('Edit Tag');?></legend>
	<?php
		echo $form->input('tagId');
		echo $form->input('tagName');
		echo $form->input('Country');
		echo $form->input('State');
		echo $form->input('City');
		echo $form->input('Coordinate');
		echo $form->input('Client');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Tag.tagId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Tag.tagId'))); ?></li>
		<li><?php echo $html->link(__('List Tags', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Destinations', true), array('controller'=> 'destinations', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Destination', true), array('controller'=> 'destinations', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Coordinates', true), array('controller'=> 'coordinates', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Coordinate', true), array('controller'=> 'coordinates', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
	</ul>
</div>
