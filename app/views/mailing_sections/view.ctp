<div class="mailingSections view">
<h2><?php  __('MailingSection');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MailingSectionId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailingSection['MailingSection']['mailingSectionId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MailingTypeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailingSection['MailingSection']['mailingTypeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MailingTypeName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailingSection['MailingSection']['mailingTypeName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaFulfillment'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailingSection['MailingSection']['loaFulfillment']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MaxInsertions'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailingSection['MailingSection']['maxInsertions']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SortOrder'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailingSection['MailingSection']['sortOrder']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Owner'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailingSection['MailingSection']['owner']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit MailingSection', true), array('action' => 'edit', $mailingSection['MailingSection']['mailingSectionId'])); ?> </li>
		<li><?php echo $html->link(__('Delete MailingSection', true), array('action' => 'delete', $mailingSection['MailingSection']['mailingSectionId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $mailingSection['MailingSection']['mailingSectionId'])); ?> </li>
		<li><?php echo $html->link(__('List MailingSections', true), array('action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New MailingSection', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
