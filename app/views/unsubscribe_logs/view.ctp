<div class="unsubscribeLogs view">
<h2><?php  __('UnsubscribeLog');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unsubscribeLog['UnsubscribeLog']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Email'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unsubscribeLog['UnsubscribeLog']['email']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SiteId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unsubscribeLog['UnsubscribeLog']['siteId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MailingId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unsubscribeLog['UnsubscribeLog']['mailingId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UnsubDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unsubscribeLog['UnsubscribeLog']['unsubDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SubDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $unsubscribeLog['UnsubscribeLog']['subDate']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit UnsubscribeLog', true), array('action' => 'edit', $unsubscribeLog['UnsubscribeLog']['Id'])); ?> </li>
		<li><?php echo $html->link(__('Delete UnsubscribeLog', true), array('action' => 'delete', $unsubscribeLog['UnsubscribeLog']['Id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $unsubscribeLog['UnsubscribeLog']['Id'])); ?> </li>
		<li><?php echo $html->link(__('List UnsubscribeLogs', true), array('action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New UnsubscribeLog', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
