<?php
/* SVN FILE: $Id: default.ctp 1851 2009-08-15 00:20:34Z vgarcia $ */
/**
 * Three column layout.
 *
 * @filesource
 * @version			$Revision: 1851 $
 * @modifiedby		$LastChangedBy: vgarcia $
 * @lastmodified	$Date: 2009-08-14 17:20:34 -0700 (Fri, 14 Aug 2009) $
 */
?>
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

		echo $html->css('main');
		echo $html->css('print', null, array('media' => 'print'));
        echo $html->css('pepper-grinder/jquery-ui-1.7.2.custom');
		
		if(isset($javascript)):
            echo $javascript->link('jquery/jquery');
            echo $javascript->link('jquery/jquery-ui-1.7.2.custom.min');
		endif;

		echo $scripts_for_layout;
	?>
	<script type="text/javascript"><?php /* Needed to avoid Flash of Unstyled Content in IE */ ?> </script>
</head>
<body>
  <div id="page"><div id="page-inner">

    <div id="main">
	<div id="main-inner" class="clear-block">
      <div id="content"><div id="content-inner">
          <div id="content-header">
			<div id='loader' style='display: none; text-align: center;'><?=$html->image('ajax-loader.gif')?></div>
			<div id='spinner' style='display: none;'><?=$html->image('spinner_small.gif', array('align' => 'top'))?> Loading...</div>
			

            <?php $session->flash();
			$session->flash('error');
			$session->flash('success');
			$session->flash('auth');
			?>

          </div> <!-- /#content-header -->

        <div id="content-area" style="padding-top: 10px">
          <?php print $content_for_layout; ?>
        </div>
      </div></div> <!-- /#content-inner, /#content -->
	  

    </div></div> <!-- /#main-inner, /#main -->

  </div></div> <!-- /#page-inner, /#page -->
	
<?php echo $cakeDebug; ?>
</body>
</html>