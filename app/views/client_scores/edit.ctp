<div class="clientScores form">
<?php echo $form->create('ClientScore');?>
	<fieldset>
 		<legend><?php __('Edit ClientScore');?></legend>
		<div class="controlset4">
		<?
		echo $form->input('sites', array('multiple' => 'checkbox'));
		?>
		</div>
	<?php
		echo $form->input('clientScoreId');
		echo $form->input('clientScoreTypeId');
		echo $form->input('clientId');
		echo $form->input('score');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
