<div class="index">
<h2><?php __('Refund Requests');?></h2>

<div class='advancedSearch' style="width: 600px; position: relative;">
	<?= $form->create(null, array('action'=>'index')); ?>
	<fieldset>
		<div class="fieldRow">
			<?= $form->input('f_status', array('label' => 'Status', 'options' => $refundStatuses, 'empty' => '-- ')); ?>
		</div>
		<div class="fieldRow">
			<label>Date Created</label>
			<div class="range">
				<?echo $datePicker->picker('f_createDate.start', array('label' => ''))?>
				&nbsp;- 
				<?echo $datePicker->picker('f_createDate.end', array('label' => ''))?>
			</div>
		</div>
		<div class="fieldRow">
			<label>Download CSV</label>
			<?= $form->checkbox('csv', array('style'=>'width:10px;')); ?>
		</div>
		
		<div style="position:absolute; top: 70px; right: 20px;">
			<?php echo $form->submit('Search') ?>
		</div>
	</fieldset>
	</form>
	
</div>


<table cellpadding="0" cellspacing="0">
<tr>
	<th>Id</th>
	<th>Ticket Id</th>
	<th>Status</th>
	<th>Date Created</th>
	<th>Created By</th>
	<th>Date Approved</th>
	<th>Approved By</th>
	<th>Date Completed</th>
	<th>Completed By</th>
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
		<td><?php echo $rq['RefundInfo']['ticketId']; ?></td>
		<td><?php echo $rq['RefundInfo']['description']; ?></td>
		<td><?php echo $rq['RefundInfo']['dateCreated']; ?></td>
		<td><?php echo $rq['RefundInfo']['createdBy']; ?></td>
		<td><?php echo $rq['RefundInfo']['dateApproved']; ?></td>
		<td><?php echo $rq['RefundInfo']['approvedBy']; ?></td>
		<td><?php echo $rq['RefundInfo']['dateCompleted']; ?></td>
		<td><?php echo $rq['RefundInfo']['completedBy']; ?></td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $rq['RefundInfo']['refundRequestId'])); ?>
			<? if ($rq['RefundInfo']['refundRequestStatusId'] == 1) { ?>
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

