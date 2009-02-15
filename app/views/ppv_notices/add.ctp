<div class="ppvNotices form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'ppvNotices/add/' . $this->params['id']))); ?>
<?php echo $javascript->link('tiny_mce/tiny_mce.js');?>

<script type="text/javascript">
    tinyMCE.init({
        theme : "advanced",
        mode : "textareas",
        convert_urls : false,
        plugins : "fullpage",
		theme_advanced_buttons3_add : "fullpage"
    });
</script> 

	<fieldset>
 		<legend><?php __('Send Notification / PPV');?></legend>
	<?php
		echo $form->input('ppvNoticeTypeId', array('disabled' => 'disabled'));
		echo $form->input('ppvNoticeTypeId', array('type' => 'hidden'));
		echo $form->input('ticketId', array('readonly'=>'readonly'));
	?>
		<div style="text-align:right;"><?php echo $form->submit('Send');?></div>
		<textarea id="PpvNoticeEmailBody" name="data[PpvNotice][emailBody]" cols="140" rows="30" style="width:100%;"><?php echo $ppv_body_text;?></textarea>
	</fieldset>
<?php echo $form->end();?>
</div>
