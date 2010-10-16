<div class="ppvNotices form">
<?php echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'ppvNotices/add/' . $this->params['id'] . $clientIdParam  ), 'enctype' => 'multipart/form-data' )); ?>
<?php echo $javascript->link('tiny_mce/tiny_mce.js');?>

<script type="text/javascript">
    tinyMCE.init({
        theme : "advanced",
        mode : "textareas",
        convert_urls : false,
		force_br_newlines : true,
		forced_root_block : '',
        theme_advanced_toolbar_location : "top",
    	theme_advanced_toolbar_align : "left",
        theme_advanced_text_colors : "CC0000,1d54e1",
		theme_advanced_buttons3_add : "fullpage, forecolor"
    });
    
    //for hotel beds, will make it more dynamic
    //function insertAfter( referenceNode, newNode )
//		{
//			referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
//		}
//		
//	function addNewIF(obj,num)
//    	{
//    		
//            var $i = parseInt(num) + 1;
//            
//            var newIF = document.createElement('input');
//            newIF.type = 'file';
//            newIF.name = 'attachFile[]';
//            newIF.size = '75';
//    		insertAfter( obj, newIF );
//    	}
//    
//    function getFileName(val,obj,num) {
//
//        var attachedFiles = document.getElementById('listAttachedFiles').innerHTML;
//        var attachFile = val;
//        document.getElementById('listAttachedFiles').innerHTML = (attachedFiles)? attachedFiles+'<br />'+attachFile : attachFile;
//        addNewIF(obj,num);                 
//    }
    
</script> 

	<fieldset>
 		<legend><?php __('Send Notification / PPV');?></legend>
	<?php
		echo $form->input('ppvNoticeTypeId', array('disabled' => 'disabled'));
		echo $form->input('ppvNoticeTypeId', array('type' => 'hidden'));
		echo $form->input('ticketId', array('readonly'=>'readonly'));
		echo $form->input('emailTo');
		echo $form->input('emailCc');
		if (isset($editSubject) && $editSubject) {
			echo $form->input('emailSubject', array('value'=>'FILL IN SUBJECT LINE HERE!!!!!!!!!!!'));
		}
        
        //hb attachments, will make it more dynamic     
        
        echo $form->input('emailAttachments', array('type' => 'file', 'name' => 'attach1', 'size' => '75'));
        echo $form->input('emailAttachments', array('type' => 'file', 'name' => 'attach2', 'size' => '75'));
        echo $form->input('emailAttachments', array('type' => 'file', 'name' => 'attach3', 'size' => '75'));
        echo $form->input('emailAttachments', array('type' => 'file', 'name' => 'attach4', 'size' => '75'));
        
       	//echo $form->input('emailAttachments', array('type' => 'hidden', 'readonly'=>'readonly'));
        //echo '<div class="input text"><div style="border: 1px solid #898989; width: 380px; padding: 10px; float: right; margin-right: 217px;" id="listAttachedFiles"></div><label for="PpvNoticeEmailTo">Email Attachment(s)</label> </div>';
//        echo $form->input('attachFile', array('type' => 'file', 'onchange' => 'javascript:getFileName(this.value,this,0);', 'size' => '75', 'name' => 'attachFile[]'));
//        echo '<div id="attachBorder"></div>';
//        //echo $form->input('emailAttachments', array('type' => 'file', 'name' => 'attach'));
////        echo $form->input('emailAttachments', array('type' => 'file', 'name' => 'attach'));
////        echo $form->input('emailAttachments', array('type' => 'file', 'name' => 'attach'));
        
		if (isset($isResConf) && $isResConf) {
			echo "<br /><br />";
			echo "<h2>Reservation Confirmation</h2>";
			echo "<h3 style='font-size:14px;'>";
			echo "Please double check the Confirmation #, Arrival Date, and Departure Date for this Reservation Confirmation Email.  If there is incorrect reservation information, ";
			echo "please <a href='/tickets/view/$ticketId'>click here</a> to back to the ticket screen to edit the Reservation info. Do not manually edit the email below.";
			echo "</h3>";
		}
	?>
		<?php if ($promo) :?>
			<h2 style="margin:20px;padding:0px;">** This ticket is associated with PROMO CODE  **</h2>

				<?php foreach ($promo as $t_promo) : ?>

					<h3 style="margin:0px;padding:0px;padding-bottom:5px;">** Promo Code [<?=$t_promo['pc']['promoCode'];?>] **</h3>

					<h3 style="margin:0px;padding:0px;padding-bottom:5px;">
						<?php if ($t_promo['p']['amountOff']) : ?>
						Amount Off: <?php echo $number->currency($t_promo['p']['amountOff']);?>
						<?php endif; ?>
						<?php if ($t_promo['p']['percentOff']) : ?>
						Percent Off: <?php echo $number->currency($t_promo['p']['percentOff']);?>
						<?php endif; ?>
					</h3>


				<?php endforeach; ?>

		<?php endif; ?>
		<?php if ((isset($isResConf) && $isResConf) && !$hasResData) :?>
			<div style="text-align:left;margin:0px;padding:5px;">
				<h3 style='font-size:14px;font-weight:bold;color:#000000'>
				** You cannot send out this email until you associated a reservation record to this ticket.
				 To add a record, <a href='/tickets/view/<?=$ticketId?>'>click here</a>. **
				</h3><br />
			</div>
		<?php else: ?>
			<div style="text-align:right;margin:0px;padding:0px;"><?php echo $form->submit('Send');?></div>
		<?php endif;?>
		<textarea id="PpvNoticeEmailBody" name="data[PpvNotice][emailBody]" cols="140" rows="30" style="width:100%;"><?php echo $ppv_body_text;?></textarea>
	</fieldset>
<?php echo $form->end();?>
</div>
