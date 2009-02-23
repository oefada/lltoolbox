<div style="position:relative;margin-top: 10px;height:500px;overflow:auto;">
<?php 

if ($ppvNotice['PpvNotice']['emailBody']) {
	echo $ppvNotice['PpvNotice']['emailBody'];
} else 
	include('../vendors/email_msgs/toolbox_sent_messages/' . $ppvNotice['PpvNotice']['emailBodyFileName']);
}

?>
</div>
