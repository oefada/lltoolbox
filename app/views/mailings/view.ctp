<div class="mailings view">
<h2><?php  __('Mailing');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MailingId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailing['Mailing']['mailingId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MailingTypeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailing['Mailing']['mailingTypeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MailingDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $mailing['Mailing']['mailingDate']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Mailing', true), array('action' => 'edit', $mailing['Mailing']['mailingId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Mailing', true), array('action' => 'delete', $mailing['Mailing']['mailingId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $mailing['Mailing']['mailingId'])); ?> </li>
		<li><?php echo $html->link(__('List Mailings', true), array('action' => 'index')); ?> </li>
		<li><?php echo $html->link(__('New Mailing', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
