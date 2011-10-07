
<?php $this->pageTitle = 'Referrals'; ?>


<h2>Referrals sent by user</h2>
Apply credit to <?=$user['User']['email'];?> for a purchase made by someone they referred. Also applies credit to person referred for registering through a referral.

<div class="bids index referrals-index" style="margin-bottom: 40px;">
	<?php echo $this->renderElement('ajax_paginator', array('showCount' => true, 'form' => '/users/referrals/a')); ?>

	<table cellpadding="0" cellspacing="0">
	
		<tr>
			<th><?php echo $paginator->sort('Referred Email', 'referredEmail');?></th>
			<th><?php echo $paginator->sort('Referral Status', 'statusTypeId');?></th>
			<th><?php echo $paginator->sort('Apply Credit', 'hasPurchase');?></th>
		</tr>
	
	
	<?php
	$i = 0;
	foreach ($referralsSent as $r):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	
		<tr <?php echo $class;?>>
			<td>
				<?php echo $r['UserReferrals']['referredEmail']; ?>
			</td>
			<td>
				<?php
					switch ($r['UserReferrals']['statusTypeId']) {
						case '1':
							echo 'No Response';
							if (isset($r['UserReferrals']['hasPurchase']) && $r['UserReferrals']['hasPurchase'] == 1) {
								echo ' (registered and purchased)';
							} else if (isset($r['UserReferrals']['isRegistered']) && $r['UserReferrals']['isRegistered'] == 1) {
								echo ' (registered)';
							}
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
					if ($r['UserReferrals']['statusTypeId'] == 1 && $r['UserReferrals']['hasPurchase'] == 1) {
						echo '<a href="/userReferrals/completereferral/' . $r['UserReferrals']['id'] . '/3">Apply Credit</a>';
					}
				?>
			</td>
		</tr>
		
	<?php endforeach; ?>
	
	</table>
</div>

