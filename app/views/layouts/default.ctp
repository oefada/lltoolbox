<?php
/* SVN FILE: $Id$ */
/**
 * Three column layout.
 *
 * @filesource
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
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
		echo $html->css('datepicker');
		echo $html->css('prototip');
		
		if(isset($javascript)):
			echo $javascript->link('prototype');
			echo $javascript->link('scriptaculous/scriptaculous');
			echo $javascript->link('modalbox');
			echo $javascript->link('collapsible');
			echo $javascript->link('datepicker');
			echo $javascript->link('livepipe');
			echo $javascript->link('tabs');
			echo $javascript->link('prototip');
			//echo $javascript->link('fixed_table_headers');
		endif;

		echo $scripts_for_layout;
	?>
	<script type="text/javascript">
		var previousQueueCount = "";
		
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
		
		function startupEvents() {
			var queueCountUpdater = new Ajax.PeriodicalUpdater('queueCounter', '/message_queues/ajaxGetTotals', {
			  method: 'get', frequency: 5, decay: 2, onSuccess: function(){flashQueueCounter()}
			});
		}
		
		function flashQueueCounter() {
			if (previousQueueCount == "") {
				previousQueueCount = $('queueCounter').innerHTML;
				return true;
			}

			if (previousQueueCount != $('queueCounter').innerHTML) {
				//new Effect.Shake('queueCounter');
				previousQueueCount = $('queueCounter').innerHTML;
			}
		}
		
		Event.observe(window, 'load',
			function() { if($('flashMessage')) { new Effect.Highlight($('flashMessage')) };
			 				startupEvents();}
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
			<div id='user-header-details'>
				<?php if(@$userDetails['masquerading'] == true): ?>
					<strong><?=$userDetails['originalUser']['LdapUser']['samaccountname']?><br />
					Masquerading as <?=$userDetails['displayname']?><?=$html2->c($userDetails['samaccountname'])?><br />
					<a href="/sessions/masquerade/revert" style="color: #ddad00">Unmasquerade</a>
					</strong>
				<?php else: ?>
					<?=$userDetails['displayname']?><?=$html2->c($userDetails['samaccountname'])?>
				<?php endif; ?>
			</div>
        </div> <!-- /#logo-title -->
    </div></div> <!-- /#header-inner, /#header -->

    <div id="main">
	<div id="main-inner" class="clear-block<?php if(!isset($this->viewVars['hideSidebar']) || $this->viewVars['hideSidebar'] === false) { echo ' sidebar-left';}?>">
		<div id="navbar"><div id="navbar-inner" class="region">
		    <div id="mainNav">
				<ul>
					<?if(0):?>
		      		<li<?if(@($this->viewVars['currentTab'] == 'home')) echo ' class="current"'?>><?=$html->link('Home', '/')?></li>
					<?endif;?>
					<li<?if(@($this->viewVars['currentTab'] == 'message_queue')) echo ' class="current"'?>><?=$html->link('My Queue (<span id="queueCounter">'.$queueCountUnread.', '.$queueCountSeverity.'</span>)', array('controller' => 'message_queues', 'action' => 'index'), array(), null, false)?></li>
		      		<li<?if(@($this->viewVars['currentTab'] == 'property')) echo ' class="current"'?>><?=$html->link('Clients', array('controller' => 'clients', 'action' => 'index'))?></li>
		      		<li<?if(@($this->viewVars['currentTab'] == 'siteMerchandising')) echo ' class="current"'?>><a href="/pages/legacytools">Site Merchandising</a></li>
		      		<li<?if(@($this->viewVars['currentTab'] == 'reports')) echo ' class="current"'?>><?=$html->link('Reports', array('controller' => 'reports', 'action' => 'index'))?></li>
		      		<li<?if(@($this->viewVars['currentTab'] == 'customers')) echo ' class="current"'?>><?=$html->link('Concierge', array('controller' => 'users', 'action' => 'index'))?></li>
					<li style="float: right; width: auto; background: none"><a href="/logout" style="background: none">Logout</a></li>
		    	</ul>
			</div>
		</div></div> <!-- /#navbar-inner, /#navbar -->
		<? if(strlen($this->renderElement('search')) != 0): ?>
			<div id="page-toolbar"><div id="page-toolbar-inner" class="page-toolbar clearfix">
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
			</div></div>
			<? endif; ?>
			<div class='title-header'>
				<h1 class="title"><?php echo $this->pageTitle; ?></h1>
			</div>

      <div id="content"><div id="content-inner">
          <div id="content-header">
			<div id='loader' style='display: none; text-align: center;'><?=$html->image('ajax-loader.gif')?></div>
			<div id='spinner' style='display: none;'><?=$html->image('spinner_small.gif', array('align' => 'top'))?> Loading...</div>
			<?php if(false)://$html->getCrumbs()): ?>
			<div id="breadcrumbs"><?= $html->getCrumbs("<span></span>", "Dashboard"); ?></div>
			<?php endif; ?>

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
	  <?php if(!isset($this->viewVars['hideSidebar']) || $this->viewVars['hideSidebar'] === false): ?>
		<div id="sidebar-left"><div id="sidebar-left-inner" class="region region-left">
			<?php if(file_exists(ELEMENTS.'/sidebar/menu_for_'.$this->params['controller'].'_'.$this->params['action'].'.ctp')): ?>
				<?php echo $this->renderElement('sidebar/menu_for_'.$$this->params['controller'].'_'.$this->params['action']); ?>
			<?php elseif(file_exists(ELEMENTS.'/sidebar/menu_for_'.$this->params['controller'].'.ctp')): ?>
				<?php echo $this->renderElement('sidebar/menu_for_'.$this->params['controller']); ?>
			<?php elseif (isset($this->viewVars['currentTab']) && file_exists(ELEMENTS.'/sidebar/menu_for_'.$this->viewVars['currentTab'].'.ctp')): ?>
				<?php echo $this->renderElement('sidebar/menu_for_'.$this->viewVars['currentTab']); ?>
			<?php endif; ?>
		<div style="clear: both;"></div>
		</div></div> <!-- /#sidebar-left-inner, /#sidebar-left -->
	  <?php endif;?>

    </div></div> <!-- /#main-inner, /#main -->

      <div id="footer"><div id="footer-inner" class="region region-footer">

          <div id="footer-message"><? if (REVISION) { ?><strong>Running SVN Revision <?=REVISION?></strong><? } ?></div>

      </div></div> <!-- /#footer-inner, /#footer -->

  </div></div> <!-- /#page-inner, /#page -->
	
<?php echo $cakeDebug; ?>
</body>
</html>