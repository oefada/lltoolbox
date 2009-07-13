<div class="clientReviews view">
<h2><?php  __('ClientReview');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ClientReviewId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $clientReview['ClientReview']['clientReviewId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Client'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($clientReview['Client']['name'], array('controller'=> 'clients', 'action'=>'view', $clientReview['Client']['clientId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($clientReview['User']['title'], array('controller'=> 'users', 'action'=>'view', $clientReview['User']['userId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReviewTitle'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $clientReview['ClientReview']['reviewTitle']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReviewBody'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $clientReview['ClientReview']['reviewBody']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Datetime'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $clientReview['ClientReview']['datetime']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $clientReview['ClientReview']['status']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Inactive'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $clientReview['ClientReview']['inactive']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit ClientReview', true), array('action'=>'edit', $clientReview['ClientReview']['clientReviewId'])); ?> </li>
		<li><?php echo $html->link(__('Delete ClientReview', true), array('action'=>'delete', $clientReview['ClientReview']['clientReviewId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $clientReview['ClientReview']['clientReviewId'])); ?> </li>
		<li><?php echo $html->link(__('List ClientReviews', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New ClientReview', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Users', true), array('controller'=> 'users', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller'=> 'users', 'action'=>'add')); ?> </li>
	</ul>
</div>
