<div class="messageView">
<div class='title'><?php echo $messageQueue['MessageQueue']['title']; ?></div>
<div class='messageModel'>
	<?php 
	if ($messageQueue['MessageQueue']['modelId']) {
		echo $messageQueue['MessageQueue']['model'];
		echo $html->c($messageQueue['MessageQueue']['modelId']);
	}
	?>
	<span class='timestamp'> on <?=date('D M j, Y h:i a', strtotime($messageQueue['MessageQueue']['created']))?></span>
</div>
	<?php echo $messageQueue['MessageQueue']['description']; ?>
</div>