<?php
/* SVN FILE: $Id: default.ctp 9 2008-10-10 05:08:45Z vgarcia $ */
/**
 * Three column layout.
 *
 * @filesource
 * @version			$Revision: 9 $
 * @modifiedby		$LastChangedBy: vgarcia $
 * @lastmodified	$Date: 2008-10-09 22:08:45 -0700 (Thu, 09 Oct 2008) $
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php __('CakePHP: the rapid development php framework:'); ?>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $html->meta('icon');

		echo $html->css('cake.generic');
		echo $html->css('main');
		
		if(isset($javascript)):
			echo $javascript->link('prototype');
			echo $javascript->link('scriptaculous/scriptaculous');
			echo $javascript->link('modalbox');
			echo $javascript->link('collapsible');
		endif;

		echo $scripts_for_layout;
	?>
	<script type="text/javascript">
		function closeModalbox()
		{
			if ($('closeModalbox')) {
				// hide the modal box
				Modalbox.hide();
				// refresh the current page
				location.reload(true);
			} else {
				// resize to content (in case of validation error messages)
				Modalbox.resizeToContent()
			}
			return true;
		}
		Event.observe(window, 'load',
			function() { new Effect.Highlight($('flashMessage')); }
		);

	</script>
	<script type="text/javascript"><?php /* Needed to avoid Flash of Unstyled Content in IE */ ?> </script>
</head>
<body>
  <div id="page"><div id="page-inner">

    <div id="header"><div id="header-inner" class="clear-block">

        <div id="logo-title">

            <div id="logo"><img src="/img/sm_logo.gif" alt="" id="logo-image" /></div>

        </div> <!-- /#logo-title -->
    </div></div> <!-- /#header-inner, /#header -->

    <div id="main"><div id="main-inner" class="clear-block with-navbar sidebar-left">

      <div id="content"><div id="content-inner">
          <div id="content-header">
			<div class="title-header">
              <h1 class="title"><?php echo $this->pageTitle; ?></h1>
			<?php if (isset($layout)): ?>
				<div class="buttons"><? $layout->output($header_for_layout);?> </div>
			<?php endif ?>
			<div id='loader' style='display: none; text-align: center;'><?=$html->image('ajax-loader.gif')?></div>
			<div id='spinner' style='display: none; text-align: center;'><?=$html->image('spinner.gif')?></div>
			</div>
			<?php if(false)://$html->getCrumbs()): ?>
			<div id="breadcrumbs"><?= $html->getCrumbs("<span></span>", "Dashboard"); ?></div>
			<?php endif; ?>
			<div class="page-toolbar clearfix">
				<?php echo $this->renderElement('search')?>
				<?php if (isset($layout)): ?>
					<div class="buttons"><? $layout->output($toolbar_for_layout);?></div>
				<?php endif ?>
			</div>
            <?php $session->flash(); ?>
          </div> <!-- /#content-header -->

        <div id="content-area" style="padding-top: 10px">
          <?php print $content_for_layout; ?>
        </div>
      </div></div> <!-- /#content-inner, /#content -->

        <div id="navbar"><div id="navbar-inner" class="region region-navbar">

            <div id="recent-items-list">
				<?php //echo $history ?>
            </div> <!-- /#primary -->

          

        </div></div> <!-- /#navbar-inner, /#navbar -->

		<div id="sidebar-left"><div id="sidebar-left-inner" class="region region-left">
			<ul>
				<li><a href="#">Home</a></li>
				<li><a href="/clients">Clients</a></li>
				<li><a href="/products">Products</a></li>
				<li><a href="/offers">Offers</a></li>
				<li><a href="#">Promo</a></li>
			</ul>
		<?php if (false)://(isset($layout)): ?>
			<? $layout->output($sidebar_for_layout);?>
		<?php endif ?>
		<div style="clear: both;"></div>
        </div></div> <!-- /#sidebar-left-inner, /#sidebar-left -->

    </div></div> <!-- /#main-inner, /#main -->

      <div id="footer"><div id="footer-inner" class="region region-footer">

          <div id="footer-message"><? if (REVISION) { ?><strong>Running SVN Revision <?=REVISION?></strong><? } ?></div>

      </div></div> <!-- /#footer-inner, /#footer -->

  </div></div> <!-- /#page-inner, /#page -->
	
<?php echo $cakeDebug; ?>
</body>
</html>