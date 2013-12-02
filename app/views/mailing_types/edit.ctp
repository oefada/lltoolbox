<div class="mailingTypes form">
<?php echo $form->create('MailingType');?>
	<fieldset>
 		<legend><?php __('Edit MailingType');?></legend>
	<?php
		echo $form->input('mailingTypeId');
		echo $form->input('mailingTypeName');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action' => 'delete', $form->value('MailingType.mailingTypeId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('MailingType.mailingTypeId'))); ?></li>
		<li><?php echo $html->link(__('List MailingTypes', true), array('action' => 'index'));?></li>
	</ul>
</div>
