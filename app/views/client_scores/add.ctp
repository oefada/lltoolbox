<div class="clientScores form">
<?php echo $form->create('ClientScore');?>
	<fieldset>
 		<legend><?php __('Add ClientScore');?></legend>
	<?php
		echo $form->input('clientScoreTypeId');
		echo $form->input('clientId');
		echo $form->input('score');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
