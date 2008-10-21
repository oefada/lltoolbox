<div class="ppvNotices view">
<h2><?php  __('PpvNotice');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PpvNoticeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['ppvNoticeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Ppv Notice Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($ppvNotice['PpvNoticeType']['ppvNoticeTypeName'], array('controller'=> 'ppv_notice_types', 'action'=>'view', $ppvNotice['PpvNoticeType']['ppvNoticeTypeId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Ticket'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($ppvNotice['Ticket']['ticketId'], array('controller'=> 'tickets', 'action'=>'view', $ppvNotice['Ticket']['ticketId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('To'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['to']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('From'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['from']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cc'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['cc']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Body'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['body']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateSent'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['dateSent']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Subject'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['subject']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit PpvNotice', true), array('action'=>'edit', $ppvNotice['PpvNotice']['ppvNoticeId'])); ?> </li>
		<li><?php echo $html->link(__('Delete PpvNotice', true), array('action'=>'delete', $ppvNotice['PpvNotice']['ppvNoticeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ppvNotice['PpvNotice']['ppvNoticeId'])); ?> </li>
		<li><?php echo $html->link(__('List PpvNotices', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New PpvNotice', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tickets', true), array('controller'=> 'tickets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ticket', true), array('controller'=> 'tickets', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ppv Notice Types', true), array('controller'=> 'ppv_notice_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ppv Notice Type', true), array('controller'=> 'ppv_notice_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
