
<?php $this->pageTitle = 'Referrals'; ?>
<script type="text/javascript">
	jQuery(function($) {
		$("#linkAdditional").click(function() {
			$("#linkAdditionalForm").show();
		})
		
		$("#linkAdditionalForm input[type=button]").click(function() {
			var linkid = $("#linkUserId").val();
			$.get("/users/linkreferral/<?= $this->params['pass'][0] ?>", { linkid: linkid }, function(data) {
				data = $.parseJSON(data);
				
				if (data.msg == "OK") {
					alert("User linked successfully!");
					location.reload(true);
				} else if (data.msg == "ALREADY") {
					alert("This user has already been referred by another user (userID: "+data.userId+")");
				}
			});
		});
	});
</script>
<h2>Referrals sent by user</h2>
<h3><a href="#" id="linkAdditional">Link Additional User Account</a> - This will allow you to link a user that was referred by this user, but wasn't properly invited.</h3>
<div id="linkAdditionalForm">
	<label for="additionalUser">Please enter user ID to link: </label><input type="text" name="additionalUser" id="linkUserId"><input type="button" value="Link User">
</div>
<p>Apply credit to <?=$user['User']['email'];?> for a purchase made by someone they referred. Also applies credit to person referred for registering through a referral.</p>

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
				<? if ($r['UserReferrals']['isRegistered'] == 1): ?><a href="/users/edit/<?php echo $r['User']['userId']; ?>"><?php echo $r['User']['userId']; ?> - <?php echo $r['UserReferrals']['referredEmail']; ?></a><?else:?>
					<?php echo $r['UserReferrals']['referredEmail']; ?>
					<?endif; ?>
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

					if (isset($r['UserReferrals']['hasPurchase']) && $r['UserReferrals']['hasPurchase'] == 1) {
						echo ' (registered and purchased)';
					} else if (isset($r['UserReferrals']['isRegistered']) && $r['UserReferrals']['isRegistered'] == 1) {
						echo ' (registered and no completed purchases)';
					}
				?>
			</td>
			<td>
				<?php 
					if ($r['UserReferrals']['statusTypeId'] < 3 && $r['UserReferrals']['hasPurchase'] == 1) {
						echo '<a href="/userReferrals/completereferral/' . $r['UserReferrals']['id'] . '/3/'.$r['UserReferrals']['referrerUserId'].'">Apply Credit</a>';
					}
				?>
			</td>
		</tr>
		
	<?php endforeach; ?>
	
	</table>
</div>

