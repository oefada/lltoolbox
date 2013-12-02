<div class="accolades form">
<?php echo $form->create('Accolade');?>
	<fieldset>
 		<legend><?php __('Edit Accolade');?></legend>
		<div class="controlset4">
		<?
		echo $multisite->checkbox('Accolade');
		?>
		</div>
	<?php
		echo $form->input('accoladeId');
		echo $form->input('accoladeSourceId');
		echo $form->input('clientId');
		echo $form->input('accoladeName');
		echo $form->input('description');
		echo $form->input('accoladeDate');
		echo $form->input('displayDate');
	?>
	<div class="controlset">
	<?php
		echo $form->input('inactive');
	?>
	</div>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
