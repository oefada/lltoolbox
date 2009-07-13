<div class="clientReviews index">
<h2><?php __('ClientReviews');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('clientReviewId');?></th>
	<th><?php echo $paginator->sort('clientId');?></th>
	<th><?php echo $paginator->sort('authorUserId');?></th>
	<th><?php echo $paginator->sort('reviewTitle');?></th>
	<th><?php echo $paginator->sort('reviewBody');?></th>
	<th><?php echo $paginator->sort('datetime');?></th>
	<th><?php echo $paginator->sort('status');?></th>
	<th><?php echo $paginator->sort('inactive');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($clientReviews as $clientReview):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $clientReview['ClientReview']['clientReviewId']; ?>
		</td>
		<td>
			<?php echo $html->link($clientReview['Client']['name'], array('controller'=> 'clients', 'action'=>'view', $clientReview['Client']['clientId'])); ?>
		</td>
		<td>
			<?php echo $html->link($clientReview['User']['title'], array('controller'=> 'users', 'action'=>'view', $clientReview['User']['userId'])); ?>
		</td>
		<td>
			<?php echo $clientReview['ClientReview']['reviewTitle']; ?>
		</td>
		<td>
			<?php echo $clientReview['ClientReview']['reviewBody']; ?>
		</td>
		<td>
			<?php echo $clientReview['ClientReview']['datetime']; ?>
		</td>
		<td>
			<?php echo $clientReview['ClientReview']['status']; ?>
		</td>
		<td>
			<?php echo $clientReview['ClientReview']['inactive']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $clientReview['ClientReview']['clientReviewId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $clientReview['ClientReview']['clientReviewId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $clientReview['ClientReview']['clientReviewId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $clientReview['ClientReview']['clientReviewId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New ClientReview', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Users', true), array('controller'=> 'users', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller'=> 'users', 'action'=>'add')); ?> </li>
	</ul>
</div>
