<div class="user">
<?php echo $form->create('Session', array('action' => 'login'));?>
	<?php
		echo $form->input('LdapUser.username');
		echo $form->input('LdapUser.password');
		echo $form->input('LdapUser.blankPassword', array('type' => 'hidden'));
	?>
<?php echo $form->end('Sign In');?>
</div>
<?php echo $javascript->codeBlock('Event.observe(window, "load", function() {$("LdapUserUsername").focus(); new Effect.Shake($("authMessage"))})'); ?>