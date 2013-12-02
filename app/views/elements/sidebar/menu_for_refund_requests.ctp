<ul class="tree">
	<li><?php echo $html->link(__('List Refund Requests', true), array('action'=>'index'));?></li>

	<? if (isset($displayRefundRequestEditLink) && $displayRefundRequestEditLink) { ?>
		<li>&nbsp;</li>
		<li><?php echo $html->link(__('Edit this Request', true), array('action'=>'edit', $this->data['RefundRequest']['refundRequestId']));?></li>
	<? } ?>

	<? if (isset($displayRefundRequestApproveLink) && $displayRefundRequestApproveLink) { ?>
		<li>&nbsp;</li>
		<li><?php echo $html->link(__('Approve this Request', true), array('action'=>'setApproved', $this->data['RefundRequest']['refundRequestId']));?></li>
	<? } ?>

	<? if (isset($displayRefundRequestCompleteLink) && $displayRefundRequestCompleteLink) { ?>
		<li>&nbsp;</li>
		<li><?php echo $html->link(__('Complete this Request', true), array('action'=>'setComplete', $this->data['RefundRequest']['refundRequestId']));?></li>
	<? } ?>

</ul>

