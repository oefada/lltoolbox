<div class="ppvNotices view">
<h2><?php  __('Ppv Notice');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PpvNoticeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['ppvNoticeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Ppv Notice Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>/ 
			<?php echo $ppvNotice['PpvNoticeType']['ppvNoticeTypeName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Ticket'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['ticketId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('To'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['emailTo']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('From'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['emailFrom']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cc'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['emailCc']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('DateSent'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['emailSentDatetime']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Subject'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $ppvNotice['PpvNotice']['emailSubject']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<br />
<hr />
<div style="margin-top: 10px;">
<?php include('../../vendors/email_msgs/toolbox_sent_messages/' . $ppvNotice['PpvNotice']['emailBodyFileName']);?>
</div>