<div class="mailingSections form">
<?php echo $form->create('MailingSection');?>
	<fieldset>
 		<legend><?php __('Add MailingSection');?></legend>
	<?php
		echo $form->input('mailingTypeId');
		echo $form->input('mailingTypeName');
		echo $form->input('loaFulfillment');
		echo $form->input('maxInsertions');
		echo $form->input('sortOrder');
		echo $form->input('owner');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List MailingSections', true), array('action' => 'index'));?></li>
	</ul>
</div>
