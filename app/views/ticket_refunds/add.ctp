<div class="ticketRefunds form">
    <?php $session->flash();
	$session->flash('error');
	?>
<?php 

if (isset($this->params['url']['cof'])) {
	echo $form->create(null, array('url' => array('controller' => 'tickets/' . $this->params['ticketId'], 'action' => 'ticket_refunds/add?cof')));
} else {
	echo $ajax->form('add', 'post', array('url' => "/tickets/{$this->params['ticketId']}/ticket_refunds/add", 'update' => 'MB_content', 'model' => 'TicketRefund', 'complete' => 'Modalbox.activate(); closeModalbox()', 'before' => 'Modalbox.deactivate();', 'indicator' => 'spinner'));
}
?>
	<fieldset>
 		<legend><?php __('Add TicketRefund');?></legend>
	<?php
		echo $form->input('ticketRefundTypeId');
		echo $form->input('refundReasonId');
		echo $form->input('ticketId', array('readonly' => 'readonly'));
		echo $form->input('dateRequested');
		echo $form->input('refundEntire', array('type' => 'checkbox','label' => 'Refund entire ticket?<br><span class="smallText">(Sets status to refunded)</span>','checked' => 'checked'));
		echo $form->input('amountRefunded');
		echo '<div class="input text"><label>Refund Amount for Credit On File (if applicable)</label><input id="cofAmount" name="data[cofAmount]" type="text" maxlength="50" value=""/></div>';
		echo $form->input('refundNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>

<?php
if (isset($closeModalbox) && $closeModalbox) echo "<div id='closeModalbox'></div>";
?>
