<script type="text/javascript" src="/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
tinyMCE.init({
	mode : "textareas",
	theme : "simple",
	init_instance_callback : 'resizeEditorBox',
	auto_resize : true
});
</script>
<div class="presses form">
<?php echo $form->create('Press');?>
	<fieldset>
 		<legend><?php __('Add Press');?></legend>
	<?php
		echo $form->input('pressTypeId');
		echo $form->input('clientId');
		echo $form->input('pressTitle');
		echo $form->input('pressDesc');
		echo $form->input('pressDate');
	?>
	<div class="controlset">
		<? echo $form->input('inactive'); ?>
	</div>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>