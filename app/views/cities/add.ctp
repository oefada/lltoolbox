<div class="cities form">
<?php echo $form->create('City');?>
	<fieldset>
 		<legend><?php __('Add City');?></legend>
	<?php
		echo $form->input('cityName');
		echo $form->input('cityAlias');
	?>
		<div class="input">
			<label for="CityStateId">State (optional)</label>
			<select id="CityStateId" name="data[City][stateId]">
				<option value="">Loading states..</option>
			</select>
		</div>
		<div class="input">
			<label for="CityCountryId">Country</label>
			<select id="CityCountryId" name="data[City][countryId]">
				<?php foreach ($countries as $k=>$r): ?>
				<option value="<?= $k ?>"<?php if ($k == $this->data['City']['countryId']): ?> selected<?php endif; ?>><?= $r ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php echo $this->element('statecountryajax', array('countryId' => $this->data['City']['countryId'], 'stateId' => $this->data['City']['stateId'])) ?>
	<?php
		echo $form->input('latitude');
		echo $form->input('longitude');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
<?php echo $this->element('gm_geocode', array(
	'position' => 'CityLongitude', 
	'address' => array('CityCityName','CityStateId','CityCountryId'),
	'latlong' => array('CityLatitude','CityLongitude')
	)); ?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Cities', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
