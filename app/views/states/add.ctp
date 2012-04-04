<div class="states form">
<?php echo $form->create('State');?>
	<fieldset>
 		<legend><?php __('Add State');?></legend>
 		<div class="input">
 			<label for="StateCountryId">Country</label>
 			<select name="data[State][countryId]" id="StateCountryId">
 				<option value="">Select a Country</option>
 				<?php foreach($countries as $k=>$v): ?>
 				<option value="<?= $k ?>"><?= $v ?></option>
 				<?php endforeach; ?>
 			</select>
 		</div>
	<?php
		echo $form->input('stateId');
		echo $form->input('stateName');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List States', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>
