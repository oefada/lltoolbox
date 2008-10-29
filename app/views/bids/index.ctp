<?php $this->pageTitle = 'Bids';
if(!isset($query)) $query = '';?>
<div id='bids-index' class="bids index">
	<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'bids-index', 'showCount' => true)); ?>
	
	<?php if (isset($query) && !empty($query)): ?>
		<div style="clear: both">
		<strong>Search Criteria:</strong> <?php echo $query; ?>
		</div>
	<?php endif ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('bidId');?></th>
	<th><?php echo $paginator->sort('offerId');?></th>
	<th><?php echo $paginator->sort('User', 'userId');?></th>
	<th><?php echo $paginator->sort('bidDateTime');?></th>
	<th><?php echo $paginator->sort('bidAmount');?></th>
	<th><?php echo $paginator->sort('autoRebid');?></th>
	<th><?php echo $paginator->sort('maxBid');?></th>
	<th><?php echo $paginator->sort('bidInactive');?></th>
	<th><?php echo $paginator->sort('note');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($bids as $bid):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $text->highlight($bid['Bid']['bidId'], $query); ?>
		</td>
		<td>
			<?php echo $text->highlight($bid['Bid']['offerId'], $query); ?>
		</td>
		<td>
			<?php echo $text->highlight($bid['User']['firstName'].' '.$bid['User']['lastName'], $query); ?> <?php echo $text->highlight($html2->c($bid['Bid']['userId'], 'User Id:'), $query); ?>
		</td>
		<td>
			<?php echo $bid['Bid']['bidDateTime']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['bidAmount']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['autoRebid']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['maxBid']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['bidInactive']; ?>
		</td>
		<td>
			<?php echo $bid['Bid']['note']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $bid['Bid']['bidId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'client-index', 'showCount' => true)); ?>
</div>
