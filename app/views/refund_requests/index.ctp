<div class="index">
<h2><?php __('Refund Requests');?></h2>

<div class='advancedSearch' style="width: 700px; position: relative;">
	<?= $form->create(null, array('action'=>'index')); ?>
	<fieldset>
		<div class="fieldRow">
			<?= $form->input('f_status', array('label' => 'Status', 'options' => $refundStatuses, 'empty' => '-- ')); ?>
		</div>
		<div class="fieldRow">
			<div class="range">
				<label style="padding-right: 60px;">Date Filter</label>
				<?= $form->input('f_Date.field', array('options'=> array('dateCreated'=>'Date Created', 'dateApproved'=>'Date Approved', 'dateCompleted'=>'Date Completed'), 'empty'=>'--', 'style'=>'', 'label'=>false, 'div'=>false)); ?>
				<?echo $datePicker->picker('f_Date.start', array('label' => ''))?>
				&nbsp;- 
				<?echo $datePicker->picker('f_Date.end', array('label' => ''))?>
				&nbsp;
				<a href="#" onclick='javascript: $("f_DateStart").value = "<?=date('Y-m-d')?>"; $("f_DateEnd").value = "<?=date('Y-m-d', strtotime('+1 day'))?>"; return false;'>Today</a> | 
				<a href="#" onclick='javascript: $("f_DateStart").value = "<?=date('Y-m-d', strtotime('-1 day'))?>"; $("f_DateEnd").value = "<?=date('Y-m-d')?>"; return false;'>Yesterday</a>
			</div>
		</div>
		<div class="fieldRow">
			<label>Download CSV</label>
			<?= $form->checkbox('csv', array('style'=>'width:10px;')); ?>
		</div>
		
		<div style="position:absolute; top: 90px; right: 20px;">
			<?php echo $form->submit('Search') ?>
		</div>
	</fieldset>
	</form>
	
</div>


<table cellpadding="0" cellspacing="0">
<tr>
	<th>Id</th>
    <th>Site</th>
    <th>Ticket Id</th>
	<th>Status</th>
	<th>Amount</th>
	<th>Date Created</th>
	<th>Created By</th>
	<th>Date Approved</th>
	<th>Approved By</th>
	<th>Date Completed</th>
	<th>Completed By</th>
	<th>Refund / COF</th>
	<th class="actions">Actions</th>
</tr>
<?php
$i = 0;
foreach ($refundRequests as $rq):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td><?php echo $rq['RefundInfo']['refundRequestId']; ?></td>
        <td><?php 
            if ($rq['RefundInfo']['siteId'] == 2) {
                echo 'FG';
            } else {
                if ($rq['RefundInfo']['tldId'] == 2) {
                    echo 'LL UK';
                } else {
                    echo 'LL';
                }
            }
            ?>
        </td>
        <td><a href="/tickets/view/<?php echo $rq['RefundInfo']['ticketId']; ?>"><?php echo $rq['RefundInfo']['ticketId']; ?></a></td>
		<td><?php echo $rq['RefundInfo']['description']; ?></td>
		<td align="right">$<?php echo number_format($rq['RefundInfo']['refundTotal'], 2); ?>&nbsp;</td>
		<td><?php echo $rq['RefundInfo']['dateCreated']; ?></td>
		<td><?php echo $rq['RefundInfo']['createdBy']; ?></td>
		<td><?php echo $rq['RefundInfo']['dateApproved']; ?></td>
		<td><?php echo $rq['RefundInfo']['approvedBy']; ?></td>
		<td><?php echo $rq['RefundInfo']['dateCompleted']; ?></td>
		<td><?php echo $rq['RefundInfo']['completedBy']; ?></td>
		<td style="text-align:center;">
		
			<?
			if ($rq['RefundInfo']['refundOrCOF']) {	
				echo $refundOrCOFList[$rq['RefundInfo']['refundOrCOF']]; 
			}
			?>
		
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $rq['RefundInfo']['refundRequestId'])); ?>
			<? if ($rq['RefundInfo']['refundRequestStatusId'] == 1 || ($rq['RefundInfo']['refundRequestStatusId'] == 2 && $allowApprovedDelete)) { ?>
				<?php echo $html->link(__('Delete', true), array('action'=>'delete', $rq['RefundInfo']['refundRequestId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $rq['RefundInfo']['refundRequestId'])); ?>
			<? } ?>
			<? if ($rq['RefundInfo']['refundRequestStatusId'] != 3) { ?>
				<?php echo $html->link(__('Edit', true), array('action'=>'edit', $rq['RefundInfo']['refundRequestId'])); ?>
			<? } ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>

