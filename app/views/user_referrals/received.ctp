
<?php $this->pageTitle = 'Referrals'; ?>


<h2>Referrals sent to <?=$user['User']['email'];?></h2>
Apply credit to <?=$user['User']['email'];?> for registering through a referral.

<div class="bids index referrals-index" style="margin-bottom: 40px;">
	<?php echo $this->renderElement('ajax_paginator', array('showCount' => true, 'form' => '/users/referrals/a')); ?>

	<table cellpadding="0" cellspacing="0">
	
		<tr>
			<th><?php echo $paginator->sort('Referrer', 'referredEmail');?></th>
			<th><?php echo $paginator->sort('Referral Status', 'statusTypeId');?></th>
			<th><?php echo $paginator->sort('Apply Credit', 'hasPurchase');?></th>
		</tr>
	
	
	<?php
	$i = 0;
	foreach ($referralsRecvd as $r):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	
		<tr <?php echo $class;?>>
			<td>
				<a href="/users/view/<?=$r['User']['userId'];?>"><?=$r['User']['userId'];?></a> - <?php echo $r['User']['email']; ?>
			</td>
			<td>
				<?php
					switch ($r['UserReferrals']['statusTypeId']) {
						case '1':
							echo 'No Response';
							break;
						case '2':
							echo 'Accepted';
							break;
						case '3':
							echo 'Purchased';
							break;
					}
				?>
			</td>
			<td>
				<?php 
					if ($r['UserReferrals']['statusTypeId'] == 1) {
						echo '<a href="/userReferrals/completereferral/' . $r['UserReferrals']['id'] . '/2">Apply Credit</a>';
					}
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	
	</table>
</div>

