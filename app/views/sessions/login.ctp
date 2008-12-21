<div class="user">
<?php echo $form->create('Session', array('action' => 'login'));?>
	<?php
		echo $form->input('LdapUser.samaccountname', array('label' => 'Username'));
		echo $form->input('LdapUser.pass', array('type' => 'password'));
		echo $form->input('LdapUser.password', array('type' => 'hidden'));
	?>
<?php echo $form->end('Sign In');?>
</div>
<?php echo $javascript->codeBlock('Event.observe(window, "load", function() {$("LdapUserUsername").focus(); new Effect.Shake($("authMessage"))})'); ?>