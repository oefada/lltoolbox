<?php $this->set('hideSidebar', true); ?>
<div class="creditTrackings form">
<script>
jQuery(document).ready(function($) {
	$("#CreditTrackingCreditTrackingTypeId").change(function() {
		if ($(this).val() == 2) {
			var s=prompt("Please enter ticketID");
			if (s != null) {
				window.location.href = "/tickets/"+s+"/ticket_refunds/add?cof";
			}
		}
	});
})
</script>
<?php echo $form->create('CreditTracking');?>
	<fieldset>
 		<legend><?php __('Manually Add Credit');?></legend>
	<?php
		echo $form->input('creditTrackingTypeId',array('default' => (isset($this->params['named']['creditTrackingTypeId']) ? $this->params['named']['creditTrackingTypeId'] : "")));
		echo $form->input('userId');
		echo $form->input('amount',array('after' => ' <strong>Enter a negative amount to adjust balance</strong>'));
		if (isset($this->params['named']['ticketId'])) {
			echo $form->input('ticketId',array('default' => $this->params['named']['ticketId']));
		}
//		echo $form->input('balance');
		echo $form->input('notes');
//		echo $form->input('datetime');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List All Credits', true), array('action'=>'index'));?></li>
	</ul>
</div>
