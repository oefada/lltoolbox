<div class="mailingTypes form">
<?php echo $form->create('MailingType');?>
	<fieldset>
 		<legend><?php __('Add MailingType');?></legend>
	<?php
		echo $form->input('mailingTypeName');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List MailingTypes', true), array('action' => 'index'));?></li>
	</ul>
</div>
