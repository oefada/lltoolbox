<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" class="yui3-cssbase">
	<head>
		<?php echo $html->charset(); ?>
		<title><?php __('CSS Tool:'); ?>
			<?php echo strip_tags($title_for_layout); ?></title>
		<?php
		echo $html->meta('icon');
		echo $html->css('//yui.yahooapis.com/combo?3.6.0/build/cssfonts/cssfonts-min.css&3.6.0/build/cssreset/cssreset-min.css&3.6.0/build/cssbase/cssbase-min.css');
		echo $html->css('cstool');
		echo $html->css('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.21/themes/blitzer/jquery-ui.css');
		echo $javascript->link('//ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js');
		echo $javascript->link('//ajax.googleapis.com/ajax/libs/jqueryui/1.8.23/jquery-ui.min.js');
		// echo $scripts_for_layout;
		?>
	</head>
	<body>
		<div id="cstool">
			<div id="csheader">
				<?php echo $this->pageTitle; ?>
			</div>
			<div id="cscontent">
				<?php echo $content_for_layout; ?>
			</div>
			<div id="csfooter">
			</div>
		</div>
		<div id="csdebug">
			<?php echo $cakeDebug; ?>
		</div>
	</body>
</html>
