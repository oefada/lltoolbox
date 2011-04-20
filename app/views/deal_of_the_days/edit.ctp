<div class="dealOfTheDay form">
<?php echo $form->create('DealOfTheDay');?>
	<fieldset>
 		<legend><?php __('Edit DealOfTheDay');?></legend>
	<?php
		echo $form->input('dealOfTheDayId');
		echo $form->input('clientId');
		echo $form->input('packageId');
		echo $form->input('destinationId');
		echo $form->input('dateActive');
		echo $form->input('inventoryCount');
		echo $form->input('siteId');
		echo $form->input('isActive');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
