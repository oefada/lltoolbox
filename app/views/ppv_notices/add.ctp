<div class="ppvNotices form">
<?php echo $form->create('PpvNotice');?>
	<fieldset>
 		<legend><?php __('Add PpvNotice');?></legend>
	<?php
		echo $form->input('ppvNoticeTypeId');
		echo $form->input('ticketId', array('readonly'=>'readonly'));
		echo $form->input('emailTo');
		echo $form->input('emailFrom');
		echo $form->input('emailCc');
		echo $form->input('emailSubject');
		echo $form->input('emailBody');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
