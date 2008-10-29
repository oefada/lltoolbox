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
		<?php __('LL webtools:'); ?>
		<?php echo strip_tags($title_for_layout) ?>
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
			} else if($('closeModalboxNoReload')) {
				// only hide the modal box, no refreshing of the current page
				Modalbox.hide();
			} else {
				// resize to content (in case of validation error messages)
				Modalbox.resizeToContent()
			}
			return true;
		}
		Event.observe(window, 'load',
			function() { if($('flashMessage')) { new Effect.Highlight($('flashMessage')) }; }
		);

	</script>
	<script type="text/javascript"><?php /* Needed to avoid Flash of Unstyled Content in IE */ ?> </script>
</head>
<body>
  <div id="page"><div id="page-inner">
	<div id="history-bar"><div id="history-bar-inner" class="clear-block">
		<strong>Last Viewed:</strong> <?=$html->link('Test 1', '#');?> | <?=$html->link('Test 2', '#');?> | <?=$html->link('Test 3', '#');?>
	</div></div>
    <div id="header"><div id="header-inner" class="clear-block">

        <div id="logo-title">

            <div id="logo"><img src="/img/sm_logo.png" alt="" id="logo-image" /></div>

        </div> <!-- /#logo-title -->
    </div></div> <!-- /#header-inner, /#header -->

    <div id="main">
	<div id="main-inner" class="clear-block<?php if(!isset($this->viewVars['hideSidebar']) || $this->viewVars['hideSidebar'] === false) { echo ' sidebar-left';}?>">
		<div id="navbar"><div id="navbar-inner" class="region">
		    <div id="mainNav">
				<ul>
		      		<li<?if(@($this->currentTab == 'home')) echo ' class="current"'?>><?=$html->link('Home', '/')?></li>
		      		<li<?if(@($this->viewVars['currentTab'] == 'property')) echo ' class="current"'?>><?=$html->link('Property', array('controller' => 'clients'))?></li>
		      		<li<?if(@($this->currentTab == 'siteMerchandising')) echo ' class="current"'?>><a href="#">Site Merchandising</a></li>
		      		<li<?if(@($this->currentTab == 'reports')) echo ' class="current"'?>><a href="#">Reports</a></li>
		      		<li<?if(@($this->currentTab == 'customers')) echo ' class="current"'?>><?=$html->link('Customers', array('controller' => 'users'))?></li>
					<li style="float: right; width: auto; background: none"><a href="#" style="background: none">Logout</a></li>
		    	</ul>
			</div>
		</div></div> <!-- /#navbar-inner, /#navbar -->
			<div id="page-toolbar"><div id="page-toolbar-inner" class="page-toolbar clearfix">
				<? if($this->renderElement('search')): ?>
				<table>
					<tr>
						<td class="search-bar"><?php echo $this->renderElement('search')?></td>
						<td class='button-bar'>
							<?php if (isset($layout)): ?>
								<div class="buttons"><? $layout->output($toolbar_for_layout);?></div>
							<?php endif ?>
						</td>
					</tr>
				</table>
				<? endif; ?>
			</div></div>
			<div class='title-header'>
				<h1 class="title"><?php echo $this->pageTitle; ?></h1>
			</div>

      <div id="content"><div id="content-inner">
          <div id="content-header">
			<div id='loader' style='display: none; text-align: center;'><?=$html->image('ajax-loader.gif')?></div>
			<div id='spinner' style='display: none; text-align: center;'><?=$html->image('spinner.gif')?></div>
			<?php if(false)://$html->getCrumbs()): ?>
			<div id="breadcrumbs"><?= $html->getCrumbs("<span></span>", "Dashboard"); ?></div>
			<?php endif; ?>

            <?php $session->flash(); ?>
          </div> <!-- /#content-header -->

        <div id="content-area" style="padding-top: 10px">
          <?php print $content_for_layout; ?>
        </div>
      </div></div> <!-- /#content-inner, /#content -->
	  <?php if(!isset($this->viewVars['hideSidebar']) || $this->viewVars['hideSidebar'] === false): ?>
		<div id="sidebar-left"><div id="sidebar-left-inner" class="region region-left">
			<?php if (isset($this->viewVars['currentTab']) && file_exists(ELEMENTS.'/sidebar/menu_for_'.$this->viewVars['currentTab'].'.ctp')): ?>
				<?php echo $this->renderElement('sidebar/menu_for_'.$this->viewVars['currentTab']); ?>
			<?php endif; ?>
	  <?php endif;?>
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