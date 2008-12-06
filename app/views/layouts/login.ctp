<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php __('Toolbox'); ?>
	</title>
	<?php echo $html->css('login'); ?>
	<?php echo $javascript->link('prototype'); ?>
	<?php echo $javascript->link('scriptaculous/scriptaculous'); ?>
</head>
<body class="login">
	<div class="login">
		<h1 class='title'>Toolbox</h1>
		<div id="login_dialog" class="login_dialog">
			<?php
				$session->flash();
				$session->flash('auth');
			?>
			<?php echo $content_for_layout; ?>
		</div>
	</div>
</body>
</html>
