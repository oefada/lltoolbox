<script language="javascript">
<!--
function confirmSubmit()
{
	var agree = confirm("Are you sure you want to continue?");
	if (agree)
		return true ;
	else
		return false ;
	}
// -->
</script>

<div class="trackDetails form">
<fieldset>
	 		<legend><?php __('Add TrackDetail');?></legend>
	 		
	 		<?php if ($track) : ?>
		 		<h2><?=$revenueModels[$track['revenueModelId']];?> - <?=$track['trackName'];?></h2>
		 		<table cellspacing="0" cellpadding="0" border="1">
		 			<tr>
		 				<th style="text-align:center;">Track Id</th>
		 				<th style="text-align:center;">Loa Id</th>
		 				<th style="text-align:center;">Track Name</th>
		 				<th style="text-align:center;">Keep Percentage</th>
		 				<th style="text-align:center;">Pending</th>
		 				<th style="text-align:center;">Collected</th>
		 				<th style="text-align:center;">X for Y</th>
		 				<th style="text-align:center;">Apply to Membership</th>
		 			</tr>
		 			<tr>
		 				<td style="text-align:center;"><?=$track['trackId'];?></td>
		 				<td style="text-align:center;"><a href="/loas/edit/<?=$track['loaId'];?>" target="_BLANK"><?=$track['loaId'];?></a></td>
		 				<td style="text-align:center;"><?=$track['trackName'];?></td>
		 				<td style="text-align:center;"><?=$track['keepPercentage'];?>%</td>
		 				<td style="text-align:center;"><?=$number->currency($track['pending']);?></td>
		 				<td style="text-align:center;"><?=$number->currency($track['collected']);?></td>
		 				<td style="text-align:center;"><?php echo ($track['revenueModelId'] > 1) ? $track['x'] . ' for ' . $track['y'] : 'N/A' ;?></td>
		 				<td style="text-align:center;"><?php echo ($track['applyToMembershipBal']) ? 'Yes' : 'No' ;?></td>
		 			</tr>
		 		</table>
		 		<br />
		 	<?php endif;?>
	 		
	 		<?php if ($trackDetails) : ?>
	 			<h2>Track Detail Records</h2>
	 			<table cellspacing="0" cellpadding="0" border="1">
		 			<tr>
		 				<th style="text-align:center;">Track Detail Id</th>
		 				<th style="text-align:center;">Ticket Id</th>
		 				<th style="text-align:center;">Ticket Amount</th>
		 				<th style="text-align:center;">Allocated Amount</th>
		 				<th style="text-align:center;">Cycle</th>
		 				<th style="text-align:center;">Iteration</th>
		 				<th style="text-align:center;">Amount Kept</th>
		 				<th style="text-align:center;">Amount Remitted</th>
		 				<th style="text-align:center;">xy Running Total</th>
		 				<th style="text-align:center;">xy Average</th>
		 				<th style="text-align:center;">Keep Balance Due</th>
		 				<th style="text-align:center;">Initials</th>
		 			</tr>
		 			<?php foreach ($trackDetails as $k => $v) : ?>
		 			<tr>
	 					<td style="text-align:center;"><?=$v['trackDetail']['trackDetailId'];?></td>
	 					<td style="text-align:center;"><?=$v['trackDetail']['ticketId'];?></td>
	 					<td style="text-align:center;"><?=$number->currency($v['trackDetail']['ticketAmount']);?></td>
	 					<td style="text-align:center;"><?=$number->currency($v['trackDetail']['allocatedAmount']);?></td>
	 					<td style="text-align:center;"><?=$v['trackDetail']['cycle'];?></td>
	 					<td style="text-align:center;"><?=$v['trackDetail']['iteration'];?></td>
	 					<td style="text-align:center;"><?=$number->currency($v['trackDetail']['amountKept']);?></td>
	 					<td style="text-align:center;"><?=$number->currency($v['trackDetail']['amountRemitted']);?></td>
	 					<td style="text-align:center;"><?=$number->currency($v['trackDetail']['xyRunningTotal']);?></td>
	 					<td style="text-align:center;"><?=$number->currency($v['trackDetail']['xyAverage']);?></td>
	 					<td style="text-align:center;"><?=$number->currency($v['trackDetail']['keepBalDue']);?></td>
	 					<td style="text-align:center;"><?=$v['trackDetail']['initials'];?></td>
	 				</tr>
	 				<?php endforeach; ?>
	 			</table>
	 			<br />
	 		<?php endif; ?>
	 		
<?php if (!$track) : ?>

	<div style="font-weight:bold;">Please refer to the LOA tool to correctly set a track.</div>
	
<?php elseif ($trackDetailExists) : ?>
	
	<div style="font-weight:bold;">Ticket already allocated.</div>

<?php else: ?>

	<?php echo $form->create('TrackDetail', array('url' => "/tickets/$ticketId/trackDetails/add", 'onsubmit' => "return confirmSubmit()"));?>		
		<?php
			if ($track['revenueModelId'] == 1) {
				echo "<input type=\"hidden\" id=\"TrackUsingToolboxNonAuto\" name=\"data[Track][trackUsingToolboxNonAuto]\" value=\"1\" />";
				$checked = $track['applyToMembershipBal'] ? "checked=\"checked\"" : '';
				echo "<div style='padding:5px;'>";
				echo "Apply to membership balance: ";
				echo "<input type=\"checkbox\" id=\"TrackApplyToMembershipBal\" name=\"data[Track][applyToMembershipBal]\" $checked /><br /><br />";
				echo "</div>";
			}
			echo $form->input('trackId', array('readonly' => 'readonly'));
			echo $form->input('ticketId', array('readonly' => 'readonly'));
			echo $form->input('ticketAmount', array('readonly' => 'readonly'));
			echo $form->input('allocatedAmount', array('readonly' => 'readonly'));
			echo $form->input('cycle', array('readonly' => 'readonly'));
			echo $form->input('iteration', array('readonly' => 'readonly'));			
			echo $form->input('initials', array('readonly' => 'readonly'));
			
			if ($track['revenueModelId'] == 2) {
				echo $form->input('xyRunningTotal', array('readonly' => 'readonly'));
				echo $form->input('xyAverage', array('readonly' => 'readonly'));
				echo $form->input('keepBalDue');
			}
			echo $form->input('amountKept');
			echo $form->input('amountRemitted');
		?>		
	<?php echo $form->end('Submit');?>
	
<?php endif; ?>

</fieldset>
</div>

