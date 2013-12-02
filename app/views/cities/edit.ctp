<div class="cities form">
<?php $this->set('hideSidebar', true); ?>
<?php echo $form->create('City');?>
	<fieldset>
 		<legend><?php __('Edit City');?></legend>
	<?php
		echo $form->input('cityId');
		echo $form->input('cityName');
	?>
		<div class="input">
			<label for="CityCountryId">Country</label>
			<select id="CityCountryId" name="data[City][countryId]">
				<?php foreach ($countries as $k=>$r): ?>
				<option value="<?= $k ?>"<?php if ($k == $this->data['City']['countryId']): ?> selected<?php endif; ?>><?= $r ?></option>
				<?php endforeach; ?>
			</select>
		</div>
		<div class="input">
			<label for="CityStateId">State</label>
			<select id="CityStateId" name="data[City][stateId]">
				<option value="">Loading states..</option>
			</select>
		</div>

		<?php echo $this->element('statecountryajax', array('countryId' => $this->data['City']['countryId'], 'stateId' => $this->data['City']['stateId'])) ?>
	<?php

		echo $form->input('latitude');
		echo $form->input('longitude');
		echo $form->input('cityAlias');
	?>

		<div class="input">
			<label for="CityGeoBandId">Geo Band</label>
			<select name="data[City][geoBandId]" style="font-size:12px">
				<option value="">-- </option>
				<?= $geoSelectOptions; ?>
			</select>
		</div>

        <div class="input">
            <label for="CityDestinationForward">Destination Forward</label>
            <select name="data[City][destinationForward]" style="font-size:12px">
                <option value="">-- </option>
                <?= $destForwardSelectOptions; ?>
            </select>
        </div>
	
	
	<?php echo $form->input('isDisabled'); ?>
	</fieldset>
<?php echo $form->submit('Submit');?>
<?php echo $form->end() ?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('City.cityId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('City.cityId'))); ?></li>
		<li><?php echo $html->link(__('List Cities', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
	</ul>
</div>
