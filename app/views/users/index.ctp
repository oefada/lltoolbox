<?php
$this->set('hideSidebar', true);
?>

<?php if(isset($query)): 
$this->pageTitle = __('Users', true);
?>
<div id='users-index' class="users index">

<?php $html->addCrumb('Users'); ?>
<?php if(isset($query)) $query = ''; ?>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'users-index', 'showCount' => true))?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('UserSiteExtended.username');?></th>
	<th><?php echo $paginator->sort('# Tickets');?></th>
	<th><?php echo $paginator->sort('firstName');?></th>
	<th><?php echo $paginator->sort('lastName');?></th>
	<th><?php echo $paginator->sort('email');?></th>
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
			<?php echo $user['UserSiteExtended']['username']; ?>
		</td>
		
		<td>
			<?php echo count($user['Ticket']); ?>
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
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('action'=>'edit', $user['User']['userId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'users-index'))?>
</div>



<?php else: ?>

<b>User Search</b> (type in criteria at the top left of the page)
<ul>
	<li><a href="/tickets">Search Tickets</a></li>
	<li><a href="/bids">Search Bids</a></li>
</ul>

<br/>
<br/>

<b>Others</b>
<ul>
	<li><a href="/credit_trackings">Credit On File</a></li>
	<li><a href="/gift_cert_balances">Gift Certificates</a></li>
</ul>

<?php endif; ?>