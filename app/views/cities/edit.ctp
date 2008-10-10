<div class="cities form">
<?php echo $form->create('City');?>
	<fieldset>
 		<legend><?php __('Edit City');?></legend>
	<?php
		echo $form->input('cityId');
		echo $form->input('cityName');
		echo $form->input('stateId');
		echo $form->input('countryId');
		echo $form->input('latitude');
		echo $form->input('longitude');
		echo $form->input('cityCode');
		echo $form->input('Tag');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('City.cityId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('City.cityId'))); ?></li>
		<li><?php echo $html->link(__('List Cities', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
