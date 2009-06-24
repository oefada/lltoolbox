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

	 		<?php if ($loa) : ?>
		 		<h2>LOA Information</h2>
		 		<table cellspacing="0" cellpadding="0" border="1">
		 			<tr>
		 				<th style="text-align:center;">Loa Id</th>
		 				<th style="text-align:center;">Cash</th>
		 				<th style="text-align:center;">Membership Fee</th>
		 				<th style="text-align:center;">Membership Balance</th>
		 				<th style="text-align:center;">Loa Value</th>
		 				<th style="text-align:center;">Total Kept</th>
		 				<th style="text-align:center;">Total Remitted</th>
		 				<th style="text-align:center;">Upgraded</th>
		 				<th style="text-align:center;">Upgraded Date</th>
		 			</tr>
		 			<tr>
		 				<td style="text-align:center;"><a href="/loas/edit/<?=$loa['Loa']['loaId'];?>" target="_BLANK"><?=$loa['Loa']['loaId'];?></a></td>
						<td style="text-align:center;"><?=$number->currency($loa['Loa']['cash']);?></td>
						<td style="text-align:center;"><?=$number->currency($loa['Loa']['membershipFee']);?></td>
						<td style="text-align:center;"><?=$number->currency($loa['Loa']['membershipBalance']);?></td>
						<td style="text-align:center;"><?=$number->currency($loa['Loa']['loaValue']);?></td>
						<td style="text-align:center;"><?=$number->currency($loa['Loa']['totalKept']);?></td>
						<td style="text-align:center;"><?=$number->currency($loa['Loa']['totalRemitted']);?></td>
						<td style="text-align:center;"><?php echo $loa['Loa']['upgraded'] ? 'Yes' : 'No' ;?></td>
						<td style="text-align:center;"><?=$loa['Loa']['upgradeDate'];?></td>
		 			</tr>
		 		</table>
		 		<br />
		 	<?php endif;?>
	 		
	 		<?php if ($track) : ?>
		 		<h2><?=$revenueModels[$track['revenueModelId']];?> - <?=$track['trackName'];?></h2>
		 		<table cellspacing="0" cellpadding="0" border="1">
		 			<tr>
		 				<th style="text-align:center;">Track Id</th>
		 				<th style="text-align:center;">Track Name</th>
		 				<th style="text-align:center;">Keep Percentage</th>
		 				<th style="text-align:center;">Pending</th>
		 				<th style="text-align:center;">Collected</th>
		 				<th style="text-align:center;">X for Y</th>
		 				<th style="text-align:center;">Apply to Membership</th>
		 			</tr>
		 			<tr>
		 				<td style="text-align:center;"><?=$track['trackId'];?></td>
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
		 				<th style="text-align:center;">Upgraded Balance Due</th>
		 				<th style="text-align:center;">Initials</th>
		 			</tr>
		 			<?php foreach ($trackDetails as $k => $v) : ?>
		 			<tr <?php if ($v['trackDetail']['ticketId'] == $ticketId) { echo 'class="altrow"';} ?>>
	 					<td style="text-align:center;"><a href="/tickets/<?=$v['trackDetail']['ticketId'];?>/trackDetails/edit/<?=$v['trackDetail']['trackDetailId'];?>"><?=$v['trackDetail']['trackDetailId'];?></a></td>
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

