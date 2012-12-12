<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php __('LL Toolbox:'); ?>
		<?php echo strip_tags($title_for_layout) ?>
	</title>
	<?php
	echo $html->meta('icon');
	echo $scripts_for_layout;
	?>
	<script type="text/javascript"><?php /* Needed to avoid Flash of Unstyled Content in IE */ ?></script>
</head>
<body>
<div id="main"><?php print $content_for_layout; ?></div>
<?php echo $cakeDebug; ?>
</body>
</html>
