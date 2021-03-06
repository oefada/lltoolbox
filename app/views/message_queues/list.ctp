<?php if (!@$read): ?>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true, 'divToPaginate' => 'unreadMessages')); ?>
<?php endif; ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th>&nbsp;</th>
	<th><?php echo $paginator->sort($html->image("flag_red.png"), 'severity', array('escape' => false, 'update' => 'unreadMessages'));?></th>
	<th><?php echo $paginator->sort($html->image("tag_blue.png"), 'model', array('escape' => false, 'update' => 'unreadMessages'));?></th>
	<th><?php echo $paginator->sort('Title', 'title', array('update' => 'unreadMessages'));?></th>
	<th><?php echo $paginator->sort('Date', 'created', array('update' => 'unreadMessages'));?></th>
</tr>
<?php
$i = 0;
foreach ($messages as $messageQueue):
	$class = null;
	$classes = array();
	if ($i++ % 2 == 0) {
		$classes[] = 'altrow';
	}
	
	$classes[] = ($messageQueue['MessageQueue']['read']) ? 'read' : 'unread';
	
	$classes[] = 'messageQueueMessage';
	
	$class = ' class="'.implode(' ', $classes).'"';
?>
	<tr<?php echo $class;?> id='message-<?=$messageQueue['MessageQueue']['messageQueueId']?>' onmouseover="rollover(1, this);" onclick="clickRow(<?=$messageQueue['MessageQueue']['messageQueueId']?>, this);" onmouseout="rollover(0, this);">
		<td style="text-align: center"><input type="checkbox" onclick="complete(this)" value="<?=$messageQueue['MessageQueue']['messageQueueId']?>" <? if(@$read) echo "checked='checked'" ?>/></td>
		<td style="text-align: center">
			<?php 
			if ($messageQueue['MessageQueue']['severity'] == 3) {
				echo $html->image("flag_red.png");
			} else if ($messageQueue['MessageQueue']['severity'] == 1) {
				echo $html->image("flag_blue.png");
			}
			?>
		</td>
		<td style="text-align: center">
			<?php echo $html->image("model_icons/".strtolower($messageQueue['MessageQueue']['model']).".png"); ?>
		</td>
		<td>
			<?php echo $messageQueue['MessageQueue']['title']; ?>
		</td>
		<td nowrap>
			<?php 
			$createdTimestamp = strtotime($messageQueue['MessageQueue']['created']);
			if (date('MjY') == date('MjY', $createdTimestamp))  {
				echo date('g:i a', $createdTimestamp);	
			} else if (date('Y') == date('Y', $createdTimestamp)) {
				echo date('M j', $createdTimestamp);	
			} else {
				echo date('n/j/y', $createdTimestamp);	
			}
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php if (!@$read): ?>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'unreadMessages')); ?>
<?php endif; ?>