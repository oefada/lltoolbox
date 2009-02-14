<?php
$this->pageTitle = __('Users', true);
$this->set('hideSidebar', true);
?>

<?php if(isset($query)): ?>
<div id='users-index' class="users index">

<?php $html->addCrumb('Users'); ?>
<?php if(isset($query)) $query = ''; ?>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'users-index', 'showCount' => true))?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('firstName');?></th>
	<th><?php echo $paginator->sort('lastName');?></th>
	<th><?php echo $paginator->sort('email');?></th>
	<th><?php echo $paginator->sort('inactive');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($users as $user):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $user['User']['userId']; ?>
		</td>

		<td>
			<?php echo $user['User']['firstName']; ?>
		</td>
		<td>
			<?php echo $user['User']['lastName']; ?>
		</td>
		<td>
			<?php echo $user['User']['email']; ?>
		</td>
		<td>
			<?php echo $user['User']['inactive']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('action'=>'edit', $user['User']['userId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'users-index'))?>
</div>
<?php else: ?>
User Search (type in criteria at the top left of the page)<br />
<a href="/tickets">Search Tickets</a><br />
<a href="/bids">Search Bids</a><br /><br />
<?php endif; ?>