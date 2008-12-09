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
	</fieldset>
	<?php echo $form->input('emailBody', array('label' => '', 'cols' => '150', 'rows' => '30'));?>
<?php echo $form->end('Submit');?>
</div>
