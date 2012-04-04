<div class="states form">
<?php $this->set('hideSidebar', true); ?>
<?php echo $form->create('State');?>
	<fieldset>
 		<legend><?php __('Edit State');?></legend>
	<?php
		echo $form->input('stateId');
	?>
 		<div class="input">
 			<label for="StateCountryId">Country</label>
				<select name="data[State][countryId]" id="StateCountryId">
					<option value="">Select a Country</option>
					<?php foreach($countries as $k=>$v): ?>
					<option value="<?= $k ?>"<? if ($k == $this->data['State']['countryId']): ?> selected="selected"<?php endif; ?>><?= $v ?></option>
					<?php endforeach; ?>
				</select>
			</label>
		</div>
	<?php
		echo $form->input('stateName');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('State.stateId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('State.stateId'))); ?></li>
		<li><?php echo $html->link(__('List States', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
	</ul>
</div>
