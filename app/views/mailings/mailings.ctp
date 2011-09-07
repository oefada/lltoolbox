<?php $this->pageTitle = 'Newsletter Scheduler'; ?>

<div class="mailings index">
<h2><?php __('Mailings');?></h2>
<p>
Tools: <a href="/mailing_types">Configure Newsletter Types</a> | <a href="/mailings/add">Create Newsletter Mailing</a>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('Mailing ID', 'mailingId');?></th>
	<th><?php echo $paginator->sort('Mailing Type', 'mailingTypeId');?></th>
	<th><?php echo $paginator->sort('Mailing Date', 'mailingDate');?></th>
    <th>Available Slots</th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($mailings as $mailing):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $mailing['Mailing']['mailingId']; ?>
		</td>
		<td>
			<?php echo $mailing['MailingType']['mailingTypeName']; ?>
		</td>
		<td>
			<?php echo $mailing['Mailing']['mailingDate']; ?>
		</td>
        <td>
            <?php foreach ($mailing['MailingSection'] as $mailingSectionName => $slots): ?>
                    <?php echo $mailingSectionName; ?>: <?php echo $slots['availableSlots']; ?><br />
            <?php endforeach; ?>
        </td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action' => 'edit', $mailing['Mailing']['mailingId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action' => 'delete', $mailing['Mailing']['mailingId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $mailing['Mailing']['mailingId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>

