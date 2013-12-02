<div class="user">
<?php echo $form->create('Session', array('controller' => 'session', 'action' => 'login'));?>
	<?php
		echo $form->input('AdminUser.username', array('label' => 'Username'));
		echo $form->input('AdminUser.password');
	?>
<?php echo $form->end('Sign In');?>
</div>
<?php  echo $javascript->codeBlock('Event.observe(window, "load", function() {$("AdminUserUsername").focus(); new Effect.Shake($("authMessage"))})'); ?>