<div class="unsubscribeLogs form">
<?php echo $form->create('UnsubscribeLog');?>
	<fieldset>
 		<legend><?php __('Add UnsubscribeLog');?></legend>
	<?php
		echo $form->input('id');
		echo $form->input('email');
		echo $form->input('siteId');
		echo $form->input('mailingId');
		echo $form->input('unsubDate');
		echo $form->input('subDate');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List UnsubscribeLogs', true), array('action' => 'index'));?></li>
	</ul>
</div>
