<style>
    input#MailingMailingDate {
        margin-right:6px;
    }
</style>

<div class="mailings form">
<?php echo $form->create('Mailing');?>
	<fieldset>
 		<legend><?php __('Add Mailing');?></legend>
	<?php 
		echo $form->input('mailingTypeId', array('options' => $mailingTypes, 'label' => 'Mailing Type'));
        echo $datePicker->picker('mailingDate', array('label' => 'Select Date'));
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
