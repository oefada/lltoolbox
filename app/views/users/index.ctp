<?php
$this->set('hideSidebar', true);
?>

<?php if(isset($query)): 

$this->pageTitle = __('Users', true);
echo '<div id="users-index" class="users index">';
$html->addCrumb('Users'); ?>
<?php if(isset($query)) $query = ''; ?>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'users-index', 'showCount' => true))?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('userId');?></th>
	<th><?php echo $paginator->sort('Username','UserSiteExtended.username');?></th>
	<th><?php echo $paginator->sort('Tickets','ticketCount');?></th>
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
			<?
			if (isset($user['ticket']) && $user['ticket']['ticketId']!=''){	
				echo $html->link(($user[0]['ticketCount']), '/tickets/?searchUserId='.$user['User']['userId']); 
			}else{
				echo '0';
			}
			?>
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
<?php $this->pageTitle = 'Users' ?>
<div class="userTools">
	<ul class="treed">
		<li class="file"><a href="/tickets">Search Tickets</a></li>
		<li class="file"><a href="/bids">Search Bids</a></li>
		<li class="file"><a href="/credit_trackings">Credit On File</a></li>
		<li class="file"><a href="/gift_cert_balances">Gift Certificates</a></li>
	</ul>
</div>
<?php endif; ?>
