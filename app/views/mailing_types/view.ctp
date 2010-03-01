<div class="mailingTypes view">
<h2><?php  __('MailingType');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MailingTypeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailingType['MailingType']['mailingTypeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MailingTypeName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailingType['MailingType']['mailingTypeName']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit MailingType', true), array('action' => 'edit', $mailingType['MailingType']['mailingTypeId'])); ?> </li>
		<li><?php echo $html->link(__('Delete MailingType', true), array('action' => 'delete', $mailingType['MailingType']['mailingTypeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $mailingType['MailingType']['mailingTypeId'])); ?> </li>
		<li><?php echo $html->link(__('List MailingTypes', true), array('action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New MailingType', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
