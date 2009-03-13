<div class="ppvNotices form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'ppvNotices/add/' . $this->params['id'] . $clientIdParam  ))); ?>
<?php echo $javascript->link('tiny_mce/tiny_mce.js');?>

<script type="text/javascript">
    tinyMCE.init({
        theme : "advanced",
        mode : "textareas",
        convert_urls : false,
        theme_advanced_toolbar_location : "top",
    	theme_advanced_toolbar_align : "left",
        theme_advanced_text_colors : "CC0000,1d54e1",
		theme_advanced_buttons3_add : "fullpage, forecolor"
    });
</script> 

	<fieldset>
 		<legend><?php __('Send Notification / PPV');?></legend>
	<?php
		echo $form->input('ppvNoticeTypeId', array('disabled' => 'disabled'));
		echo $form->input('ppvNoticeTypeId', array('type' => 'hidden'));
		echo $form->input('ticketId', array('readonly'=>'readonly'));
	?>
		<?php if ($promo) :?>
			<h3 style="margin:20px;padding:0px;">** This ticket is associated with PROMO CODE  **</h3>
		<?php endif; ?>
		<div style="text-align:right;margin:0px;padding:0px;"><?php echo $form->submit('Send');?></div>
		<textarea id="PpvNoticeEmailBody" name="data[PpvNotice][emailBody]" cols="140" rows="30" style="width:100%;"><?php echo $ppv_body_text;?></textarea>
	</fieldset>
<?php echo $form->end();?>
</div>
